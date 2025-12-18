<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Helpers\ApiFormatter;
use App\Models\LogModel;
use Exception;
use Throwable;

class LogAPI
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
{
    $user = null;
    try {
        $user = JWTAuth::parseToken()->authenticate();
    } catch (Exception $e) {
        $user = null;
    } // [cite: 108, 109, 110, 111, 115]

    $filteredRequest = ApiFormatter::filterSensitiveData($request->all()); // [cite: 116, 117]

    // Simpan Log Request Awal
    $log = LogModel::create([
        'user_id' => $user ? $user->id : null,
        'log_method' => $request->method(),
        'log_url' => $request->fullUrl(),
        'log_ip' => $request->ip(),
        'log_request' => json_encode($filteredRequest),
    ]); // [cite: 118, 121, 123, 125, 127, 128, 131, 132, 133, 134, 135]

    try {
        $response = $next($request);

        // Update Log dengan Response Sukses
        $log->update([
            'log_response' => $response->getContent()
        ]); // [cite: 140, 141, 142, 144]

        return $response;

    } catch (Throwable $e) {
        // Handle Error dan Update Log
        $errorResponse = ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage());
        $log->update([
            'log_response' => json_encode($errorResponse)
        ]);
        return response()->json($errorResponse, 500);
    } // [cite: 146, 147, 148, 155]
}
}
