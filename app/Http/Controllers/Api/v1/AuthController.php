<?php

namespace App\Http\Controllers\Api\v1;

use App\Repositories\User\DbUserRepository;
use Illuminate\Http\Request;
use App\Models\User;
use HttpException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;
use Symfony\Component\HttpFoundation\Response as ResponseStatusCodes;

class AuthController extends Controller
{
    private $userRepo;
    private $JWTAuth;

    public function __construct(DbUserRepository $userRepo, JWTAuth $JWTAuth)
    {
        $this->userRepo = $userRepo;
        $this->JWTAuth = $JWTAuth;
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            if (! $token = $this->JWTAuth->attempt($credentials)) {
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
                'message' => 'You have successfully created your token.',
                'status_code' => ResponseStatusCodes::HTTP_OK
            ])
            ->header('Authorization', 'Bearer '.$token);

    }

    public function register(Request $request)
    {
        $credentials = $request->all();
        $hasToReleaseToken = Config::get('boilerplate.user_register.register_token_release');
        $user = new User($credentials);
        if(!$user->save()) {
            return response()->json([
                'message' => 'Could not save user.',
                'status_code' => ResponseStatusCodes::HTTP_INTERNAL_SERVER_ERROR
            ], ResponseStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
        }

        /**
         * optional check boilerplate configuration
         */
        if($hasToReleaseToken) {
            return $this->login($request);
        }

        return response()->json([
            'message' => 'You have successfully registered.',
            'status_code' => ResponseStatusCodes::HTTP_OK,
            'data' => [$user]
        ]);
    }

    /**
     * TODO
     */
    public function facebook(Request $request)
    {
        $provider_id = '930440620437118';

// $socialUser = Socialite::with('facebook')->userFromToken('token');

        // if ( ! @$socialUser->user->id) {
        //     return response([
        //         'status' => 'error',
        //         'code' => 'ErrorGettingSocialUser',
        //         'msg' => 'There was an error getting the ' . $type . ' user.'
        //     ], 400);
        // }
        // $user = $this->userRepo->retrieveUserBySocialProviderId($socialUser->user->id);
        // if ( ! ($user instanceof User)) {
        //     $user = User::where('email', $social_user->email)->first();
        //     if ( ! ($user instanceof User)) {
        //         $new_user = true;
        //         $user = new User();
        //     }
        //     $user->{$type . '_id'} = $social_user->id;
        // }
        // // Update info and save.
        // if (empty($user->email)) { $user->email = $social_user->email; }
        // if (empty($user->first_name)) { $user->first_name = $social_user->first_name; }
        // if (empty($user->last_name)) { $user->last_name = $social_user->last_name; }
//        if ( ! $token = $this->JWTAuth->fromUser($user)) {
//            throw new AuthorizationException;
//        }
        //logic
        //handle data
        //hardcoded should be get from the request
        //check if the user have linked his email to social provider
        /*
         * if the user is not linked with social , check with the email from social if we already have this user and if
         * the user is in our system log him in and populate his social credentials
        */
        /**
         * if the user is pass the credentials to the request and redirect him to populate his password where
         * will auto populate his user and social profile and afterwards we will use them to log him in
         */



        dd($this->userRepo->retrieveUserBySocialProviderId($provider_id));

    }

    public function logout()
    {
        $this->JWTAuth->invalidate();
        return response()->json([
            'message' => 'You have successfully logout.',
            'status_code' => ResponseStatusCodes::HTTP_NO_CONTENT
        ], ResponseStatusCodes::HTTP_NO_CONTENT);
    }

    public function refresh()
    {
        /**
         * the refreshed_token is attached to the response headers from the RefreshToken middleware
         */
        return response()->json([
            'message' => 'You have successfully refreshed your token.',
            'status_code' => ResponseStatusCodes::HTTP_NO_CONTENT
        ], ResponseStatusCodes::HTTP_NO_CONTENT);
    }

    /**
     * password.email => POST
     * will validate the request and send email to the user for resetting his password
     */
    public function recovery(Request $request)
    {
        $response = Password::sendResetLink($request->only('email'), function (Message $message) {
            $message->subject(Config::get('boilerplate.user_forgot_password.forgot_email_subject'));
        });
        switch ($response) {
            case Password::RESET_LINK_SENT:
                return response()->json([
                    'message' => 'Reset link sent.',
                    'status_code' => ResponseStatusCodes::HTTP_NO_CONTENT
                ], ResponseStatusCodes::HTTP_NO_CONTENT);
            case Password::INVALID_USER:
                throw new HttpException(500);
        }
    }

    /**
     * password.reset route => POST
     * will validate the request and probably will return token to the user ?
     */
    public function reset(Request $request)
    {
        $credentials = $request->all();
        $hasToReleaseToken = Config::get('boilerplate.user_reset_password.reset_token_release');
        $response = Password::reset($credentials, function ($user, $password) {
            $user->password = $password;
            $user->save();
        });
        switch ($response) {
            case Password::PASSWORD_RESET:
                if($hasToReleaseToken   ) {
                    return $this->login($request);
                }
                return response()->json([
                    'message' => 'Password reset.',
                    'status_code' => ResponseStatusCodes::HTTP_NO_CONTENT
                ], ResponseStatusCodes::HTTP_NO_CONTENT);
            default:
                return response()->json([
                    'message' => 'Could not reset password.',
                    'status_code' => ResponseStatusCodes::HTTP_INTERNAL_SERVER_ERROR
                ], ResponseStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
