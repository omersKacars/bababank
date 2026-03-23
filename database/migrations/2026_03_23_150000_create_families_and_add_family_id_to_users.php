<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('families', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('family_id')->nullable()->after('parent_id')->constrained('families')->nullOnDelete();
        });

        // Backfill existing users into family groups so old data remains usable.
        $parents = DB::table('users')->where('role', 'parent')->get(['id', 'name']);
        foreach ($parents as $parent) {
            $familyId = DB::table('families')->insertGetId([
                'name' => trim((string) $parent->name) !== '' ? $parent->name.' Ailesi' : 'Aile '.$parent->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('users')->where('id', $parent->id)->update(['family_id' => $familyId]);
            DB::table('users')->where('parent_id', $parent->id)->update(['family_id' => $familyId]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('family_id');
        });

        Schema::dropIfExists('families');
    }
};
