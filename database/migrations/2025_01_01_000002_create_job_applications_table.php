<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->longText('cover_letter')->nullable();
            $table->string('linkedin_profile')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->integer('years_of_experience')->default(0);
            $table->string('education_level')->nullable();
            $table->decimal('expected_salary', 10, 2)->nullable();
            $table->string('location')->nullable();
            $table->date('availability_date')->nullable();
            $table->enum('status', ['applied', 'screening', 'shortlisted', 'interview', 'final_interview', 'offer', 'hired', 'rejected'])->default('applied');
            $table->decimal('match_score', 5, 2)->default(0);
            $table->decimal('ranking_score', 5, 2)->default(0);
            $table->longText('match_details')->nullable(); // JSON
            $table->dateTime('shortlisted_at')->nullable();
            $table->dateTime('rejected_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->boolean('cv_parsed')->default(false);
            $table->unique(['job_id', 'user_id']);
            $table->timestamps();
            $table->index('status');
            $table->index('match_score');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
