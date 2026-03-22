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
        Schema::create('tick_tick_projects', function (Blueprint $table) {
            $table->id();
            $table->string('ticktick_id')->unique();
            $table->string('name');
            $table->string('color')->nullable();
            $table->string('view_mode')->nullable();
            $table->string('kind')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->bigInteger('sort_order')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tick_tick_projects');
    }
};
