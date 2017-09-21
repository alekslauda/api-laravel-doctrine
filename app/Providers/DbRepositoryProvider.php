<?php

namespace App\Providers;

use App\Models\Social;
use App\Models\User;
use App\Repositories\DbRepositoryInterface;
use App\Repositories\User\DbUserRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class DbRepositoryProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //DI
//        $this->app->bind(DbUserRepository::class, function(){
//           return new DbUserRepository(new User());
//        });
        #$this->app->bind(DbRepositoryInterface::class, DbUserRepository::class);
        #$this->app->when(DbUserRepository::class)->needs(User::class)->give(User::class);
    }
}
