<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ValidateRequestsWithBoilerPlateRules
{
    use ValidatesRequests;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next , $boilerPlateConfigOptionRules)
    {
        $rules = $this->getValidationRules($boilerPlateConfigOptionRules);
        try {
            $this->validate($request, $rules);
        } catch (ValidationException $ex) {
            return response()->json(['message' => $ex->getMessage(), 'errors' => $ex->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return $next($request);
    }

    public function getValidationRules($boilerPlateConfigOptionRules)
    {
        switch ($boilerPlateConfigOptionRules)
        {
            case 'user_register':
                return Config::get('boilerplate.user_register.validation_rules');
            case 'user_login':
                return Config::get('boilerplate.user_login.validation_rules');
            case 'user_forgot_password':
                return Config::get('boilerplate.user_forgot_password.validation_rules');
            case 'user_reset_password':
                return Config::get('boilerplate.user_reset_password.validation_rules');
            default:
                throw new \Exception("Boilerplate config option: {$boilerPlateConfigOptionRules} not found");
        }
    }
}
