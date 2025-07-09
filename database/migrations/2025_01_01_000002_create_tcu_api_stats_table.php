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
        Schema::create('tcu_api_stats', function (Blueprint $table) {
            $table->id();
            $table->string('endpoint', 255)->index();
            $table->string('username', 100)->nullable()->index();
            $table->date('date')->index();
            $table->integer('total_calls')->default(0);
            $table->integer('successful_calls')->default(0);
            $table->integer('failed_calls')->default(0);
            $table->integer('timeout_calls')->default(0);
            $table->decimal('avg_execution_time', 8, 3)->nullable();
            $table->decimal('max_execution_time', 8, 3)->nullable();
            $table->decimal('min_execution_time', 8, 3)->nullable();
            $table->integer('total_data_sent')->default(0); // bytes
            $table->integer('total_data_received')->default(0); // bytes
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            // Unique constraint to prevent duplicate daily stats
            $table->unique(['endpoint', 'username', 'date']);
            
            // Composite indexes for reporting
            $table->index(['date', 'endpoint']);
            $table->index(['username', 'date']);
            $table->index(['endpoint', 'date', 'successful_calls']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tcu_api_stats');
    }
};