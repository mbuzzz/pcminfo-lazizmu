<?php

declare(strict_types=1);

namespace App\Filament\Resources\PostResource\Pages;

use App\Domain\Content\Services\PostService;
use App\Filament\Resources\PostResource;
use App\Models\Post;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['author_id'] ??= auth()->id();

        return $data;
    }

    protected function handleRecordCreation(array $data): Post
    {
        return app(PostService::class)->create($this->mutateFormDataBeforeCreate($data));
    }
}
