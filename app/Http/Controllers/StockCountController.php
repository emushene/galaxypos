<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockCount;
use DB;
use Auth;
use Log;

class StockCountController extends Controller
{
    // Show all stock counts
    public function index()
    {
        $lims_warehouse_list = Warehouse::where('is_active', true)->get();
        $lims_brand_list = Brand::where('is_active', true)->get();
        $lims_category_list = Category::where('is_active', true)->get();
        $general_setting = DB::table('general_settings')->latest()->first();

        if (Auth::user()->role_id > 2 && $general_setting->staff_access == 'own') {
            $lims_stock_count_all = StockCount::where('user_id', Auth::id())
                ->orderBy('id', 'desc')
                ->paginate(15);
        } else {
            $lims_stock_count_all = StockCount::orderBy('id', 'desc')
                ->paginate(15);
        }

        return view('backend.stock_count.index', compact(
            'lims_warehouse_list',
            'lims_brand_list',
            'lims_category_list',
            'lims_stock_count_all',
            'general_setting'
        ));
    }

    // Create new stock count (generate CSV)
    public function store(Request $request)
    {
        $data = $request->all();

        $query = Product::join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')
            ->where('product_warehouse.warehouse_id', $data['warehouse_id'])
            ->where('products.is_active', true);

        if(!empty($data['category_id'])) $query->whereIn('products.category_id', $data['category_id']);
        if(!empty($data['brand_id'])) $query->whereIn('products.brand_id', $data['brand_id']);

        $products = $query->select('products.id','products.name','products.code','product_warehouse.imei_number','product_warehouse.qty')->get();

        if($products->isEmpty()){
            return redirect()->back()->with('not_permitted', 'No product found!');
        }

        // Prepare CSV
        $csvData = [['Product Name','Product Code','IMEI or Serial Numbers','Expected','Counted']];
        foreach($products as $product){
            $csvData[] = [
                $product->name,
                $product->code,
                str_replace(',', '/', $product->imei_number),
                $product->qty,
                ''
            ];
        }

        $filename = date('Ymd') . '-' . date('His') . ".csv";
        $file_path = public_path('stock_count/' . $filename);
        $file = fopen($file_path, 'w');
        foreach($csvData as $row){
            fputcsv($file, $row);
        }
        fclose($file);

        $data['user_id'] = Auth::id();
        $data['reference_no'] = 'scr-' . date("Ymd") . '-' . date("His");
        $data['initial_file'] = $filename;
        $data['is_adjusted'] = false;
        $data['category_id'] = isset($data['category_id']) ? implode(',', $data['category_id']) : null;
        $data['brand_id'] = isset($data['brand_id']) ? implode(',', $data['brand_id']) : null;

        StockCount::create($data);

        return redirect()->back()->with('message','Stock Count created successfully! Please download the initial file.');
    }

    // Finalize CSV upload
    public function finalize(Request $request)
    {
        $request->validate([
            'final_file' => 'required|mimes:csv,txt',
            'stock_count_id' => 'required|exists:stock_counts,id'
        ]);

        $stockCount = StockCount::find($request->stock_count_id);

        $fileName = date('Ymd') . '-' . date('His') . '.csv';
        $request->final_file->move(public_path('stock_count'), $fileName);

        $stockCount->update([
            'final_file' => $fileName,
            'note' => $request->note
        ]);

        return redirect()->back()->with('message','Stock Count finalized successfully!');
    }

    // Delete stock count
    public function destroy($id)
    {
        $stockCount = StockCount::find($id);
        if(!$stockCount) return response()->json(['error'=>'Stock count not found'],404);

        if($stockCount->initial_file && file_exists(public_path('stock_count/'.$stockCount->initial_file))){
            unlink(public_path('stock_count/'.$stockCount->initial_file));
        }
        if($stockCount->final_file && file_exists(public_path('stock_count/'.$stockCount->final_file))){
            unlink(public_path('stock_count/'.$stockCount->final_file));
        }

        $stockCount->delete();
        return response()->json(['success'=>'Stock count deleted successfully']);
    }

    // Get internal stock for inline adjustment
    public function internalStockCount(Request $request, $warehouse_id)
    {
        $query = Product::join('product_warehouse','products.id','=','product_warehouse.product_id')
            ->where('product_warehouse.warehouse_id',$warehouse_id)
            ->where('products.is_active',true);

        if(!empty($request->category_id)) $query->whereIn('products.category_id',$request->category_id);
        if(!empty($request->brand_id)) $query->whereIn('products.brand_id',$request->brand_id);

        $products = $query->select('products.id','products.name','products.code','product_warehouse.qty as expected')->get();

        return response()->json($products);
    }

    // Update counted quantities inline
    public function updateStockCount(Request $request, $id)
    {
        $stockCount = StockCount::find($id);
        if (!$stockCount) return response()->json(['error' => 'Stock count not found'], 404);

        $countedData = $request->input('counted_qty', []);
        if (empty($countedData)) return response()->json(['error' => 'No data provided'], 400);

        foreach ($countedData as $code => $counted) {
            $product = Product::where('code', $code)->first();
            if ($product) {
                DB::table('product_warehouse')
                    ->where('product_id', $product->id)
                    ->where('warehouse_id', $stockCount->warehouse_id)
                    ->update(['qty' => $counted]);

                Log::info("Stock updated: Product {$product->code}, Warehouse {$stockCount->warehouse_id}, Counted {$counted}");
            } else {
                Log::warning("Product code not found during stock adjustment: {$code}");
            }
        }

        $stockCount->is_adjusted = true;
        $stockCount->save();

        return response()->json(['success' => 'Stock count updated successfully']);
    }
}
