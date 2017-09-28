<?php
namespace App\Http\Controllers\Api\v1;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response as ResponseStatusCodes;
use Tymon\JWTAuth\JWTAuth;

/**
 * Trait IssueJWTTokenTrait
 * @package App\Http\Controllers\Api\v1
 *
 *      / JWTAuth issue token from/
 *
 * @method fromUser($user, array $customClaims = [])
 * @method attempt(array $credentials = [], array $customClaims = [])
 */

trait IssueJWTTokenTrait
{
    /**
     * @param string $method
     * @param array ...$args
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function issueToken($method="fromUser", ...$args)
    {
        if (!$this->JWTAuth) {
            throw new NotFoundHttpException('You can use this trait only with JWTAuth. Check ' . JWTAuth::class);
        }

        if (!method_exists($this->JWTAuth, $method)) {
            throw new \RuntimeException("{$method} does not exist in " . get_class($this->JWTAuth));
        }

        $reflection = new \ReflectionMethod(get_class($this->JWTAuth), $method);
        if (count($args) < $reflection->getNumberOfRequiredParameters()) {
            throw new \RuntimeException("{$method} requires {$reflection->getNumberOfRequiredParameters()} parameter. {$args} given");
        }

        try {
            if (!$token = call_user_func_array([$this->JWTAuth, $method], $args)) {
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
                'status_code' => ResponseStatusCodes::HTTP_CREATED
            ])
            ->header('Authorization', 'Bearer ' . $token);
    }
}