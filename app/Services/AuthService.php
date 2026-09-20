<?php

namespace App\Services;

use App\Repositories\AuthRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        protected readonly AuthRepository $authRepository,
        protected readonly BrevoMailService $brevoMailService,
    ) {}

    public function login(array $data): array
    {
        $user = $this->authRepository->findUserByEmail($data['email']);

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new \Exception('Invalid credentials', 401);
        }

        $this->authRepository->deleteUserTokens($user);
        $token = $this->authRepository->createUserToken($user);

        return [
            'token' => $token,
            'user'  => $user,
        ];
    }

    public function sendOtp(string $email): array
    {
        $user = $this->authRepository->findUserByEmail($email);

        if (!$user) {
            throw new \Exception('User not found', 404);
        }

        $otpRow = $this->authRepository->getLatestOtp($user->id);

        if ($otpRow && $otpRow->locked_until && now()->lt($otpRow->locked_until)) {
            throw new \Exception(json_encode([
                'message'      => 'Your account is locked. Try again after 15 minutes.',
                'locked'       => true,
                'locked_until' => $otpRow->locked_until->timestamp,
            ]), 423);
        }

        $this->authRepository->deleteUserOtps($user->id);

        $otp = random_int(100000, 999999);
        $row = $this->authRepository->createOtp([
            'user_id'      => $user->id,
            'otp'          => Hash::make($otp),
            'expires_at'   => now()->addMinutes(2),
            'attempts'     => 0,
            'locked_until' => null,
        ]);

        $this->brevoMailService->send(
            $user->email,
            'Your OTP to Set Password',
            view('emails.admin-otp', ['otp' => $otp])->render()
        );

        return [
            'expires_at'   => $row->expires_at->timestamp,
            'attempts_left' => 5,
        ];
    }

    public function verifyOtp(string $email, string $otp): array
    {
        $user = $this->authRepository->findUserByEmail($email);

        if (!$user) {
            throw new \Exception('User not found', 404);
        }

        $otpRow = $this->authRepository->getLatestOtp($user->id);

        if (!$otpRow || now()->greaterThan($otpRow->expires_at)) {
            throw new \Exception('OTP expired. Please resend.', 422);
        }

        if ($otpRow->locked_until && now()->lessThan($otpRow->locked_until)) {
            throw new \Exception(json_encode([
                'locked'       => true,
                'locked_until' => $otpRow->locked_until->timestamp,
                'attempts_left' => 0,
                'message'      => 'OTP locked for 15 minutes',
            ]), 423);
        }

        if (!Hash::check($otp, $otpRow->otp)) {
            $otpRow = $this->authRepository->incrementAttempts($otpRow);

            throw new \Exception(json_encode([
                'message'      => $otpRow->locked_until ? 'OTP locked for 15 minutes' : 'Invalid OTP',
                'attempts_left' => max(0, 5 - $otpRow->attempts),
                'locked'       => (bool) $otpRow->locked_until,
                'locked_until' => $otpRow->locked_until?->timestamp,
            ]), $otpRow->locked_until ? 423 : 422);
        }

        $this->authRepository->deleteOtp($otpRow);

        return [
            'verified' => true,
            'user_id'  => $user->id,
        ];
    }

    public function setPassword(array $data): array
    {
        $user = $this->authRepository->findById($data['user_id']);

        $this->authRepository->updatePassword($user, Hash::make($data['password']));
        $this->authRepository->deleteUserTokens($user);

        $token = $this->authRepository->createUserToken($user);

        return [
            'token' => $token,
            'user'  => $user,
        ];
    }

    public function logout(Request $request): void
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $this->authRepository->deleteCurrentToken($user);
        }
    }

    public function register(array $data): array
    {
        $user = $this->authRepository->createUser([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $this->authRepository->deleteUserTokens($user);
        $token = $this->authRepository->createUserToken($user);

        return [
            'token' => $token,
            'user'  => $user,
        ];
    }
}