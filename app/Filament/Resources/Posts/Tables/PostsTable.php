<?php

namespace App\Filament\Resources\Posts\Tables;

use App\Filament\Resources\Posts\PostResource;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SpatieTagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->wrap(),

                /**
                 * Official Spatie tags plugin: every badge is styled from its own Tag model.
                 */
                SpatieTagsColumn::make('tags')
                    ->label('Frameworks (SpatieTagsColumn)')
                    ->type('framework')
                    ->color(fn (Tag $relatedRecord): array => Color::hex($relatedRecord->color))
                    ->icon(fn (Tag $relatedRecord): ?string => $relatedRecord->icon)
                    ->tooltip(fn (Tag $relatedRecord): string => "Type: {$relatedRecord->type}"),

                /**
                 * Before: the workaround from the issue looks the tag up by its name. Both
                 * "Laravel" tags resolve to the first match, so they get the same color.
                 */
                // TextColumn::make('tag_names_before')
                //     ->label('Tags: before (firstWhere workaround)')
                //     ->state(fn (Post $record): array => $record->tags->pluck('name')->all())
                //     ->badge()
                //     ->color(fn (Post $record, string $state): array => Color::hex($record->tags->firstWhere('name', $state)->color))
                //     ->tooltip(fn (Post $record, string $state): string => "Type: {$record->tags->firstWhere('name', $state)->type}"),

                /**
                 * After: `$relatedRecord` is the exact Tag behind each badge.
                 */
                // TextColumn::make('tags.name')
                //     ->label('Tags: after ($relatedRecord)')
                //     ->badge()
                //     ->color(fn (Tag $relatedRecord): array => Color::hex($relatedRecord->color))
                //     ->icon(fn (Tag $relatedRecord): ?string => $relatedRecord->icon)
                //     ->tooltip(fn (Tag $relatedRecord): string => "Type: {$relatedRecord->type}")
                //     ->url(fn (Tag $relatedRecord): string => PostResource::getUrl('index', [
                //         'filters' => ['tag' => ['value' => $relatedRecord->getKey()]],
                //     ])),

                /**
                 * Before: two contributors share an avatar URL, so looking them up by
                 * the image URL shows the wrong name for one of them.
                 */
                ImageColumn::make('contributor_avatars_before')
                    ->label('Contributors: before')
                    ->state(fn (Post $record): array => $record->contributors->pluck('avatar_url')->filter()->values()->all())
                    ->circular()
                    ->stacked()
                    ->tooltip(fn (Post $record, ?string $state): ?string => $record->contributors->firstWhere('avatar_url', $state)?->name),

                /**
                 * After: each avatar gets its own User, including ones without an avatar.
                 */
                // ImageColumn::make('contributors.avatar_url')
                //     ->label('Contributors: after ($relatedRecord)')
                //     ->circular()
                //     ->stacked()
                //     ->tooltip(fn (User $relatedRecord): string => $relatedRecord->name)
                //     ->url(fn (User $relatedRecord): string => "mailto:{$relatedRecord->email}"),
            ])
            ->filters([
                SelectFilter::make('tag')
                    ->options(fn (): array => Tag::query()
                        ->get()
                        ->mapWithKeys(fn (Tag $tag): array => [$tag->getKey() => "{$tag->name} ({$tag->type})"])
                        ->all())
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'],
                        fn (Builder $query, string $tagId): Builder => $query->whereHas('tags', fn (Builder $query): Builder => $query->whereKey($tagId)),
                    )),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
