<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Select::make('author_id')
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                SpatieTagsInput::make('tags'),
                Select::make('contributors')
                    ->relationship('contributors', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Textarea::make('body')
                    ->required()
                    ->rows(6)
                    ->columnSpanFull(),
            ]);
    }
}
