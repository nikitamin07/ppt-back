<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
final class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => mb_ucfirst(fake()->words(5, true)),
            'slug' => fake()->unique()->slug(4),
            'excerpt' => fake()->sentence(12),
            'content' => '<p>'.implode('</p><p>', fake()->paragraphs(4)).'</p>',
            'cover_image' => null,
            'published_at' => fake()->dateTimeBetween('-1 year'),
        ];
    }

    public function draft(): static
    {
        return $this->state(['published_at' => null]);
    }
}
