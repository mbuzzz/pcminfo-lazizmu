<?php

declare(strict_types=1);

namespace App\Filament\Resources\Pages\Schemas;

use App\Enums\PageStatus;
use App\Filament\Support\EnumOptions;
use Filament\Forms;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            \Filament\Schemas\Components\Tabs::make('PageTabs')
                ->tabs([
                    \Filament\Schemas\Components\Tabs\Tab::make('Konten')
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->label('Judul')
                                ->required()
                                ->live(onBlur: true)
                                ->maxLength(255)
                                ->afterStateUpdated(function ($state, \Filament\Schemas\Components\Utilities\Get $get, \Filament\Schemas\Components\Utilities\Set $set): void {
                                    if (filled($get('slug'))) {
                                        return;
                                    }

                                    $set('slug', Str::slug((string) $state));
                                }),
                            Forms\Components\TextInput::make('slug')
                                ->label('Slug')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->helperText('Dipakai sebagai URL. Contoh: tentang, kontak, pedoman-donasi.'),
                            \Filament\Schemas\Components\Grid::make(12)
                                ->schema([
                                    Forms\Components\Select::make('status')
                                        ->label('Status')
                                        ->options(EnumOptions::make(PageStatus::class))
                                        ->required()
                                        ->default(PageStatus::Draft->value)
                                        ->columnSpan(4),
                                    Forms\Components\DateTimePicker::make('published_at')
                                        ->label('Jadwal Publish')
                                        ->columnSpan(4),
                                ]),
                            Forms\Components\Textarea::make('excerpt')
                                ->label('Ringkasan')
                                ->rows(3),
                            Forms\Components\RichEditor::make('content')
                                ->label('Konten')
                                ->columnSpanFull(),
                        ]),
                    \Filament\Schemas\Components\Tabs\Tab::make('SEO')
                        ->schema([
                            Forms\Components\TextInput::make('meta_title')
                                ->label('Meta Title')
                                ->helperText('Jika dikosongkan, sistem akan memakai judul halaman.'),
                            Forms\Components\Textarea::make('meta_description')
                                ->label('Meta Description')
                                ->rows(3),
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }
}
