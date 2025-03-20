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
        Schema::create('urls', function (Blueprint $table) {
            $table->ulid('id')->primary(); // Use ULID instead of auto-increment ID
            $table->text('original_url'); // Use text for encrypted URLs
            $table->string('short_url')->unique(); // Hash stored as string
            $table->timestamp('expires_at')->nullable(); // Expiry time for the link
            $table->timestamps();
        });

        // Add an index for better performance on queries
        Schema::table('urls', function (Blueprint $table) {
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('urls', function (Blueprint $table) {
            $table->dropIndex(['expires_at']); // Drop the index on expires_at
        });

        Schema::dropIfExists('urls');
    }
};
