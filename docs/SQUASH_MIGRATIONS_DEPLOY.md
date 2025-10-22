# Squashed Migrations — Safe Deployment Guide

This document explains safe options to deploy squashed migrations to an existing production database without causing downtime or migration failures. It assumes you've created new squashed migration files in `database/migrations/` and moved prior individual migrations into `database/migrations/archived/`.

Important note: Do NOT delete original migrations from any branch that is currently deployed in production until you have a verified deployment plan. Rewriting migration history requires coordination with DB backups and maintenance windows.

## Goals

-   Ensure a fresh install uses the new squashed migrations to create schema.
-   Allow existing production databases (which already have tables created by old migrations) to accept the repo changes without `table already exists` errors.

## Options (ranked by safety)

### Option A — Keep originals in production branch (Safest)

-   Leave the original migration files present on the production branch/commit that is currently deployed.
-   On the main/dev branch keep the squashed migrations + `database/migrations/archived/` with originals for history.
-   When ready, deploy the new branch that contains squashed migrations _and also keeps original migrations_ until all prod nodes are moved to the new revision. This avoids touching the `migrations` table at all.

When safe to rewrite migration history (long maintenance window or new DB instance), you can remove originals and rely on squashed files.

### Option B — Mark squashed migrations as already applied in production (Practical)

When you must remove original migration files from the top-level `database/migrations/` and replace with squashed ones, production's `migrations` table will not contain the new filenames. To avoid `table already exists` errors, insert rows into the `migrations` table that correspond to each squashed migration (filename without `.php`) and set an appropriate `batch` number.

Steps (high level):

1. Backup your production database (mandatory).
2. Enter maintenance mode if applicable.
3. Determine the current maximum batch in `migrations`:

    SELECT COALESCE(MAX(batch), 0) AS last_batch FROM migrations;

    Let `LAST` be this value.

4. Insert the squashed migration names into `migrations` with `batch = LAST + 1` (or any higher batch number you choose). Example SQL for MySQL/Postgres/SQLite (migration name = file name without `.php`):

    INSERT INTO migrations (migration, batch) VALUES
    ('2025_10_20_000001_create_roles_table_squashed', LAST + 1),
    ('2025_10_20_000002_create_users_table_squashed', LAST + 1),
    ('2025_10_20_000003_create_departments_table_squashed', LAST + 1),
    ('2025_10_20_000004_create_positions_and_work_schedules_squashed', LAST + 1),
    ('2025_10_20_000005_create_employees_table_squashed', LAST + 1),
    ('2025_10_20_000006_create_attendances_table_squashed', LAST + 1);

Notes:

-   Replace `LAST + 1` with the numeric value you got from the `SELECT` query.
-   Ensure migration names exactly match filenames in `database/migrations` minus the `.php` suffix.

Example concrete MySQL session (interactive):

mysql -u <user> -p <database>
SELECT COALESCE(MAX(batch),0) AS last_batch FROM migrations;
-- Suppose last_batch = 12
INSERT INTO migrations (migration, batch) VALUES
('2025_10_20_000001_create_roles_table_squashed', 13),
('2025_10_20_000002_create_users_table_squashed', 13),
...;

After inserting, run `php artisan migrate:status` to confirm Laravel considers those migrations as already applied.

### Option C — Add compatibility no-op migrations (Alternative)

Create new migrations that do nothing when the target table already exists. These migrations should be safe to run on existing databases (they check for table existence before creating or altering). Example:

```php
public function up(): void
{
    if (! Schema::hasTable('roles')) {
        Schema::create('roles', function (Blueprint $table) {
            // ... schema definition
        });
    }
}
```

This approach is more work because you'd need to author safe guards for each squashed migration and ensure down() is safe. It can be used when you prefer to run `php artisan migrate` rather than modifying the `migrations` table.

## Recommended process (concrete)

1. Backup DB and assets. Test backup restore on a staging copy.
2. Prepare a short maintenance window.
3. Merge/push the branch containing squashed migrations and `database/migrations/archived/`.
4. On production, either:
    - Keep original migrations present until all instances are migrated (Option A), OR
    - If removing originals, mark squashed migrations as applied by inserting records into `migrations` (Option B). Use the `SELECT MAX(batch)` trick and insert with `batch = last_batch + 1`.
5. Run `php artisan migrate --force` to apply any remaining migrations that are safe to run.
6. Run health checks and smoke tests.
7. Exit maintenance mode.

## Verification commands

-   Check migrations recognized by Laravel:

    php artisan migrate:status

-   Verify a migration is listed as run (the status command will show it as `Yes`).

## Rollback / Recovery

-   If something goes wrong, you can restore the DB from the backup.
-   If you inserted rows into the `migrations` table and need to revert that, you can delete those rows. Proceed carefully:

    DELETE FROM migrations WHERE migration IN (
    '2025_10_20_000001_create_roles_table_squashed',
    ...
    );

-   Only delete migration rows if you understand the consequences; if the actual schema was already present, deleting the row then running `php artisan migrate` may try to create existing tables and fail.

## Final notes

-   Keep `database/migrations/archived/` under version control as the canonical history of how the schema evolved.
-   Update your CI pipeline to run migrations from scratch on every branch to detect missing schema elements early.
-   If you want, I can prepare a small `artisan` command or a seeder that programmatically inserts squashed migration rows into `migrations` (with confirmation prompts), or create no-op compatibility migrations automatically.

---

If you want, I can now:

-   generate the SQL snippet for your specific squashed migration filenames (I already included examples above), or
-   add a small `database/seeders/MarkSquashedMigrationsSeeder.php` that inserts the rows via Laravel's DB facade (safer than raw SQL), or
-   prepare a PR that includes this README and the seeder/command.
