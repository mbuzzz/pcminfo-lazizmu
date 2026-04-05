<?php

declare(strict_types=1);

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Domain\Content\Services\CategoryService;
use App\Filament\Resources\CategoryResource;
use App\Models\Category;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected function handleRecordCreation(array $data): Category
    {
        return app(CategoryService::class)->create($data);
    }
}
