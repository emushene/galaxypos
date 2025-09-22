<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\MoneyTransfer;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Auth;

class MoneyTransferController extends Controller
{
    public function index()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('money-transfer')){
            $lims_money_transfer_all = MoneyTransfer::get();
            $lims_account_list = Account::where('is_active', true)->get();
            return view('backend.money_transfer.index', compact('lims_money_transfer_all', 'lims_account_list'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $request->validate([
            'from_account_id' => 'required|integer|exists:accounts,id',
            'to_account_id' => 'required|integer|exists:accounts,id|different:from_account_id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $data['reference_no'] = 'mtr-' . date("Ymd") . '-'. date("his");
        $data['user_id'] = Auth::id();

        // If the transfer is from POS, associate it with the active cash register
        if(isset($data['pos_transfer'])) {
            $cash_register_data = CashRegister::where([
                ['user_id', $data['user_id']],
                ['warehouse_id', $data['warehouse_id']],
                ['status', true]
            ])->first();
            if($cash_register_data) {
                $data['cash_register_id'] = $cash_register_data->id;
            }
        }

        DB::beginTransaction();
        try {
            $lims_from_account_data = Account::find($data['from_account_id']);
            $lims_from_account_data->total_balance -= $data['amount'];
            $lims_from_account_data->save();

            $lims_to_account_data = Account::find($data['to_account_id']);
            $lims_to_account_data->total_balance += $data['amount'];
            $lims_to_account_data->save();

            MoneyTransfer::create($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('not_permitted', 'Sorry! An error occurred while transferring money.');
        }

        if(isset($data['pos_transfer'])) {
            return redirect()->route('sale.pos')->with('message', 'Money transferred successfully');
        }

        return redirect('money-transfers')->with('message', 'Money transfered successfully');
    }

    public function show(MoneyTransfer $moneyTransfer)
    {
        //
    }

    public function edit(MoneyTransfer $moneyTransfer)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        MoneyTransfer::find($data['id'])->update($data);
        return redirect()->back()->with('message', 'Money transfer updated successfully');
    }

    public function destroy($id)
    {
        MoneyTransfer::find($id)->delete();
        return redirect()->back()->with('not_permitted', 'Data deleted successfully');
    }
}
