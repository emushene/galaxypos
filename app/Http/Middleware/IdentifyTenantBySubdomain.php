<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class IdentifyTenantBySubdomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $subdomain = explode('.', $host)[0];

        // Find the tenant using the main database connection.
        $tenant = Tenant::where('subdomain', $subdomain)->first();

        if ($tenant) {
            // Dynamically set the database configuration for the tenant connection.
            Config::set('database.connections.tenant.database', $tenant->db_database);
            Config::set('database.connections.tenant.host', $tenant->db_host ?? env('DB_HOST', '127.0.0.1'));
            Config::set('database.connections.tenant.port', $tenant->db_port ?? env('DB_PORT', '3306'));
            Config::set('database.connections.tenant.username', $tenant->db_username ?? env('DB_USERNAME', 'forge'));
            Config::set('database.connections.tenant.password', $tenant->db_password ?? env('DB_PASSWORD', ''));

            // Purge the old connection and reconnect to ensure the new settings are used.
            DB::purge('tenant');

            // Set the default connection to the tenant's database.
            DB::setDefaultConnection('tenant');

            // Store the tenant instance for global access if needed.
            app()->instance('tenant', $tenant);
        } else {
            // Optional: Redirect or abort if the subdomain does not correspond to a valid tenant.
            // For now, we will allow the request to proceed using the main connection.
            // You might want to change this to abort(404) in production.
        }

        return $next($request);
    }
}
