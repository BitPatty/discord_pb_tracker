<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TokenController extends Controller
{
    public function __construct()
    {
    }

    public function generateToken(Request $request)
    {
        $token = Str::random(60);

        $currentUser = User::find($request->user()->id);
        $currentUser->api_token = hash('sha256', $token);
        $currentUser->save();

        return ['token' => $token];
    }
}
