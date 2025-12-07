<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Helpers\ApiFormatter;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;
use Exception;

class AuthController extends Controller
{
    // 1. LOGIN
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                return ApiFormatter::createJson(400, 'Bad Request', $validator->errors()->all());
            }

            $credentials = $request->only('email', 'password');

            // Cek User manual (sesuai modul)
            $user = User::where('email', $request->email)->first();
            if (!$user || !Hash::check($request->password, $user->password)) {
                return ApiFormatter::createJson(401, 'Unauthorized: Email or Password incorrect');
            }

            // Generate Token
            if (!$token = JWTAuth::fromUser($user)) {
                return ApiFormatter::createJson(500, 'Could not create token');
            }

            return $this->respondWithToken($token, 'Login Successful');

        } catch (Exception $e) {
            return ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage());
        }
    }

    // 2. ME (Cek Token milik siapa)
    public function me()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            return ApiFormatter::createJson(200, 'Authenticated User', $user);
        } catch (Exception $e) {
            return ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage());
        }
    }

    // 3. REFRESH TOKEN
    public function refresh()
    {
        try {
            $newToken = JWTAuth::refresh(JWTAuth::getToken());
            return $this->respondWithToken($newToken, 'Token Refreshed');
        } catch (Exception $e) {
            return ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage());
        }
    }

    // 4. LOGOUT
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return ApiFormatter::createJson(200, 'Successfully logged out');
        } catch (Exception $e) {
            return ApiFormatter::createJson(500, 'Logout Failed', $e->getMessage());
        }
    }

    // Helper untuk format respon token
    protected function respondWithToken($token, $message)
    {
        return ApiFormatter::createJson(200, $message, [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }
}