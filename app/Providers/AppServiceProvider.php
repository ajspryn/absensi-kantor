<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
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
        // Prevent accidental destructive database artisan commands in production.
        // If APP_ENV=production and ALLOW_PROD_DB_COMMANDS is not truthy, block commands
        // like migrate:fresh, migrate:refresh, migrate:reset, migrate:rollback and db:wipe.
        if (app()->environment('production') && !env('ALLOW_PROD_DB_COMMANDS')) {
            // Listen for console commands starting and throw when a dangerous command runs.
            if ($this->app->runningInConsole()) {
                $this->app['events']->listen(CommandStarting::class, function (CommandStarting $event) {
                    $command = $event->command;
                    $dangerous = [
                        'migrate:fresh',
                        'migrate:refresh',
                        'migrate:reset',
                        'migrate:rollback',
                        'db:wipe',
                        'db:seed', // optional - seed can be destructive depending on seeds
                    ];

                    foreach ($dangerous as $d) {
                        if (Str::startsWith($command, $d) || Str::contains($command, " $d")) {
                            Log::warning("Blocked dangerous artisan command in production: {$command}");
                            throw new \RuntimeException("Artisan command '{$command}' is blocked in production. Set ALLOW_PROD_DB_COMMANDS=1 to allow.");
                        }
                    }
                });
            }
        }
    }
}
