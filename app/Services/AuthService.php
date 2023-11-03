<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function loginOrRegister($phone): void
    {
        $user = User::where('phone', $phone)->first();
        if ($user)
            $this->login($user);
        else
            $this->register($phone);
    }

    public function login(User $user): void
    {
        Auth::login($user);
    }

    public function register($phone): void
    {
        $user = User::create(['phone' => $phone]);
        $this->login($user);
    }
}
