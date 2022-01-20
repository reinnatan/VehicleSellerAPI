<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use App\Auth\SecureToken;

class JwtMiddleware extends BaseMiddleware
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

        //echo "From Middleware : ".$request->header('Authorization');
        try {
            $user = SecureToken::verifytoken($request->header('Authorization'));
        } catch (Exception $e) {
            //echo $e->getMessage();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
        return $next($request);
    }
}