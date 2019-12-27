<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::DASHBOARD;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectToProvider()
    {
        return Socialite::with('discord')
            ->setScopes(['identify'])
            ->redirect();
    }

    public function handleProviderCallback(Request $request)
    {
        if (!$request->input('code')) {
            return redirect('/')->withErrors('Login failed: ' . $request->input('error') . ' - ' . $request->input('error_reason'));
        }

        $user = Socialite::driver('discord')->user();
        $existingUser = User::where(['discord_id' => $user->getId()])->first();
        if (!$existingUser) {
            $existingUser = new User();
            $existingUser->discord_id = $user->getId();
            $existingUser->name = $user->getNickname();
            $existingUser->avatar_url = $user->getAvatar();
            $existingUser->save();
        } else {
            $existingUser->name = $user->getNickname();
            $existingUser->avatar_url = $user->getAvatar();
            $existingUser->save();
        }

        auth()->login($existingUser);
        return redirect($this->redirectTo);
    }
}
