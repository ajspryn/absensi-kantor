<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Mark squashed migrations as applied by running the seeder that inserts rows into `migrations` table.
Artisan::command('migrations:mark-squashed', function () {
    $this->comment('This will mark squashed migrations as applied by inserting rows into the migrations table.');

    if (! $this->confirm('Are you sure you want to continue?')) {
        $this->comment('Aborted.');
        return 1;
    }

    $this->comment('Running seeder: Database\\Seeders\\MarkSquashedMigrationsSeeder');
    $exit = $this->call('db:seed', ['--class' => 'Database\\Seeders\\MarkSquashedMigrationsSeeder', '--force' => true]);

    if ($exit === 0) {
        $this->info('Seeder finished successfully. Run php artisan migrate:status to verify.');
        return 0;
    }

    $this->error('Seeder failed with code: ' . $exit);
    return $exit;
})->purpose('Mark squashed migrations as applied (idempotent)');

// Register the purge-data command wrapper so the class in app/Console/Commands works
Artisan::command('app:purge-data {--dry-run} {--yes}', function () {
    /** @var \App\Console\Commands\PurgeData $command */
    $command = app(\App\Console\Commands\PurgeData::class);
    // Ensure command has the application, input and output set so ->info()/->line()/->option() work
    $command->setLaravel(app());
    if (method_exists($command, 'setInput')) {
        $command->setInput($this->input);
    }
    if (method_exists($command, 'setOutput')) {
        $command->setOutput($this->output);
    }

    return $command->handle();
})->purpose('Purge positions, employees and non-admin users (use --dry-run to preview)');
