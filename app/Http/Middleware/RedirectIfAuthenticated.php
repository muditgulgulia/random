<?php

namespace App\Http\Middleware;

use App\ACME\Admin\AdminHelper;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated extends AdminHelper
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            if ($this->authenticateUser() == 1) {
                return redirect('admin/');
            }

            return redirect('login');
        }

        return $next($request);
    }

}
