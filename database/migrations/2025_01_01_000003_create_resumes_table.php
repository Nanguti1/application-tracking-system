<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resumes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->constrained()->cascadeOnDelete();
            $table->string('original_filename');
            $table->string('file_path');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->longText('raw_text')->nullable();
            $table->json('parsed_data')->nullable(); // Contains: skills, experience, education, certifications
            $table->json('extracted_email')->nullable();
            $table->json('extracted_phone')->nullable();
            $table->timestamps();
            $table->index('job_application_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resumes');
    }
};
