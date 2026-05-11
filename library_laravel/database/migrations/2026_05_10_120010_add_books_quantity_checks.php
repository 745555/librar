<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('books')) {
            return;
        }

        $driver = DB::getDriverName();

        // SQLite (tests) ignores CHECK constraints and Laravel schema builder
        // does not support portable check constraints across all drivers.
        if ($driver === 'sqlite') {
            return;
        }

        // MySQL 8+ and PostgreSQL support CHECK constraints.
        // Keep constraint names stable for rollback.
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `books` ADD CONSTRAINT `chk_books_quantity_nonnegative` CHECK (`quantity` >= 0)');
            DB::statement('ALTER TABLE `books` ADD CONSTRAINT `chk_books_available_nonnegative` CHECK (`available_quantity` >= 0)');
            DB::statement('ALTER TABLE `books` ADD CONSTRAINT `chk_books_available_lte_quantity` CHECK (`available_quantity` <= `quantity`)');
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE books ADD CONSTRAINT chk_books_quantity_nonnegative CHECK (quantity >= 0)');
            DB::statement('ALTER TABLE books ADD CONSTRAINT chk_books_available_nonnegative CHECK (available_quantity >= 0)');
            DB::statement('ALTER TABLE books ADD CONSTRAINT chk_books_available_lte_quantity CHECK (available_quantity <= quantity)');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('books')) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            return;
        }

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `books` DROP CHECK `chk_books_quantity_nonnegative`');
            DB::statement('ALTER TABLE `books` DROP CHECK `chk_books_available_nonnegative`');
            DB::statement('ALTER TABLE `books` DROP CHECK `chk_books_available_lte_quantity`');
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE books DROP CONSTRAINT IF EXISTS chk_books_quantity_nonnegative');
            DB::statement('ALTER TABLE books DROP CONSTRAINT IF EXISTS chk_books_available_nonnegative');
            DB::statement('ALTER TABLE books DROP CONSTRAINT IF EXISTS chk_books_available_lte_quantity');
        }
    }
};

