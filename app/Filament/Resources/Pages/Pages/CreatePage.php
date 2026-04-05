<?php

declare(strict_types=1);

namespace App\Filament\Resources\Pages\Pages;

use App\Enums\PageStatus;
use App\Filament\Resources\Pages\PageResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] ??= Auth::id();
        $data['updated_by'] ??= Auth::id();

        if (($data['status'] ?? null) === PageStatus::Published->value && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $data;
    }
}
