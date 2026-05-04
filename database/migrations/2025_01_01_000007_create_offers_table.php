<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->constrained()->cascadeOnDelete();
            $table->decimal('offered_salary', 10, 2);
            $table->string('position_title');
            $table->date('start_date');
            $table->text('offer_letter')->nullable();
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected', 'expired'])->default('draft');
            $table->dateTime('sent_at')->nullable();
            $table->dateTime('accepted_at')->nullable();
            $table->dateTime('rejected_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
