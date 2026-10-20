<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('branch_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['branch_id', 'user_id']);
        });

        // Automatically associate existing staff users with the main branch so no user is locked out
        $mainBranch = DB::table('branches')->where('is_main_branch', true)->first();
        if ($mainBranch) {
            $users = DB::table('users')->pluck('id');
            foreach ($users as $userId) {
                DB::table('branch_user')->insertOrIgnore([
                    'branch_id' => $mainBranch->id,
                    'user_id' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_user');
    }
};
