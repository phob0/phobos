<?php

namespace Phobos\Framework\App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Crypt;

class ExtractApiTokenFromCookie
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
        if ($request->headers->get('Authorization')) {
            // already have a token
        } else if ($token = $request->get('api_token')) {
            // pass token through URL
            $request->headers->set('Authorization', 'Bearer ' . $token);
        } else if ($request->cookie('app_auth')) { // ignore any Authorization tokens
            // retrieve token from cookie
            $request->headers->set('Authorization', 'Bearer ' . $request->cookie('app_auth'));
        }

        return $next($request);
    }
}
