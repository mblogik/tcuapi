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
        Schema::create('tcu_api_rate_limits', function (Blueprint $table) {
            $table->id();
            $table->string('username', 100)->index();
            $table->string('endpoint', 255)->index();
            $table->string('ip_address', 45)->nullable();
            $table->integer('requests_count')->default(1);
            $table->integer('requests_limit')->default(100);
            $table->timestamp('window_start')->useCurrent();
            $table->timestamp('window_end')->index();
            $table->boolean('is_blocked')->default(false)->index();
            $table->timestamp('blocked_until')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            // Composite indexes for rate limiting queries
            $table->index(['username', 'endpoint', 'window_end']);
            $table->index(['ip_address', 'window_end']);
            $table->index(['is_blocked', 'blocked_until']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tcu_api_rate_limits');
    }
};