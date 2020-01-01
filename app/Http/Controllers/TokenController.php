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

    /**
     * Generates and returns a new token for the current user
     * @param Request $request The request
     * @return \Illuminate\Http\JsonResponse Returns the new token
     */
    public function generateToken(Request $request)
    {
        $token = Str::random(60);

        $currentUser = User::find($request->user()->id);
        $currentUser->api_token = hash('sha256', $token);
        $currentUser->save();

        return response()->json(['token' => $token]);
    }
}
