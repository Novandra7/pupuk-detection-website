<?php

namespace App\Providers;

use App\Interfaces\BagRepository;
use App\Interfaces\CctvRepository;
use App\Interfaces\RoleAndPermissionRepository;
use App\Interfaces\ShiftRepository;
use App\Interfaces\UserRepository;
use App\Interfaces\WarehouseRepository;
use App\Repositories\BagRepositoryImpl;
use App\Repositories\CctvRepositoryImpl;
use App\Repositories\RoleAndPermissionRepositoryImpl;
use App\Repositories\ShiftRepositoryImpl;
use App\Repositories\UserRepositoryImpl;
use App\Repositories\WarehouseRepositoryImpl;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepository::class,UserRepositoryImpl::class);
        $this->app->bind(WarehouseRepository::class,WarehouseRepositoryImpl::class);
        $this->app->bind(CctvRepository::class,CctvRepositoryImpl::class);
        $this->app->bind(BagRepository::class,BagRepositoryImpl::class);
        $this->app->bind(ShiftRepository::class,ShiftRepositoryImpl::class);
        $this->app->bind(RoleAndPermissionRepository::class,RoleAndPermissionRepositoryImpl::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
