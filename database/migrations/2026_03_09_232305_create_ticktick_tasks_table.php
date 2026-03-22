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
        Schema::create('tick_tick_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained('tick_tick_projects')->nullOnDelete();
            $table->string('ticktick_id')->unique();
            $table->string('title');
            $table->text('content')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(0)->index();
            $table->tinyInteger('priority')->default(0);
            $table->datetime('start_date')->nullable()->index();
            $table->datetime('due_date')->nullable()->index();
            $table->datetime('completed_time')->nullable();
            $table->string('timezone')->nullable();
            $table->boolean('is_all_day')->default(false);
            $table->bigInteger('sort_order')->nullable();
            $table->json('tags')->nullable();
            $table->json('items')->nullable();
            $table->string('repeat_flag')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tick_tick_tasks');
    }
};
