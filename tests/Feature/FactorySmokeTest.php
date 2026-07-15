<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Attribute;
use App\Models\Post;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class FactorySmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_factory_builds_product_with_category_and_attributes(): void
    {
        $product = Product::factory()->create();

        $this->assertNotNull($product->category);
        $this->assertTrue($product->is_active);

        $attribute = Attribute::create(['name' => 'Плотность', 'slug' => 'plotnost']);
        $product->attributes()->attach($attribute, ['value' => '15 кг/м3', 'position' => 0]);

        $this->assertSame('15 кг/м3', $product->fresh()->attributes->first()->pivot->value);
    }

    public function test_published_scope_separates_drafts(): void
    {
        Post::factory()->create();
        Post::factory()->draft()->create();

        $this->assertSame(2, Post::count());
        $this->assertSame(1, Post::published()->count());
    }

    public function test_post_tags_attach(): void
    {
        $post = Post::factory()->create();
        $post->tags()->attach(Tag::factory(2)->create());

        $this->assertCount(2, $post->fresh()->tags);
    }
}
