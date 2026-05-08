<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('faculty_borrowings', function (Blueprint $table) {
            $table->id();
            $table->string('faculty_name');
            $table->string('faculty_department', 100)->nullable();
            $table->string('faculty_contact', 100)->nullable();
            $table->string('book_title');
            $table->string('book_isbn', 20)->nullable();
            $table->date('borrow_date');
            $table->date('expected_return_date');
            $table->date('actual_return_date')->nullable();
            $table->enum('status', ['active', 'returned', 'overdue'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculty_borrowings');
    }
};
