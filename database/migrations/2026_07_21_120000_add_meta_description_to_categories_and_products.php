<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Отдельный текст под <meta name="description"> — короче обычного описания (~160 символов).
    public function up(): void
    {
        foreach (['categories', 'products'] as $name) {
            Schema::table($name, function (Blueprint $table): void {
                $table->string('meta_description')->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach (['categories', 'products'] as $name) {
            Schema::table($name, function (Blueprint $table): void {
                $table->dropColumn('meta_description');
            });
        }
    }
};
