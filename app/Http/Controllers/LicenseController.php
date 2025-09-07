<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Keygen\Keygen;
use App\Models\License;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Purchase;
use App\Models\CashRegister;
use App\Models\Account;
use App\Models\Payment;
use App\Models\MailSetting;
use Illuminate\Validation\Rule;
use Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Mail;

class LicenseController extends Controller
{
    use \App\Traits\MailInfo;

    public function index()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('license')){
            $permissions = Role::findByName($role->name)->permissions;
            foreach ($permissions as $permission)
                $all_permission[] = $permission->name;
            if(empty($all_permission))
                $all_permission[] = 'dummy text';
            // $lims_license_all = License::where('is_active', true)->get();
            $lims_license_all = License::get();
            // dd($lims_license_all);
            return view('backend.license.index',compact('lims_license_all', 'all_permission'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function generateCode()
    {   
        
        $first = Keygen::numeric(5)->generate();
        $second = Keygen::numeric(5)->generate();
        $third = Keygen::numeric(5)->generate();
        $fourth = Keygen::numeric(5)->generate();
        $fifth = Keygen::numeric(5)->generate();
        $licenseCode = $first.'-'.$second.'-'.$third.'-'.$fourth.'-'.$fifth;
        return $licenseCode;
    }

    public function create()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('license')){
            $lims_customer_group_all = CustomerGroup::where('is_active',true)->get();
            return view('backend.license.create', compact('lims_customer_group_all'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'company_name' => [
                'max:255',
                    Rule::unique('licenses')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
            'email' => [
                'max:255',
                    Rule::unique('licenses')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
            'license_number' => [
                'max:255',
                    Rule::unique('licenses')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],            
        ]);
        
        $lims_license_data['name'] = $request->name;
        $lims_license_data['company_name'] = $request->company_name;
        $lims_license_data['license_number'] = $request->license_number;
        $lims_license_data['email'] = $request->email;
        $lims_license_data['phone_number'] = $request->phone_number;
        $lims_license_data['valid_start'] = $request->valid_start;
        $lims_license_data['valid_end'] = $request->valid_end;
        $lims_license_data['user_id'] = Auth::id();
        $lims_license_data['is_active'] = true;
        License::create($lims_license_data);

        $message = 'License';
        return redirect('license')->with('message', $message);
    }

    public function edit($id)
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('license')){
            $lims_license_data = License::where('id',$id)->first();
            return view('backend.license.edit',compact('lims_license_data'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'company_name' => [
                'max:255',
                    Rule::unique('licenses')->ignore($id)->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],

            'email' => [
                'max:255',
                    Rule::unique('licenses')->ignore($id)->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
            'license_number' => [
                'max:255',
                    Rule::unique('licenses')->ignore($id)->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
        ]);

        $lims_license_data = License::findOrFail($id);
        $lims_license_update['name'] = $request->name;
        $lims_license_update['company_name'] = $request->company_name;
        // $lims_license_update['license_number'] = $request->license_number;
        $lims_license_update['email'] = $request->email;
        $lims_license_update['phone_number'] = $request->phone_number;
        $lims_license_update['valid_start'] = $request->valid_start;
        $lims_license_update['valid_end'] = $request->valid_end;
        $lims_license_update['user_id'] = Auth::id();
        $lims_license_update['is_active'] = true;
        $lims_license_data->update($lims_license_update);

        return redirect('license')->with('message','Data updated successfully');
    }

    public function fetchDataLicense(Request $request,$data)
    {   
        $data['status'] ='Test';
        return response()->json($data,201);
        
        $data = $request->all();
        dd($data, $data);
        /*$path = base_path('track/fetch-data-upgrade.json');
        $data = null;
        if (File::exists($path)) {
            $json_file = File::get($path);
            $data = json_decode($json_file);
        }
        $data['status'] ='Test';
        return response()->json($data,201);*/
    }

    public function deleteBySelection(Request $request)
    {
        $license_id = $request['licenseIdArray'];
        foreach ($license_id as $id) {
            $lims_license_data = License::findOrFail($id);
            $lims_license_data->is_active = false;
            $lims_license_data->save();
            
        }
        return 'License InActive successfully !';
    }

    public function destroy($id)
    {
        $lims_license_data = License::findOrFail($id);
        $lims_license_data->is_active = false;
        $lims_license_data->save();

        return redirect('license')->with('not_permitted','License InActive successfully');
    }

}
