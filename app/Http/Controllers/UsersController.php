<?php

namespace App\Http\Controllers;

use App\Http\Resources\Users\UserResource;
use App\Notifications\Registration;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UsersController extends Controller
{

    public function regist(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'first_name' => 'required|min:3',
            'last_name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);
        $success = false;

        if (!$validation->fails()) {
            $success = true;

            $user = User::create([
                'password' => Hash::make($request->password),
                'active' => 0,
                'registration_token' => Str::random(20),
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
            ]);

            $user->notify(new Registration());
        }

        return $this->success($success);
    }

    public function acceptRegistration(Request $request)
    {
        $user = User::where('registration_token', $request->token)->first();
        $user->acceptRegistration();

        return response()->redirectTo('/');
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!empty($user) && Hash::check($request->password, $user->password)) {
            $tokenResult = $user->createToken('Personal Access Token');
            $tokenResult->token->expires_at = Carbon::now()->addMonth();

            if ($request->remember_me) {
                $tokenResult->token->expires_at = Carbon::now()->addYear();
            }

            return response()->json([
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString()
            ]);

        }

        return $this->success(false);
    }

    public function getCurrUser(Request $request) {
        $user = $request->user();

        return new UserResource($user);
    }


    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return $this->success(true);
    }
}
