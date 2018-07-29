<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RequestRestriction
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    protected $limitPerTenMinutes = 15;

    protected $allowStatusCodes = [
        Response::HTTP_OK, Response::HTTP_CREATED, Response::HTTP_NO_CONTENT
    ];

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function handle(Request $request, Closure $next)
    {
        $key = 'restriction_'.$request->user()->id;
        $current = \Cache::get($key, $this->limitPerTenMinutes);

        if($current == 0) {
            return $this->response(
                response()
                    ->json([
                        'message' => 'Query limit reached.'
                    ], 429),
                $current
            );
        }

        /** @var Response $response */
        $response = $next($request);

        in_array($response->getStatusCode(), $this->allowStatusCodes) && \Cache::set($key, --$current, 10);

        return $this->response($response, $current);
    }

    /**
     * @param mixed $response
     * @param int $limit
     * @return mixed
     */
    private function response($response, int $limit)
    {
        return $response
            ->withHeaders([
                'Requests-Limit' => $limit
            ]);
    }
}
