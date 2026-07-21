<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

final class Category extends Model
{
    use HasFactory;

    protected $fillable = ['parent_id', 'name', 'slug', 'description', 'meta_description', 'position'];

    protected static function booted(): void
    {
        // Новая категория встаёт в конец списка, а не в начало (position по умолчанию 0)
        static::creating(function (self $category): void {
            $category->position ??= (int) self::max('position') + 1;
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Id категорий с такими слагами плюс, для корневых, id их подкатегорий:
     * фильтр по корню отдаёт и товары его детей.
     *
     * @param  array<string>  $slugs
     * @return Collection<int, int>
     */
    public static function idsBySlugs(array $slugs): Collection
    {
        return self::query()
            ->whereIn('slug', $slugs)
            ->orWhereHas('parent', fn (Builder $query) => $query->whereIn('slug', $slugs))
            ->pluck('id');
    }
}
