<?php

declare(strict_types=1);

namespace App\Filament\Resources\Pages\Pages;

use App\Enums\PageStatus;
use App\Filament\Resources\Pages\PageResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = Auth::id();

        if (($data['status'] ?? null) === PageStatus::Published->value && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('Preview')
                ->icon('heroicon-o-eye')
                ->url(fn (): string => route('pages.show', ['page' => $this->record, 'preview' => 1]))
                ->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }
}
