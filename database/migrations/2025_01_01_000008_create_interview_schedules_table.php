<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interview_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->cascadeOnDelete();
            $table->date('interview_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->json('breaks')->nullable(); // Array of break times
            $table->integer('slot_duration_minutes')->default(60);
            $table->timestamps();
            $table->index(['job_id', 'interview_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interview_schedules');
    }
};
