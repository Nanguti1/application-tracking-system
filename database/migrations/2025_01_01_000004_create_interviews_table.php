<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('interview_stage_id')->constrained()->cascadeOnDelete();
            $table->dateTime('scheduled_at');
            $table->integer('duration_minutes')->default(60);
            $table->string('interview_type')->nullable(); // e.g., "phone", "video", "in-person"
            $table->string('interviewer_name')->nullable();
            $table->string('interviewer_email')->nullable();
            $table->string('location')->nullable();
            $table->string('meeting_link')->nullable();
            $table->enum('status', ['scheduled', 'rescheduled', 'completed', 'no_show', 'cancelled'])->default('scheduled');
            $table->text('feedback')->nullable();
            $table->decimal('interviewer_score', 5, 2)->nullable();
            $table->dateTime('rescheduled_at')->nullable();
            $table->text('reschedule_reason')->nullable();
            $table->timestamps();
            $table->index(['job_application_id', 'scheduled_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
