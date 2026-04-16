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
        Schema::table('song_infos', function (Blueprint $table) {
            $table->string('spotify_track_id')->nullable();
            $table->json('spotify_external_urls')->nullable();
            $table->string('spotify_preview_url')->nullable();
            $table->json('spotify_images')->nullable();
            $table->integer('spotify_duration_ms')->nullable();
            $table->string('spotify_uri')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('song_infos', function (Blueprint $table) {
            $table->dropColumn([
                'spotify_track_id',
                'spotify_external_urls',
                'spotify_preview_url',
                'spotify_images',
                'spotify_duration_ms',
                'spotify_uri'
            ]);
        });
    }
};
