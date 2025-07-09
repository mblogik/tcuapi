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
        Schema::create('tcu_api_logs', function (Blueprint $table) {
            $table->id();
            $table->string('endpoint', 255)->index();
            $table->string('method', 10)->default('POST');
            $table->json('request_headers')->nullable();
            $table->longText('request_body')->nullable();
            $table->integer('response_code')->nullable()->index();
            $table->json('response_headers')->nullable();
            $table->longText('response_body')->nullable();
            $table->decimal('execution_time', 8, 3)->nullable()->index();
            $table->string('username', 100)->nullable()->index();
            $table->string('session_id', 100)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->enum('status', ['pending', 'completed', 'error', 'timeout'])->default('pending')->index();
            $table->text('error_message')->nullable();
            $table->string('request_id', 100)->nullable()->unique();
            $table->timestamps();
            
            // Composite indexes for common queries
            $table->index(['endpoint', 'created_at']);
            $table->index(['username', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index(['response_code', 'created_at']);
            $table->index(['created_at', 'execution_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tcu_api_logs');
    }
};