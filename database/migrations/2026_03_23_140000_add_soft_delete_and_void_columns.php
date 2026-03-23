<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('transactions', function (Blueprint $table): void {
            $table->timestamp('voided_at')->nullable()->index();
            $table->string('void_reason', 255)->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table): void {
            $table->dropSoftDeletes();
            $table->dropColumn(['voided_at', 'void_reason']);
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });
    }
};
