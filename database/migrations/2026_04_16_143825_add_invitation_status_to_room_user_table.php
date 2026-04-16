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
        Schema::table('room_user', function (Blueprint $table) {
            $table->enum('role', ['owner', 'member'])->default('member');
            $table->enum('status', ['pending', 'accepted', 'declined'])->default('accepted');
            $table->timestamp('invited_at')->nullable();
            $table->foreignId('invited_by')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_user', function (Blueprint $table) {
            $table->dropForeign(['invited_by']);
            $table->dropColumn(['role', 'status', 'invited_at', 'invited_by']);
        });
    }
};
