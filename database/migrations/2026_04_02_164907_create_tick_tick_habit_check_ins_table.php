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
        Schema::create('tick_tick_habit_check_ins', function (Blueprint $table) {
            $table->id();
            $table->string('ticktick_id')->unique();
            $table->foreignId('habit_id')->constrained('tick_tick_habits', 'ticktick_id')->cascadeOnDelete();
            $table->integer('checkin_stamp')->index();
            $table->datetime('checkin_time');
            $table->datetime('op_time');
            $table->decimal('value', 10, 2)->default(0);
            $table->decimal('goal', 10, 2)->default(0);
            $table->tinyInteger('status')->default(0)->index();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tick_tick_habit_check_ins');
    }
};
