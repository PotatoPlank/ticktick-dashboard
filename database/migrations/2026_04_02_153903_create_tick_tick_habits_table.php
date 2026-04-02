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
        Schema::create('tick_tick_habits', function (Blueprint $table) {
            $table->id();
            $table->string('ticktick_id')->unique();
            $table->string('name');
            $table->string('color')->nullable();
            $table->tinyInteger('status')->default(0)->index();
            $table->string('type')->nullable();
            $table->integer('goal')->nullable();
            $table->integer('step')->nullable();
            $table->string('unit')->nullable();
            $table->string('repeat_rule')->nullable();
            $table->string('encouragement')->nullable();
            $table->string('reminders')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tick_tick_habits');
    }
};
