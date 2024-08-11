<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

class DisableCsrf
{
    public function handle($request, Closure $next)
    {
        (new VerifyCsrfToken(app(), app('encrypter')))->handle($request, function () use ($request, $next) {
            return $next($request);
        });

        return $next($request);
    }
}
