<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->unsignedSmallInteger('featured_position')->nullable();
        });

        // Уже популярные товары получают порядок, в котором их отдавал API до этой миграции.
        $ids = DB::table('products')->where('is_featured', true)->orderBy('id')->pluck('id');

        foreach ($ids as $position => $id) {
            DB::table('products')->where('id', $id)->update(['featured_position' => $position + 1]);
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn('featured_position');
        });
    }
};
