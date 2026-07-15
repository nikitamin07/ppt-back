<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Дефолтный сид пуст намеренно: реальные данные живут в БД (бэкап —
     * pg_dump вне репозитория), админ-юзеры создаются через make:filament-user.
     *
     * Фейковые данные для ручной проверки UI: php artisan db:seed --class=DemoDataSeeder
     */
    public function run(): void
    {
        //
    }
}
