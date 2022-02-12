<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\TodoRepositoryInterface;
use App\Repositories\TodoRepository;

class RepositoryServiceProvider extends ServiceProvider {
    /**
    * Register services.
    *
    * @return void
    */

    public function register()  {
        $this->app->bind( TodoRepositoryInterface::class, TodoRepository::class );
    }

    /**
    * Bootstrap services.
    *
    * @return void
    */

    public function boot() {
        //
    }
}
