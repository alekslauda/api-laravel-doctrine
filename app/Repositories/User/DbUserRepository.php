<?php
namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\AbstractDbRepository;

class DbUserRepository extends AbstractDbRepository
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function retrieveUserBySocialProviderId($providerId)
    {
        return $this->model->whereHas('socials', function($query) use ($providerId){
            $query->where('socials.provider_id', $providerId);
        })->first();
    }

    public function retrieveUserByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function retrieveUserByConfirmationToken($token)
    {
        /**
         * trying and using different alternatives fo eloquent
         */
        return $this->model->where(
            [
                ['confirmation_token', '=', $token],
                ['confirm', '=', false]
            ]
//            [
//                'confirmation_token' => ['confirmation_token' => $token],
//                'confirm' => ['confirm' => false]
//            ]
        )->first();
    }

    public function confirmEmail($token)
    {
        return $this->update([
            'confirm' => 1,
            'confirmation_token' => null
        ], 'confirmation_token', $token);
    }
}