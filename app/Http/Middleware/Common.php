<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class Common
{
    public function handle(Request $request, Closure $next)
    {
        // 🔹 Force HTTPS if needed (disabled by default)
        /*
        if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) {
            URL::forceScheme('https');
        }
        */

        // 🔹 Load general settings with cache
        $general_setting = Cache::remember('general_setting', 60 * 60 * 24 * 365, function () {
            return DB::table('general_settings')->latest()->first();
        });

        $todayDate = date("Y-m-d");

        // 🔹 Check SaaS expiry (subscription model)
        if ($general_setting && $general_setting->expiry_date) {
            $expiry_date = date("Y-m-d", strtotime($general_setting->expiry_date));
            if ($todayDate > $expiry_date) {
                if (Auth::check()) {
                    Log::warning("Account expired for user", ['user_id' => Auth::id()]);
                    Auth::logout();
                }
                return redirect()->route('account.trial_expired');
            }
        }

        // 🔹 Handle user SaaS states
        if (Auth::check()) {
            $user = Auth::user();

            switch ($user->status ?? null) {
                case 'pending':
                    return redirect()->route('account.pending');
                case 'trial_expired':
                    return redirect()->route('account.trial_expired');
                case 'suspended':
                    return redirect()->route('account.suspended');
                case 'inactive':
                    return redirect()->route('account.inactive');
                case 'active': // ✅ allow normal access
                default:
                    break;
            }
        }

        // 🔹 Set language
        app()->setLocale($_COOKIE['language'] ?? 'en');

        // 🔹 Set theme
        View::share('theme', $_COOKIE['theme'] ?? 'light');

        // 🔹 Load currency from cache
        $currency = Cache::remember('currency', 60 * 60 * 24 * 365, function () {
            $settingData = DB::table('general_settings')->select('currency')->latest()->first();
            return \App\Models\Currency::find($settingData->currency ?? 1);
        });

        View::share('general_setting', $general_setting);
        View::share('currency', $currency);

        config([
            'staff_access'            => $general_setting->staff_access ?? 'all',
            'date_format'             => $general_setting->date_format ?? 'Y-m-d',
            'currency'                => $currency->code ?? 'USD',
            'currency_position'       => $general_setting->currency_position ?? 'left',
            'decimal'                 => $general_setting->decimal ?? 2,
            'is_zatca'                => $general_setting->is_zatca ?? false,
            'company_name'            => $general_setting->company_name ?? 'My SaaS',
            'vat_registration_number' => $general_setting->vat_registration_number ?? '',
            'without_stock'           => $general_setting->without_stock ?? false,
        ]);

        // 🔹 Alerts
        $alert_product = DB::table('products')
            ->where('is_active', true)
            ->whereColumn('alert_quantity', '>', 'qty')
            ->count();

        $dso_alert_product = DB::table('dso_alerts')
            ->select('number_of_products')
            ->whereDate('created_at', $todayDate)
            ->first();

        $dso_alert_product_no = $dso_alert_product->number_of_products ?? 0;

        View::share([
            'alert_product'       => $alert_product,
            'dso_alert_product_no'=> $dso_alert_product_no,
        ]);

        // 🔹 User roles & permissions (custom schema, no Spatie)
        if (Auth::check()) {
            $role = Cache::remember('user_role_' . Auth::id(), 60 * 60 * 24 * 365, function () {
                return DB::table('roles')->find(Auth::user()->role_id);
            });
            View::share('role', $role);

            $permission_list = Cache::remember('permissions', 60 * 60 * 24 * 365, function () {
                return DB::table('permissions')->get();
            });
            View::share('permission_list', $permission_list);

            $role_has_permissions = Cache::remember('role_has_permissions_' . Auth::id(), 60 * 60 * 24 * 365, function () {
                return DB::table('role_has_permissions')
                    ->where('role_id', Auth::user()->role_id)
                    ->get();
            });
            View::share('role_has_permissions', $role_has_permissions);

            $role_has_permissions_list = Cache::remember(
                'role_has_permissions_list_' . Auth::user()->role_id,
                60 * 60 * 24 * 365,
                function () {
                    return DB::table('permissions')
                        ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                        ->where('role_id', Auth::user()->role_id)
                        ->select('permissions.name')
                        ->get();
                }
            );
            View::share('role_has_permissions_list', $role_has_permissions_list);
        }

        // 🔹 Categories
        $categories_list = Cache::remember('category_list', 60 * 60 * 24 * 365, function () {
            return DB::table('categories')->where('is_active', true)->get();
        });
        View::share('categories_list', $categories_list);

        return $next($request);
    }
}
