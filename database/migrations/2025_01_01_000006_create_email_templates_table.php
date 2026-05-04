<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['rejection', 'shortlist', 'interview_invitation', 'interview_reschedule', 'offer', 'congratulations', 'regret'])->nullable();
            $table->string('subject');
            $table->longText('body');
            $table->text('placeholders_available')->nullable(); // JSON array of available placeholders
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->unique(['job_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
