<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Filament\Resources\Posts\PostResource;
use App\Models\Tag;
use App\Models\User;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\SpatieTagsEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;

class PostInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Post')
                    ->schema([
                        TextEntry::make('title'),
                        TextEntry::make('author.name')
                            ->label('Author'),
                        TextEntry::make('body')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Related records')
                    ->description('Every item below is styled from its own related record via $relatedRecord.')
                    ->schema([
                        SpatieTagsEntry::make('tags')
                            ->label('Frameworks (SpatieTagsEntry)')
                            ->type('framework')
                            ->color(fn (Tag $relatedRecord): array => Color::hex($relatedRecord->color))
                            ->icon(fn (Tag $relatedRecord): ?string => $relatedRecord->icon)
                            ->tooltip(fn (Tag $relatedRecord): string => "Type: {$relatedRecord->type}"),
                        TextEntry::make('tags.name')
                            ->label('All tags')
                            ->badge()
                            ->color(fn (Tag $relatedRecord): array => Color::hex($relatedRecord->color))
                            ->icon(fn (Tag $relatedRecord): ?string => $relatedRecord->icon)
                            ->tooltip(fn (Tag $relatedRecord): string => "Type: {$relatedRecord->type}")
                            ->url(fn (Tag $relatedRecord): string => PostResource::getUrl('index', [
                                'filters' => ['tag' => ['value' => $relatedRecord->getKey()]],
                            ])),
                        ImageEntry::make('contributors.avatar_url')
                            ->label('Contributors')
                            ->circular()
                            ->stacked()
                            ->tooltip(fn (User $relatedRecord): string => $relatedRecord->name)
                            ->url(fn (User $relatedRecord): string => "mailto:{$relatedRecord->email}"),
                        TextEntry::make('contributors.name')
                            ->label('Contributor emails')
                            ->listWithLineBreaks()
                            ->bulleted()
                            ->formatStateUsing(fn (string $state, User $relatedRecord): string => "{$state} <{$relatedRecord->email}>")
                            ->copyable()
                            ->copyableState(fn (User $relatedRecord): string => $relatedRecord->email),
                    ])
                    ->columns(2),
            ])
            ->columns(1);
    }
}
