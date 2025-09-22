<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashRegister;
use App\Models\Sale;
use App\Models\Account;
use App\Models\Payment;
use App\Models\Returns;
use App\Models\GeneralSetting;
use App\Models\MoneyTransfer;
use App\Models\Expense;
use Auth;

class CashRegisterController extends Controller
{
	public function index()
	{
		if(Auth::user()->role_id <= 2) {
            $lims_cash_register_all = CashRegister::with('user', 'warehouse')->get();
            $general_setting = GeneralSetting::latest()->first();
			return view('backend.cash_register.index', compact('lims_cash_register_all', 'general_setting'));
		}
		else
			return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
	}
	public function store(Request $request)
	{
		$data = $request->all();
		$data['status'] = true;
		$data['user_id'] = Auth::id();
		CashRegister::create($data);
		return redirect()->back()->with('message', 'Cash register created successfully');
	}

	public function getDetails($id)
	{
		$cash_register_data = CashRegister::find($id);
        if(!$cash_register_data)
            return response()->json(['error' => 'Cash register not found'], 404);

		return $this->_getRegisterDetails($cash_register_data);
	}

	public function showDetails($warehouse_id)
	{
		$cash_register_data = CashRegister::where([
    		['user_id', Auth::id()],
    		['warehouse_id', $warehouse_id],
    		['status', true]
    	])->first();

        if(!$cash_register_data)
            return response()->json(['error' => 'Active cash register not found for this user and warehouse'], 404);

		return $this->_getRegisterDetails($cash_register_data);
	}

	public function close(Request $request)
	{
		$cash_register_data = CashRegister::find($request->input('cash_register_id'));
        if (!$cash_register_data) {
            return redirect()->back()->with('not_permitted', 'Cash register not found!');
        }
		$cash_register_data->status = false;
		$cash_register_data->save();
		return redirect()->back()->with('message', 'Cash register closed successfully');
	}

    private function _getRegisterDetails(CashRegister $cash_register_data)
    {
        $data['cash_in_hand'] = $cash_register_data->cash_in_hand;
        $data['total_sale_amount'] = Sale::where([
                                        ['cash_register_id', $cash_register_data->id],
                                        ['sale_status', 1]
                                    ])->sum('grand_total');

        $payments = Payment::where('cash_register_id', $cash_register_data->id)->get();

        $data['total_payment'] = $payments->sum('amount');
        $data['cash_payment'] = $payments->where('paying_method', 'Cash')->sum('amount');
        $data['credit_card_payment'] = $payments->where('paying_method', 'Credit Card')->sum('amount');
        $data['gift_card_payment'] = $payments->where('paying_method', 'Gift Card')->sum('amount');
        $data['deposit_payment'] = $payments->where('paying_method', 'Deposit')->sum('amount');
        $data['cheque_payment'] = $payments->where('paying_method', 'Cheque')->sum('amount');
        $data['paypal_payment'] = $payments->where('paying_method', 'Paypal')->sum('amount');

        $data['total_sale_return'] = Returns::where('cash_register_id', $cash_register_data->id)->sum('grand_total');
        $data['total_expense'] = Expense::where('cash_register_id', $cash_register_data->id)->sum('amount');

        $default_account = Account::where('is_default', true)->first();
        $data['total_cash_drop'] = 0;
        if ($default_account) {
            $data['total_cash_drop'] = MoneyTransfer::where('cash_register_id', $cash_register_data->id)
                                            ->where('from_account_id', $default_account->id)
                                            ->sum('amount');
        }

        // Note: total_cash includes all payment methods, not just cash.
        // If you want to calculate only cash, you should use $data['cash_payment']
        // For example: $data['total_cash'] = $data['cash_in_hand'] + $data['cash_payment'] - ($data['total_sale_return'] + $data['total_expense'] + $data['total_cash_drop']);
        $data['total_cash'] = $data['cash_in_hand'] + $data['total_payment'] - ($data['total_sale_return'] + $data['total_expense'] + $data['total_cash_drop']);
        $data['status'] = $cash_register_data->status;
        $data['id'] = $cash_register_data->id;

        return $data;
    }

    public function checkAvailability($warehouse_id)
    {
        $isOpen = CashRegister::where([
            ['user_id', Auth::id()],
            ['warehouse_id', $warehouse_id],
            ['status', true]
        ])->exists();

        // The frontend expects a string 'true' or 'false'.
        // For better practice, consider returning a JSON boolean and updating the frontend check.
        // Example: return response()->json(['available' => $isOpen]);
        if($isOpen)
            return 'true';
        else
            return 'false';
    }
}
