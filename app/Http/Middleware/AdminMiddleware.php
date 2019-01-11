<?php

namespace App\Http\Middleware;

use Closure;
use Session;

class AdminMiddleware
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
        if (!Session::has('Coffee_Admin_Logged_in')) {
            if($request->ajax() || $request->wantsJson()){
                return response('Unauthorized.',401);
            }else{
                return redirect('/admin');
            }
        }
        $response = $next($request);
        return $response->header('Cache-Control','nocache, no-store, max-age=0, must-revalidate')
            ->header('Pragma','no-cache')
            ->header('Expires','Sat, 01 Jan 1990 00:00:00 GMT');
    }
}
