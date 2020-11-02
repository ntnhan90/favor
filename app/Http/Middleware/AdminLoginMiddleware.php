<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Session;

class AdminLoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {        
        if(Auth::check()){
            $user = Auth::user();
            if($user->role_id == 1 || $user->level == 0){
                return $next($request);
            }else{
                return redirect('/');
            }
        }
        else
        {            
            Auth::logout();
            return redirect('login');
            //return redirect('login');
        }
    }
}
