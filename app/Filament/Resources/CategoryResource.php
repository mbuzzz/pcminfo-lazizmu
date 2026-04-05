<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\Concerns\HasResourceAuthorization;
use App\Models\Category;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class CategoryResource extends Resource
{
    use HasResourceAuthorization;

    protected static ?string $model = Category::class;

    protected static string $permission = 'manage_categories';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Kategori';

    protected static ?string $pluralModelLabel = 'Kategori';

    protected static ?string $navigationLabel = 'Kategori';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('parent_id')
                ->label('Kategori Induk')
                ->options(Category::query()->orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->preload(),
            Forms\Components\TextInput::make('name')
                ->label('Nama Kategori')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, \Filament\Schemas\Components\Utilities\Set $set) => $set('slug', Str::slug((string) $state))),
            Forms\Components\TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->unique(ignoreRecord: true),
            Forms\Components\Select::make('type')
                ->label('Jenis Kategori')
                ->options([
                    'post' => 'Artikel',
                    'campaign' => 'Program Donasi',
                    'agenda' => 'Agenda',
                    'distribution' => 'Penyaluran',
                    'general' => 'Umum',
                ])
                ->required(),
            Forms\Components\Textarea::make('description')
                ->label('Deskripsi')
                ->rows(3),
            Forms\Components\TextInput::make('icon')
                ->label('Ikon'),
            Forms\Components\ColorPicker::make('color')
                ->label('Warna'),
            Forms\Components\TextInput::make('order')
                ->label('Urutan')
                ->numeric()
                ->default(0),
            Forms\Components\Toggle::make('is_active')
                ->label('Aktif')
                ->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('type')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Category $record): string => $record->parent?->name ?? 'Kategori utama'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->badge(),
                Tables\Columns\ColorColumn::make('color')
                    ->label('Warna'),
                Tables\Columns\TextColumn::make('order')
                    ->label('Urutan')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'post' => 'Artikel',
                        'campaign' => 'Program Donasi',
                        'agenda' => 'Agenda',
                        'distribution' => 'Penyaluran',
                        'general' => 'Umum',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make()
                    ->label('Ubah'),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make()
                        ->label('Hapus yang dipilih'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
