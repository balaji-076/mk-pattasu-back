<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected readonly AuthService $authService
    ) {}

    // =========================================================================
    // POST /login
    // =========================================================================
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        try {
            $result = $this->authService->login($validated);

            return $this->successResponse('Login successful', $result);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    // =========================================================================
    // POST /send-otp
    // =========================================================================
    public function sendOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        try {
            $result = $this->authService->sendOtp($validated['email']);

            return $this->successResponse('OTP sent successfully', $result);
        } catch (\Exception $e) {
            if ($e->getCode() === 423) {
                return response()->json(json_decode($e->getMessage(), true), 423);
            }

            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    // =========================================================================
    // POST /verify-otp
    // =========================================================================
    public function verifyOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|digits:6',
        ]);

        try {
            $result = $this->authService->verifyOtp($validated['email'], $validated['otp']);

            return $this->successResponse('OTP verified successfully', $result);
        } catch (\Exception $e) {
            if ($e->getCode() === 423 || $e->getCode() === 422) {
                return response()->json(
                    json_decode($e->getMessage(), true) ?: ['message' => $e->getMessage()],
                    $e->getCode()
                );
            }

            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    // =========================================================================
    // POST /set-password
    // =========================================================================
    public function setPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id'  => 'required|exists:users,id',
            'password' => 'required|min:8',
        ]);

        try {
            $result = $this->authService->setPassword($validated);

            return $this->successResponse('Password set successfully', $result);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    // =========================================================================
    // POST /logout
    // =========================================================================
    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->logout($request);

            return $this->successResponse('Logged out successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    // =========================================================================
    // POST /register
    // =========================================================================
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        try {
            $result = $this->authService->register($validated);

            return $this->successResponse('Registration successful', $result, 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}