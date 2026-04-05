<?php

declare(strict_types=1);

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Domain\Content\Services\CategoryService;
use App\Filament\Resources\CategoryResource;
use App\Models\Category;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): Category
    {
        return app(CategoryService::class)->update($record, $data);
    }
}
