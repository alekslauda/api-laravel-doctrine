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
}