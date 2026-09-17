<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mailings', function (Blueprint $table): void {
            $table->id();
            $table->string('subject');
            $table->text('body');
            $table->json('attachments')->nullable();
            $table->boolean('send_to_all')->default(true);
            $table->string('status')->default('sending'); // sending | paused | sent
            $table->unsignedInteger('total')->default(0);
            $table->unsignedInteger('sent_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
            $table->boolean('paused_by_window')->default(false);
            $table->boolean('window_override')->default(false);
        });


        Schema::create('mailing_deliveries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('mailing_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->foreignId('recipient_id')->nullable()->constrained('mailing_recipients')->nullOnDelete();
            $table->string('status')->default('pending'); // pending | sent | delivered | bounced | complained | failed
            $table->text('error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->string('provider_message_id')->nullable();
            $table->index(['mailing_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mailing_deliveries');
        Schema::dropIfExists('mailings');
    }
};
