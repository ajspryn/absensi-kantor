# Usage: MarkSquashedMigrationsSeeder

This seeder helps mark squashed migration files as "already applied" in an existing database by inserting rows into the `migrations` table. It's idempotent and safe to run multiple times.

WARNING: Always backup your production database before running any changes.

How it works

-   It reads the `migrations` table and inserts only the squashed migration names that are missing.
-   It uses `batch = (max existing batch) + 1` for inserted rows.

How to run (production example)

1. Backup your DB.
2. Ensure the squashed migration files are present in `database/migrations` and that the seeder list in `database/seeders/MarkSquashedMigrationsSeeder.php` matches those filenames (without `.php`).
3. Put the app in maintenance mode (optional but recommended):

    php artisan down --message="Squash migrations: marking applied"

4. Run the seeder on production (ensure you are using the production environment/config):

    php artisan db:seed --class=Database\\Seeders\\MarkSquashedMigrationsSeeder --force

5. Verify with:

    php artisan migrate:status

6. If everything looks OK, remove maintenance mode:

    php artisan up

Notes

-   This seeder does NOT modify schemas. It only writes to the `migrations` table.
-   If you manually inserted migration rows earlier, the seeder will skip duplicates.
-   If you later want to rollback these inserted migration entries, remove the corresponding rows from the `migrations` table. Do this only if you know the consequences.

Example: run on staging first to validate the workflow.
