<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        if (isset($_COOKIE['language']))
            \App::setLocale($_COOKIE['language']);
        else
            \App::setLocale('en');

        $theme = $_COOKIE['theme'] ?? 'light';

        $general_setting = Cache::remember('general_setting', 60 * 60 * 24 * 365, function () {
            return DB::table('general_settings')->latest()->first();
        });

        if (!$general_setting) {
            DB::unprepared(file_get_contents('public/tenant_necessary.sql'));
            $general_setting = Cache::remember('general_setting', 60 * 60 * 24 * 365, function () {
                return DB::table('general_settings')->latest()->first();
            });
        }

        $numberOfUserAccount = \App\Models\User::where('is_active', true)->count();

        return view('backend.auth.login', compact('theme', 'general_setting', 'numberOfUserAccount'));
    }

    public function login(Request $request)
    {
        Log::info('Login attempt started', [
            'input' => $request->only(['name']),
            'ip' => $request->ip(),
        ]);

        $this->validate($request, [
            'name' => 'required',
            'password' => 'required',
        ]);

        $input = $request->all();
        $fieldType = filter_var($request->name, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        if (auth()->attempt([$fieldType => $input['name'], 'password' => $input['password']])) {
            $user = Auth::user();
            $role = Role::find($user->role_id);

            Log::info('Login success', [
                'user_id' => $user->id,
                'username' => $user->name,
                'role' => $role ? $role->name : null,
            ]);

            cookie()->queue(cookie('login_now', 1, 1440)); // 1 day

            if ($role && $role->name == 'Cashier') {
                Log::info('Redirecting user to /pos', ['user_id' => $user->id]);
                return redirect('pos');
            } else {
                Log::info('Redirecting user to /dashboard', ['user_id' => $user->id]);
                return redirect('/dashboard');
            }
        } else {
            Log::warning('Login failed', [
                'username' => $input['name'],
                'ip' => $request->ip(),
            ]);
            return redirect()->route('login')->with('error', 'Username And Password Are Wrong.');
        }
    }
}
