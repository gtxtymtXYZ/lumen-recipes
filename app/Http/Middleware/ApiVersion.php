<?php

namespace App\Http\Middleware;

use Closure;

class ApiVersion
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
        $header = sprintf('application/vnd.recipes.%s+json', env('API_VERSION', 'v1'));

        return $next($request)
            ->withHeaders([
                'Content-Type' => $header
            ]);
    }
}
