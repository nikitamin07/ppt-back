<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Получатели автоматической рассылки
        Schema::create('mailing_recipients', function (Blueprint $table): void {
            $table->id();
            $table->string('email')->unique();
            $table->boolean('is_active')->default(true);
            // Текст последней ошибки доставки
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mailing_recipients');
    }
};
