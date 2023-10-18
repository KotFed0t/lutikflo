<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function loginOrRegister($phone)
    {
        $user = User::where('phone', $phone)->first();
        if ($user)
            $this->login($user);
        else
            $this->register($phone);
    }

    public function login(User $user)
    {
        Auth::login($user);
    }

    public function register($phone)
    {
        $user = User::create(['phone' => $phone]);
        $this->login($user);
    }
}
