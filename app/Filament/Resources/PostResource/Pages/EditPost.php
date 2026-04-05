<?php

declare(strict_types=1);

namespace App\Filament\Resources\PostResource\Pages;

use App\Domain\Content\Services\PostService;
use App\Filament\Resources\PostResource;
use App\Models\Post;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): Post
    {
        return app(PostService::class)->update($record, $data);
    }
}
