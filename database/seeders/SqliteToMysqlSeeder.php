<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;

/**
 * Copy application data from SQLite into the current MySQL/MariaDB database.
 *
 * Prerequisites:
 * 1. MySQL credentials configured in .env (DB_CONNECTION=mysql, DB_HOST, etc.)
 * 2. Migrations already run on MySQL: php artisan migrate --force
 * 3. SQLite file available (default: database/database.sqlite)
 *
 * Run:
 *   php artisan db:seed --class=SqliteToMysqlSeeder --force
 *
 * Optional env:
 *   SQLITE_SOURCE_DATABASE=/absolute/path/to/database.sqlite
 *   SQLITE_TO_MYSQL_WIPE=true   (default true — truncates target tables first)
 */
class SqliteToMysqlSeeder extends Seeder
{
    /**
     * Tables copied in FK-safe order (foreign keys disabled during import).
     *
     * @var list<string>
     */
    private array $tables = [
        'users',
        'password_reset_tokens',
        'categories',
        'products',
        'product_images',
        'customers',
        'leads',
        'assistant_requests',
        'sales',
        'sale_items',
        'activities',
        'admin_notifications',
        'settings',
        'audit_logs',
    ];

    public function run(): void
    {
        $source = 'sqlite_source';
        $target = (string) config('database.default');
        $wipe = filter_var(env('SQLITE_TO_MYSQL_WIPE', true), FILTER_VALIDATE_BOOLEAN);

        $sourcePath = (string) config('database.connections.sqlite_source.database');
        if (! is_file($sourcePath)) {
            throw new RuntimeException("SQLite source file not found: {$sourcePath}");
        }

        $driver = DB::connection($target)->getDriverName();
        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            throw new RuntimeException(
                "Default connection must be mysql/mariadb to import into MySQL. Current driver: {$driver}."
            );
        }

        if ($target === $source) {
            throw new RuntimeException('Source and target database connections must be different.');
        }

        $this->command?->info("Source: sqlite ({$sourcePath})");
        $this->command?->info("Target: {$driver} (".config("database.connections.{$target}.database").')');

        $this->disableForeignKeys($target);

        try {
            if ($wipe) {
                $this->command?->warn('Wiping target tables…');
                foreach (array_reverse($this->tables) as $table) {
                    if (Schema::connection($target)->hasTable($table)) {
                        DB::connection($target)->table($table)->delete();
                    }
                }
            }

            foreach ($this->tables as $table) {
                $this->copyTable($source, $target, $table);
            }
        } finally {
            $this->enableForeignKeys($target);
        }

        $this->command?->info('SQLite → MySQL import finished.');
    }

    private function copyTable(string $source, string $target, string $table): void
    {
        if (! $this->sqliteHasTable($source, $table)) {
            $this->command?->warn("Skip {$table}: missing on SQLite source.");

            return;
        }

        if (! Schema::connection($target)->hasTable($table)) {
            $this->command?->warn("Skip {$table}: missing on MySQL target (run migrations first).");

            return;
        }

        $sourceColumns = $this->sqliteColumns($source, $table);
        $targetColumns = Schema::connection($target)->getColumnListing($table);
        $columns = array_values(array_intersect($sourceColumns, $targetColumns));

        if ($columns === []) {
            $this->command?->warn("Skip {$table}: no shared columns.");

            return;
        }

        $orderColumn = in_array('id', $columns, true) ? 'id' : $columns[0];
        $count = 0;
        $chunkSize = 200;
        $lastKey = null;

        // Manual paging avoids Laravel Schema helpers that need newer SQLite.
        while (true) {
            $query = DB::connection($source)->table($table)->orderBy($orderColumn)->limit($chunkSize);

            if ($lastKey !== null) {
                $query->where($orderColumn, '>', $lastKey);
            }

            $rows = $query->get();
            if ($rows->isEmpty()) {
                break;
            }

            $payload = [];
            foreach ($rows as $row) {
                $item = [];
                foreach ($columns as $column) {
                    $item[$column] = $this->normalizeValue($row->{$column} ?? null);
                }
                $payload[] = $item;
                $lastKey = $row->{$orderColumn};
            }

            try {
                DB::connection($target)->table($table)->insert($payload);
                $count += count($payload);
            } catch (Throwable) {
                foreach ($payload as $item) {
                    try {
                        DB::connection($target)->table($table)->insert($item);
                        $count++;
                    } catch (Throwable $rowError) {
                        $this->command?->error("Failed {$table} row: ".$rowError->getMessage());
                    }
                }
            }

            if ($rows->count() < $chunkSize) {
                break;
            }
        }

        if (in_array('id', $columns, true)) {
            $maxId = (int) DB::connection($target)->table($table)->max('id');
            if ($maxId > 0) {
                DB::connection($target)->statement(
                    'ALTER TABLE `'.$table.'` AUTO_INCREMENT = '.($maxId + 1)
                );
            }
        }

        $this->command?->line("Copied {$table}: {$count} row(s)");
    }

    private function sqliteHasTable(string $connection, string $table): bool
    {
        $row = DB::connection($connection)->selectOne(
            "SELECT name FROM sqlite_master WHERE type = 'table' AND name = ?",
            [$table]
        );

        return $row !== null;
    }

    /**
     * Use PRAGMA table_info for older SQLite hosts that lack pragma_table_xinfo().
     *
     * @return list<string>
     */
    private function sqliteColumns(string $connection, string $table): array
    {
        // Quote table name safely for PRAGMA (identifier only).
        $safe = str_replace("'", "''", $table);
        $rows = DB::connection($connection)->select("PRAGMA table_info('{$safe}')");

        return collect($rows)
            ->map(fn ($row) => (string) ($row->name ?? ''))
            ->filter()
            ->values()
            ->all();
    }

    private function normalizeValue(mixed $value): mixed
    {
        if (is_bool($value)) {
            return $value ? 1 : 0;
        }

        return $value;
    }

    private function disableForeignKeys(string $connection): void
    {
        DB::connection($connection)->statement('SET FOREIGN_KEY_CHECKS=0');
    }

    private function enableForeignKeys(string $connection): void
    {
        DB::connection($connection)->statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
