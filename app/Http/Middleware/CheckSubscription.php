<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(Auth::check()){
            $user = Auth::user();
            if($user->userroles->role_name == 'SuperAdmin'){
            return $next($request);
            }

            if($user->store->is_trial && $user->store->created_at->addDays(14)->isPast() ){
                Auth::logout();
                return redirect('/expire-trial');
            }

            if(!$user->store->is_trial && ($user->store->is_locked || (strtotime($user->store->renewal_date) < time())) ){
                
                return redirect("/payment");
            }
        }

        return $next($request);
    }
}
