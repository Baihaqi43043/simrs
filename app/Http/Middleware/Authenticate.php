<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        // Untuk API, jangan redirect - return null
        if ($request->expectsJson() || $request->is('api/*')) {
            return null;
        }

        // Untuk web, redirect ke homepage atau create simple login route
        return '/';
    }
}
