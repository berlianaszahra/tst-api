<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\JWTGuard;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->error(
                    'Data tidak valid',
                    422,
                    $validator->errors()
                );
            }

            /** @var JWTGuard $guard */
            $guard = auth('api');

            $token = $guard->attempt($validator->validated());

            if (!$token) {
                return $this->error(
                    'Email atau password salah',
                    401
                );
            }

            return $this->success([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => $guard->factory()->getTTL() * 60,
            ], 'Login berhasil');

        } catch (QueryException $e) {
            Log::error('Gagal login: ' . $e->getMessage());

            return $this->error(
                'Terjadi kesalahan pada database',
                500
            );
        }
    }
}
