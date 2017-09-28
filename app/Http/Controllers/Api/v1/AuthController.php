<?php

namespace App\Http\Controllers\Api\v1;

use App\Notifications\ConfirmationNotification;
use App\Repositories\User\DbUserRepository;
use Illuminate\Http\Request;
use App\Models\User;
use HttpException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Password;
use Tymon\JWTAuth\JWTAuth;
use Symfony\Component\HttpFoundation\Response as ResponseStatusCodes;

/**
        API EarthMedia:
    in the auth controller where are the reset and confirm end points
    if we need use the jwt-auth attempt method instead of fromUser(which is being used now) its going to be a problem , because
    at the moment validateCredentials method which is being used because we are using the Laravel provider is comparing the hashed password vs the plain password
    and at the moment our two end points is working with already builded user objects so we cant de-hashed it

    a possible solution will be:
    1.craete custom provider which will extends the laravel and override the validateCredentials method
    2. than build a logic based on some flag or anything we can pass via the request and skip the hash check if we are using
        those end points
 */


class AuthController extends Controller
{
    use IssueJWTTokenTrait;

    private $userRepo;
    private $JWTAuth;

    public function __construct(DbUserRepository $userRepo, JWTAuth $JWTAuth)
    {
        $this->userRepo = $userRepo;
        $this->JWTAuth = $JWTAuth;
    }

    public function login(Request $request)
    {
        $credentials = array_merge([
                'confirm' => true
            ], $request->only('email', 'password')
        );

        if($user = $this->userRepo->retrieveUserByEmail($credentials['email'])) {
            if(!$user->confirm) {
                return response()->json([
                    'error' => 'Confirm your account. Check your email address - ' . $user->email,
                    'status_code' => ResponseStatusCodes::HTTP_UNAUTHORIZED
                ], ResponseStatusCodes::HTTP_UNAUTHORIZED);
            }
        }

        return $this->issueToken('attempt', $credentials);
    }

    public function register(Request $request)
    {
        $credentials = array_merge([
                'confirm' => false,
                'confirmation_token' => str_random(64)
            ], $request->only('name', 'email', 'password')
        );
        $user = new User($credentials);
        if(!$user->save()) {
            return response()->json([
                'message' => 'Could not save user.',
                'status_code' => ResponseStatusCodes::HTTP_INTERNAL_SERVER_ERROR
            ], ResponseStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
        }

        $user->notify(new ConfirmationNotification($user));

        return response()->json([
            'message' => 'You have successfully registered.',
            'status_code' => ResponseStatusCodes::HTTP_CREATED,
            'data' => [$user]
        ]);
    }

    public function confirm($token = null)
    {

        $user = $this->userRepo->retrieveUserByConfirmationToken($token);
        $hasToReleaseToken = Config::get('boilerplate.user_confirm_email.confirm_email_token_release');
        if(!$this->userRepo->confirmEmail($token) || !$user) {
            return response()->json([
                'message' => 'Invalid token.',
                'status_code' => ResponseStatusCodes::HTTP_INTERNAL_SERVER_ERROR
            ], ResponseStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
        }

        if($hasToReleaseToken) {
            return $this->issueToken('fromUser', $user);
        }

        return response()->json([
            'message' => 'Email confirmed.',
            'status_code' => ResponseStatusCodes::HTTP_ACCEPTED
        ], ResponseStatusCodes::HTTP_ACCEPTED);
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
        return response()->json([], ResponseStatusCodes::HTTP_NO_CONTENT);
    }

    public function refresh()
    {
        /**
         * the refreshed_token is attached to the response headers from the RefreshToken middleware
         */
        return response()->json([
            'message' => 'You have successfully refreshed your token.',
            'status_code' => ResponseStatusCodes::HTTP_ACCEPTED
        ], ResponseStatusCodes::HTTP_ACCEPTED);
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
                    'status_code' => ResponseStatusCodes::HTTP_ACCEPTED
                ], ResponseStatusCodes::HTTP_ACCEPTED);
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
        $credentials = $request->only('email', 'password', 'token', 'password_confirmation');
        $hasToReleaseToken = Config::get('boilerplate.user_reset_password.reset_token_release');
        $response = Password::reset($credentials, function ($user, $password) use (&$userResetPassword) {
            $user->password = $password;
            $user->save();

            $userResetPassword = $user;
        });

        $statusCode = ResponseStatusCodes::HTTP_UNAUTHORIZED;
        if($response == Password::PASSWORD_RESET) {
            $statusCode = ResponseStatusCodes::HTTP_ACCEPTED;
            if($hasToReleaseToken) {
                return $this->issueToken('fromUser', $userResetPassword);
            }
        }

        return response()->json([
            'message' => trans($response),
            'status_code' => $statusCode
        ], $statusCode);
    }

}
