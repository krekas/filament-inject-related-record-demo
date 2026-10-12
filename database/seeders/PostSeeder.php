<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Seed posts with colored tags and contributors.
     *
     * Two tags are deliberately named "Laravel" (with different types and colors) and
     * two contributors deliberately share the same avatar URL, to show cases where
     * looking up the related record by its state is impossible.
     */
    public function run(): void
    {
        $tags = collect([
            'laravel-framework' => ['name' => 'Laravel', 'type' => 'framework', 'color' => '#FF2D20', 'icon' => 'heroicon-m-code-bracket'],
            'laravel-community' => ['name' => 'Laravel', 'type' => 'community', 'color' => '#6366F1', 'icon' => 'heroicon-m-user-group'],
            'filament' => ['name' => 'Filament', 'type' => 'framework', 'color' => '#F59E0B', 'icon' => 'heroicon-m-sparkles'],
            'livewire' => ['name' => 'Livewire', 'type' => 'framework', 'color' => '#FB70A9', 'icon' => 'heroicon-m-bolt'],
            'php' => ['name' => 'PHP', 'type' => 'language', 'color' => '#777BB4', 'icon' => 'heroicon-m-command-line'],
            'tailwind' => ['name' => 'Tailwind CSS', 'type' => 'framework', 'color' => '#06B6D4', 'icon' => 'heroicon-m-paint-brush'],
            'release' => ['name' => 'Release', 'type' => 'topic', 'color' => '#10B981', 'icon' => 'heroicon-m-rocket-launch'],
            'tutorial' => ['name' => 'Tutorial', 'type' => 'topic', 'color' => '#8B5CF6', 'icon' => 'heroicon-m-academic-cap'],
        ])->map(fn (array $attributes): Tag => Tag::create($attributes));

        $sharedAvatarUrl = 'https://ui-avatars.com/api/?name=Core+Team&background=0D8ABC&color=fff';

        $contributors = collect([
            ['name' => 'Taylor Otwell', 'avatar_url' => 'https://ui-avatars.com/api/?name=Taylor+Otwell&background=FF2D20&color=fff'],
            ['name' => 'Dan Harrin', 'avatar_url' => 'https://ui-avatars.com/api/?name=Dan+Harrin&background=F59E0B&color=fff'],
            ['name' => 'Caleb Porzio', 'avatar_url' => 'https://ui-avatars.com/api/?name=Caleb+Porzio&background=FB70A9&color=fff'],
            ['name' => 'Core Team Bot', 'avatar_url' => $sharedAvatarUrl],
            ['name' => 'Release Bot', 'avatar_url' => $sharedAvatarUrl],
            ['name' => 'Anonymous Contributor', 'avatar_url' => null],
        ])->map(fn (array $attributes): User => User::factory()->create($attributes));

        $posts = [
            [
                'title' => 'Laravel 13 is out: what the community thinks',
                'tags' => ['laravel-framework', 'laravel-community', 'release'],
                'contributors' => [0, 3, 4],
            ],
            [
                'title' => 'Building admin panels with Filament',
                'tags' => ['filament', 'laravel-framework', 'tutorial'],
                'contributors' => [1, 5],
            ],
            [
                'title' => 'Livewire tips from the Laravel meetup',
                'tags' => ['livewire', 'laravel-community', 'laravel-framework', 'tutorial'],
                'contributors' => [2, 0, 5, 3],
            ],
            [
                'title' => 'Styling Filament themes with Tailwind CSS',
                'tags' => ['tailwind', 'filament'],
                'contributors' => [1],
            ],
            [
                'title' => 'Modern PHP features you should be using',
                'tags' => ['php', 'tutorial'],
                'contributors' => [4, 5, 3],
            ],
        ];

        foreach ($posts as $post) {
            $model = Post::factory()->create([
                'author_id' => $contributors->first()->getKey(),
                'title' => $post['title'],
            ]);

            $model->tags()->attach(collect($post['tags'])->map(fn (string $key): int => $tags[$key]->getKey()));
            $model->contributors()->attach(collect($post['contributors'])->map(fn (int $index): int => $contributors[$index]->getKey()));
        }
    }
}
