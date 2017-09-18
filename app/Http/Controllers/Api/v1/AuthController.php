<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Social;
use Illuminate\Http\Request;
use App\Models\User;
use HttpException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;
use Symfony\Component\HttpFoundation\Response as ResponseStatusCodes;

class AuthController extends Controller
{
    public function login(Request $request, JWTAuth $JWTAuth)
    {
        $this->validate($request, Config::get('boilerplate.user_login.validation_rules'));
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = $JWTAuth->attempt($credentials)) {
                return response()->json([
                    'error' => 'Invalid credentials.',
                    'status_code' => ResponseStatusCodes::HTTP_UNAUTHORIZED
                ], ResponseStatusCodes::HTTP_UNAUTHORIZED);
            }
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Could not create token.',
                'status_code' => ResponseStatusCodes::HTTP_INTERNAL_SERVER_ERROR
            ], ResponseStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()
            ->json([
                'message' => 'You have successfully refreshed your token.',
                'status_code' => ResponseStatusCodes::HTTP_OK
            ])
            ->header('Authorization', 'Bearer '.$token);

    }

    public function register(Request $request)
    {
        $this->validate($request, Config::get('boilerplate.user_register.validation_rules'));

        $credentials = $request->all();
        $credentials['password'] = Hash::make($credentials['password']);
        $user = new User($credentials);
        if(!$user->save()) {
            throw new HttpException(500);
        }

        return response()->json([
            'message' => 'You have successfully registered.',
            'status_code' => ResponseStatusCodes::HTTP_OK,
            'data' => [$user]
        ]);
    }

    public function facebook(Request $request, JWTAuth $JWTAuth)
    {
        //logic
        //handle data
        //hardcoded should be get from the request
        $provider_id = '930440620437118';
        $user = Social::where('provider_id', '=', $provider_id)->first()->user()->first();
//password?
        dd($JWTAuth->fromUser($user));

    }

//    public function handleFacebookCallbackUrl(Request $request)
//    {

//        $social_user = Socialite::with('facebook')->stateless()->user();
//        dd($social_user);
//
//
//        return response()->json();
//    }

    public function logout(JWTAuth $JWTAuth)
    {
        $JWTAuth->invalidate();
        return response()->json([
            'message' => 'You have successfully logout.',
            'status_code' => ResponseStatusCodes::HTTP_NO_CONTENT
        ], ResponseStatusCodes::HTTP_NO_CONTENT);
    }

    public function refresh()
    {
        /**
         * get the refreshed_token from the response headers
         */
        return response()->json([
            'message' => 'You have successfully refreshed your token.',
            'status_code' => ResponseStatusCodes::HTTP_NO_CONTENT
        ], ResponseStatusCodes::HTTP_NO_CONTENT);
    }
}
