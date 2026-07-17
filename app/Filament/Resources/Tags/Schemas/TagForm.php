<?php

namespace App\Filament\Resources\Tags\Schemas;

use App\Filament\Resources\Concerns\SlugFields;
use Filament\Schemas\Schema;

class TagForm
{
    use SlugFields;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                self::titleField('name', 'Название'),
                self::slugField(),
            ]);
    }
}
