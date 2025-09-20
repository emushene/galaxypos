<?php

namespace App\Http\Controllers;

use App\Models\ZReport;
use App\Models\Warehouse;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Returns;
use App\Models\Payment;
use App\Models\ProductSale;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ZReportController extends Controller
{
    public function index()
    {
        $z_reports = ZReport::with('user', 'warehouse')->latest()->paginate(10);
        return view('backend.z_report.index', compact('z_reports'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('is_active', true)->get();
        return view('backend.z_report.create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);

        $warehouse_id = $request->warehouse_id;
        $user_id = Auth::id();

        // Determine the date range for the report
        $last_z_report = ZReport::where('warehouse_id', $warehouse_id)->latest('end_date')->first();
        $start_date = $last_z_report ? $last_z_report->end_date : Carbon::parse('2000-01-01');
        $end_date = Carbon::now();

        // Calculate summaries
        $total_sales = Sale::where('warehouse_id', $warehouse_id)->whereBetween('created_at', [$start_date, $end_date])->sum('grand_total');
        $total_purchases = Purchase::where('warehouse_id', $warehouse_id)->whereBetween('created_at', [$start_date, $end_date])->sum('grand_total');
        $total_expenses = Expense::where('warehouse_id', $warehouse_id)->whereBetween('created_at', [$start_date, $end_date])->sum('amount');
        $total_returns = Returns::where('warehouse_id', $warehouse_id)->whereBetween('created_at', [$start_date, $end_date])->sum('grand_total');

        $payments = Payment::whereBetween('created_at', [$start_date, $end_date])
                            ->whereHas('sale', function($q) use ($warehouse_id) {
                                $q->where('warehouse_id', $warehouse_id);
                            })
                            ->get();
        
        $total_payments = $payments->sum('amount');
        $payment_summary = $payments->groupBy('paying_method')->map(function ($group) {
            return $group->sum('amount');
        });

        // Sales by category
        $sales_by_category = ProductSale::whereBetween('product_sales.created_at', [$start_date, $end_date])
            ->join('products', 'product_sales.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereIn('product_sales.sale_id', Sale::where('warehouse_id', $warehouse_id)->whereBetween('created_at', [$start_date, $end_date])->pluck('id'))
            ->select('categories.name as category_name', DB::raw('SUM(product_sales.total) as total_sales'))
            ->groupBy('categories.name')
            ->pluck('total_sales', 'category_name');

        // Create the Z Report
        $z_report = ZReport::create([
            'user_id' => $user_id,
            'warehouse_id' => $warehouse_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'total_sales' => $total_sales,
            'total_purchases' => $total_purchases,
            'total_expenses' => $total_expenses,
            'total_returns' => $total_returns,
            'total_payments' => $total_payments,
            'payment_summary' => $payment_summary,
            'sales_by_category' => $sales_by_category,
        ]);

        return redirect()->route('z_reports.show', $z_report->id)->with('message', 'Z Report generated successfully!');
    }

    public function show(ZReport $zReport)
    {
        $zReport->load('user', 'warehouse');
        $general_setting = GeneralSetting::latest()->first();
        return view('backend.z_report.show', compact('zReport', 'general_setting'));
    }
}
