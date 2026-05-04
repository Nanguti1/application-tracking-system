<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications_log', function (Blueprint $table) {
            $table->id();
            $table->morphs('notifiable');
            $table->string('type');
            $table->string('notification_type'); // email, in_app, sms
            $table->string('recipient_email')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->string('subject')->nullable();
            $table->longText('body')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed', 'bounced'])->default('pending');
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();
            $table->index(['notifiable_type', 'notifiable_id']);
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications_log');
    }
};
