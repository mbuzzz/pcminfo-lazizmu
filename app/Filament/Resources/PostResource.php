<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domain\Content\Services\PostService;
use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Filament\Resources\Concerns\HasResourceAuthorization;
use App\Filament\Resources\PostResource\Pages;
use App\Filament\Support\EnumOptions;
use App\Models\Category;
use App\Models\Institution;
use App\Models\Post;
use App\Models\User;
use App\Services\Media\MediaUploadService;
use App\Support\Media\MediaPath;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class PostResource extends Resource
{
    use HasResourceAuthorization;

    protected static ?string $model = Post::class;

    protected static string $permission = 'manage_articles';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-newspaper';

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Artikel';

    protected static ?string $pluralModelLabel = 'Artikel';

    protected static ?string $navigationLabel = 'Artikel';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            \Filament\Schemas\Components\Tabs::make('PostTabs')
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
                                ->helperText('Slug akan dibuat otomatis dari judul, tetapi tetap bisa diubah manual.')
                                ->unique(ignoreRecord: true),
                            \Filament\Schemas\Components\Grid::make(12)
                                ->schema([
                                    Forms\Components\Select::make('category_id')
                                        ->label('Kategori')
                                        ->options(Category::query()->where('type', 'post')->orderBy('name')->pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->columnSpan(4),
                                    Forms\Components\Select::make('institution_id')
                                        ->label('Amal Usaha')
                                        ->options(Institution::query()->orderBy('name')->pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->columnSpan(4),
                                    Forms\Components\Select::make('author_id')
                                        ->label('Penulis')
                                        ->options(User::query()->orderBy('name')->pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->default(auth()->id())
                                        ->columnSpan(4),
                                    Forms\Components\Select::make('type')
                                        ->label('Jenis Konten')
                                        ->options(EnumOptions::make(PostType::class))
                                        ->required()
                                        ->columnSpan(4),
                                    Forms\Components\Select::make('status')
                                        ->label('Status Publikasi')
                                        ->options(EnumOptions::make(PostStatus::class))
                                        ->required()
                                        ->columnSpan(4),
                                    Forms\Components\DateTimePicker::make('published_at')
                                        ->label('Jadwal Publish')
                                        ->columnSpan(4),
                                ]),
                            Forms\Components\FileUpload::make('featured_image')
                                ->label('Gambar Utama')
                                ->image()
                                ->disk(config('media.disk'))
                                ->directory(MediaPath::postFeatured())
                                ->visibility(config('media.visibility'))
                                ->acceptedFileTypes(config('media.accepted_image_types'))
                                ->maxSize((int) config('media.max_sizes_kb.image'))
                                ->getUploadedFileNameForStorageUsing(fn (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file) => app(MediaUploadService::class)->generateFilename($file, 'post-featured'))
                                ->imageEditor()
                                ->openable()
                                ->downloadable(),
                            Forms\Components\Textarea::make('excerpt')
                                ->label('Ringkasan')
                                ->rows(3)
                                ->helperText('Ringkasan singkat ini dipakai untuk kartu berita, preview, dan fallback SEO.'),
                            Forms\Components\RichEditor::make('content')
                                ->label('Isi Konten')
                                ->required()
                                ->fileAttachmentsDisk(config('media.disk'))
                                ->fileAttachmentsDirectory(MediaPath::postContent())
                                ->fileAttachmentsVisibility(config('media.visibility'))
                                ->toolbarButtons([
                                    'attachFiles',
                                    'blockquote',
                                    'bold',
                                    'bulletList',
                                    'codeBlock',
                                    'h2',
                                    'h3',
                                    'italic',
                                    'link',
                                    'orderedList',
                                    'redo',
                                    'strike',
                                    'underline',
                                    'undo',
                                ])
                                ->helperText('Anda dapat menambahkan gambar dan mengatur posisinya dengan klik pada gambar.')
                                ->columnSpanFull(),
                        ]),
                    \Filament\Schemas\Components\Tabs\Tab::make('SEO & Opsi')
                        ->schema([
                            Forms\Components\TextInput::make('meta_title')
                                ->label('Meta Title')
                                ->helperText('Jika dikosongkan, sistem akan memakai judul artikel.'),
                            Forms\Components\Textarea::make('meta_description')
                                ->label('Meta Description')
                                ->rows(3)
                                ->helperText('Jika dikosongkan, sistem akan memakai ringkasan artikel.'),
                            Forms\Components\Toggle::make('is_featured')
                                ->label('Tandai sebagai konten unggulan'),
                            Forms\Components\Toggle::make('allow_comments')
                                ->label('Izinkan komentar')
                                ->default(true),
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Post $record): string => $record->category?->name ?? 'Tanpa kategori'),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable(),
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Gambar')
                    ->disk(config('media.disk'))
                    ->square(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                Tables\Columns\TextColumn::make('author.name')
                    ->label('Penulis')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('Belum dijadwalkan'),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
                Tables\Columns\TextColumn::make('view_count')
                    ->label('Dilihat')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(EnumOptions::make(PostType::class)),
                Tables\Filters\SelectFilter::make('status')
                    ->options(EnumOptions::make(PostStatus::class)),
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->options(Category::query()->where('type', 'post')->orderBy('name')->pluck('name', 'id')),
                Tables\Filters\TernaryFilter::make('is_featured'),
            ])
            ->defaultSort('published_at', 'desc')
            ->searchPlaceholder('Cari judul, slug, atau penulis')
            ->actions([
                ActionGroup::make([
                    \Filament\Actions\EditAction::make()
                        ->label('Ubah'),
                    \Filament\Actions\Action::make('submitReview')
                        ->label('Kirim Review')
                        ->color('warning')
                        ->visible(fn (Post $record): bool => $record->status === PostStatus::Draft)
                        ->action(fn (Post $record) => app(PostService::class)->submitReview($record)),
                    \Filament\Actions\Action::make('publish')
                        ->label('Publikasikan')
                        ->color('success')
                        ->visible(fn (Post $record): bool => in_array($record->status, [PostStatus::Draft, PostStatus::Review], true))
                        ->action(fn (Post $record) => app(PostService::class)->publish($record, auth()->user())),
                    \Filament\Actions\Action::make('archive')
                        ->label('Arsipkan')
                        ->color('gray')
                        ->visible(fn (Post $record): bool => $record->status !== PostStatus::Archived)
                        ->action(fn (Post $record) => app(PostService::class)->archive($record)),
                ]),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
