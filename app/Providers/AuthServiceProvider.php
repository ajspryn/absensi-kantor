<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\DailyActivity;
use App\Policies\DailyActivityPolicy;

class AuthServiceProvider extends ServiceProvider
{
     /**
      * Register any application services.
      */
     public function register(): void
     {
          //
     }

     /**
      * Bootstrap any application services.
      */
     public function boot(): void
     {
          // Register model policies
          Gate::policy(DailyActivity::class, DailyActivityPolicy::class);
     }
}
