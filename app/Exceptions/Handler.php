<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

// --- BAGIAN 1: TAMBAHKAN INI DI ATAS ---
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\LogModel;
use App\Helpers\ApiFormatter;
// ---------------------------------------

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    // --- BAGIAN 2: TAMBAHKAN METHOD RENDER DI SINI (PALING BAWAH) ---
    public function render($request, Throwable $exception)
    {
        // Tangani Error 404 (Not Found)
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            
            $user = null;
            try {
                // Cek apakah ada token JWT (Login)
                $user = JWTAuth::parseToken()->authenticate();
            } catch (\Exception $e) {
                // Jika token invalid/tidak ada, user tetap null
                $user = null;
            }

            // Filter data sensitif sebelum dicatat
            $filteredRequest = ApiFormatter::filterSensitiveData($request->all());

            // Catat ke Log Database [cite: 223]
            LogModel::create([
                'user_id' => $user ? $user->id : null,
                'log_method' => $request->method(),
                'log_url' => $request->fullUrl(),
                'log_ip' => $request->ip(),
                'log_request' => json_encode($filteredRequest),
                'log_response' => json_encode(ApiFormatter::createJson(404, 'Not Found', 'Route not found.')),
            ]);

            // Kembalikan response JSON 404 custom
            return response()->json(ApiFormatter::createJson(404, 'Not Found', 'Route not found.'), 404);
        }

        return parent::render($request, $exception);
    }
    // ---------------------------------------------------------------
}