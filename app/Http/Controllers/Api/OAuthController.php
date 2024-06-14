<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Exception;

class OAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->stateless()
            ->redirect();
    }
    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')
                ->stateless()
                ->user();

            $existingUser = User::where('email', $user->getEmail())->first();

            if ($existingUser) {
                Auth::login($existingUser, true);
                $token = $existingUser->createToken('API Token')->accessToken;
            } else {
                $newUser = User::create([
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                    'google_id' => $user->getId(),
                    'password' => bcrypt(Str::random(24)), // generate a random password
                ]);

                Auth::login($newUser, true);
                $token = $newUser->createToken('API Token')->accessToken;
            }

            return response()->json(['token' => $token], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Unable to authenticate'], 400);
        }
    }
}
