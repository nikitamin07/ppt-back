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

    protected $fillable = ['parent_id', 'name', 'slug', 'description', 'meta_description', 'position', 'show_calculator'];

    protected function casts(): array
    {
        return ['show_calculator' => 'boolean'];
    }

    /** Флаг живёт на корневой категории — подкатегория берёт его у родителя. */
    public function showsCalculator(): bool
    {
        return $this->parent_id === null
            ? $this->show_calculator
            : (bool) ($this->parent?->show_calculator ?? true);
    }

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
