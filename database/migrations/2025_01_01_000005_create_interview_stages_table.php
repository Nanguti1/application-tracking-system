<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interview_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g., "CV Review", "Phone Screening", "Technical Interview"
            $table->integer('order')->default(0);
            $table->text('description')->nullable();
            $table->integer('duration_minutes')->default(60);
            $table->timestamps();
            $table->index(['job_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interview_stages');
    }
};
