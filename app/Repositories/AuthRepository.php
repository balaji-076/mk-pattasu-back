<?php

namespace App\Repositories;

use App\Models\PasswordOtp;
use App\Models\User;

class AuthRepository
{
    public function findUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function deleteUserTokens(User $user): void
    {
        $user->tokens()->delete();
    }

    public function createUserToken(User $user): string
    {
        return $user->createToken('auth')->plainTextToken;
    }

    public function getLatestOtp(int $userId): ?PasswordOtp
    {
        return PasswordOtp::where('user_id', $userId)->orderByDesc('id')->first();
    }

    public function deleteUserOtps(int $userId): void
    {
        PasswordOtp::where('user_id', $userId)->delete();
    }

    public function createOtp(array $data): PasswordOtp
    {
        return PasswordOtp::create($data);
    }

    public function deleteOtp(PasswordOtp $otpRow): void
    {
        $otpRow->delete();
    }

    public function incrementAttempts(PasswordOtp $otpRow): PasswordOtp
    {
        $otpRow->attempts = $otpRow->attempts + 1;

        if ($otpRow->attempts >= 5) {
            $otpRow->locked_until = now()->addMinutes(15);
        }

        $otpRow->save();
        return $otpRow;
    }

    public function findById(int $id): User
    {
        return User::findOrFail($id);
    }

    public function updatePassword(User $user, string $password): bool
    {
        return $user->update([
            'password' => $password
        ]);
    }

    public function deleteCurrentToken(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function createUser(array $data): User
    {
        return User::create($data);
    }

}