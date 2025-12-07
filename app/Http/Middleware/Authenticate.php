<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Closure;
use App\Helpers\ApiFormatter;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Exception;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        // Cek Token di Header
        $header = $request->header('Authorization');
        if (!$header) {
            // PERBAIKAN: Langsung return ApiFormatter tanpa response()->json()
            return ApiFormatter::createJson(401, 'Authorization header not provided', null);
        }

        try {
            // Verifikasi Token
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return ApiFormatter::createJson(401, 'Unauthorized', null);
            }
        } catch (TokenExpiredException $e) {
            return ApiFormatter::createJson(401, 'Token has expired', null);
        } catch (TokenInvalidException $e) {
            return ApiFormatter::createJson(401, 'Token is invalid', null);
        } catch (TokenBlacklistedException $e) {
            return ApiFormatter::createJson(401, 'Token has been blacklisted', null);
        } catch (Exception $e) {
            return ApiFormatter::createJson(401, 'Token could not be parsed', null);
        }

        return $next($request);
    }

    protected function redirectTo($request)
    {
        return null;
    }
}