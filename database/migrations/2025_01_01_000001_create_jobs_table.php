<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('department');
            $table->string('location');
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'temporary', 'internship'])->default('full_time');
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->longText('description');
            $table->longText('requirements');
            $table->dateTime('deadline_at')->nullable();
            $table->dateTime('interview_date_start')->nullable();
            $table->dateTime('interview_date_end')->nullable();
            $table->integer('candidates_to_shortlist')->default(10);
            $table->enum('interview_type', ['fixed', 'rolling'])->default('fixed');
            $table->enum('status', ['draft', 'published', 'closed', 'interviewing', 'offer_stage', 'filled', 'archived'])->default('draft');
            $table->integer('applications_count')->default(0);
            $table->integer('shortlisted_count')->default(0);
            $table->integer('rejected_count')->default(0);
            $table->timestamps();
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
