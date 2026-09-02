<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DepartmentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Resolves the logged-in admin's assigned department (if any) from the
     * `department_id` column on `users` and makes it available to the rest
     * of the request. This middleware never blocks access — an admin with
     * no department assigned yet still logs in and uses the dashboard
     * normally; pages that list/filter alumni by department decide what to
     * show when this is null.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $departmentId = null;

        // Super-admins aren't scoped to a single department.
        if ($user && method_exists($user, 'hasRole') && ! $user->hasRole('super-admin')) {
            $departmentId = $user->department_id ?? null;
        }

        $request->attributes->set('admin_department_id', $departmentId);
        app()->instance('admin_department_id', $departmentId);

        return $next($request);
    }
}
