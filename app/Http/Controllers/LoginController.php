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

    /**
     * Redirect the user to the discord OAUTH page
     * @return mixed
     */
    public function redirectToProvider()
    {
        return Socialite::with('discord')
            ->setScopes(['identify'])
            ->redirect();
    }

    /**
     * Authenticates the user based on the discord response
     * @param Request $request The request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function handleProviderCallback(Request $request)
    {
        if (!$request->input('code')) {
            return redirect('/')->withErrors('Login failed: ' . $request->input('error') . ' - ' . $request->input('error_reason'));
        }

        $user = Socialite::driver('discord')->user();
        $dbUser = User::where(['discord_id' => $user->getId()])->first();
        if (!$dbUser) {
            $dbUser = new User();
            $dbUser->discord_id = $user->getId();
            $dbUser->name = $user->getNickname();
            $dbUser->avatar_url = $user->getAvatar();
            $dbUser->save();
        } else {
            $dbUser->name = $user->getNickname();
            $dbUser->avatar_url = $user->getAvatar();
            $dbUser->save();
        }

        auth()->login($dbUser);
        return redirect($this->redirectTo);
    }

    public function logout(Request $request)
    {
        auth()->logout();
        return redirect('/');
    }
}
