<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Database\Seeder;

/**
 * Фейковый каталог и блог для ручной проверки UI. Запускается только явно:
 * php artisan db:seed --class=DemoDataSeeder
 */
final class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command->error('DemoDataSeeder запрещён в production.');

            return;
        }

        if (Product::exists() || Post::exists()) {
            $this->command->error('В базе уже есть товары или посты — демо-данные поверх реальных не сеем.');

            return;
        }

        Category::factory(3)->create()->each(function (Category $root): void {
            Category::factory(3)->childOf($root)->create()->each(function (Category $sub): void {
                Product::factory(4)->create(['category_id' => $sub->id]);
            });
        });

        $tags = Tag::factory(5)->create();

        Post::factory(8)->create()->each(fn (Post $post) => $post->tags()->attach($tags->random(2)));
        Post::factory(2)->draft()->create();
    }
}
