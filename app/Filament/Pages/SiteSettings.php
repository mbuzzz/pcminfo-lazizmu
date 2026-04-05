<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Domain\Setting\Services\SiteSettingService;
use App\Services\Media\MediaUploadService;
use App\Support\Media\MediaPath;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use UnitEnum;

final class SiteSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Pengaturan Situs';

    protected static ?string $title = 'Pengaturan Situs';

    protected static ?string $slug = 'site-settings';

    /**
     * @var array<string, mixed>
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->getSchema('form')->fill(app(SiteSettingService::class)->all());
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user?->can('view_settings')
            || $user?->can('update_settings')
            || $user?->can('manage_settings')
            || false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Tabs::make('siteSettingsTabs')
                    ->tabs([
                        \Filament\Schemas\Components\Tabs\Tab::make('Identitas')
                            ->schema([
                                \Filament\Schemas\Components\Section::make('Identitas Website')
                                    ->schema([
                                        Forms\Components\TextInput::make('site_name')
                                            ->label('Nama Situs')
                                            ->required()
                                            ->maxLength(150),
                                        Forms\Components\TextInput::make('site_tagline')
                                            ->label('Slogan Situs')
                                            ->maxLength(150),
                                        Forms\Components\Textarea::make('site_description')
                                            ->label('Deskripsi Situs')
                                            ->rows(4)
                                            ->columnSpanFull(),
                                        Forms\Components\FileUpload::make('logo')
                                            ->label('Logo')
                                            ->image()
                                            ->disk(config('media.disk'))
                                            ->directory(MediaPath::siteLogo())
                                            ->visibility(config('media.visibility'))
                                            ->acceptedFileTypes(config('media.accepted_image_types'))
                                            ->maxSize((int) config('media.max_sizes_kb.image'))
                                            ->getUploadedFileNameForStorageUsing(
                                                fn (TemporaryUploadedFile $file): string => app(MediaUploadService::class)->generateFilename($file, 'site-logo')
                                            )
                                            ->imageEditor()
                                            ->openable()
                                            ->downloadable(),
                                        Forms\Components\FileUpload::make('favicon')
                                            ->label('Ikon Situs')
                                            ->disk(config('media.disk'))
                                            ->directory(MediaPath::siteFavicon())
                                            ->visibility(config('media.visibility'))
                                            ->acceptedFileTypes([
                                                'image/png',
                                                'image/jpeg',
                                                'image/webp',
                                                'image/svg+xml',
                                                'image/x-icon',
                                                'image/vnd.microsoft.icon',
                                            ])
                                            ->maxSize(1024)
                                            ->helperText('Disarankan PNG atau ICO dengan ukuran kecil.')
                                            ->getUploadedFileNameForStorageUsing(
                                                fn (TemporaryUploadedFile $file): string => app(MediaUploadService::class)->generateFilename($file, 'site-favicon')
                                            )
                                            ->openable()
                                            ->downloadable(),
                                    ])
                                    ->columns(2),
                            ]),
                        \Filament\Schemas\Components\Tabs\Tab::make('Kontak')
                            ->schema([
                                \Filament\Schemas\Components\Section::make('Informasi Kontak')
                                    ->schema([
                                        Forms\Components\TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->maxLength(150),
                                        Forms\Components\TextInput::make('phone')
                                            ->label('Telepon')
                                            ->tel()
                                            ->maxLength(30),
                                        Forms\Components\TextInput::make('whatsapp_number')
                                            ->label('Nomor WhatsApp')
                                            ->tel()
                                            ->helperText('Gunakan format internasional, misalnya 62812xxxxxx.')
                                            ->maxLength(30),
                                        Forms\Components\TextInput::make('google_maps_url')
                                            ->label('URL Google Maps')
                                            ->url()
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('address')
                                            ->label('Alamat')
                                            ->rows(4)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),
                        \Filament\Schemas\Components\Tabs\Tab::make('Sosial')
                            ->schema([
                                \Filament\Schemas\Components\Section::make('Media Sosial')
                                    ->schema([
                                        Forms\Components\TextInput::make('instagram')
                                            ->label('Instagram')
                                            ->url(),
                                        Forms\Components\TextInput::make('facebook')
                                            ->label('Facebook')
                                            ->url(),
                                        Forms\Components\TextInput::make('youtube')
                                            ->label('Youtube')
                                            ->url(),
                                        Forms\Components\TextInput::make('tiktok')
                                            ->label('Tiktok')
                                            ->url(),
                                    ])
                                    ->columns(2),
                            ]),
                        \Filament\Schemas\Components\Tabs\Tab::make('Donasi')
                            ->schema([
                                \Filament\Schemas\Components\Section::make('Pengaturan Donasi Global')
                                    ->schema([
                                        Forms\Components\FileUpload::make('qris_image')
                                            ->label('Gambar QRIS Default')
                                            ->image()
                                            ->disk(config('media.disk'))
                                            ->directory(MediaPath::donationQris())
                                            ->visibility(config('media.visibility'))
                                            ->acceptedFileTypes(config('media.accepted_image_types'))
                                            ->maxSize((int) config('media.max_sizes_kb.image'))
                                            ->getUploadedFileNameForStorageUsing(
                                                fn (TemporaryUploadedFile $file): string => app(MediaUploadService::class)->generateFilename($file, 'global-qris')
                                            )
                                            ->imageEditor()
                                            ->openable()
                                            ->downloadable()
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('donation_whatsapp_number')
                                            ->label('Nomor WhatsApp Donasi')
                                            ->tel()
                                            ->maxLength(30),
                                        Forms\Components\Textarea::make('donation_whatsapp_message_template')
                                            ->label('Template Pesan WhatsApp Donasi')
                                            ->rows(4)
                                            ->helperText('Contoh placeholder: :campaign_title')
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('donation_instruction_text')
                                            ->label('Instruksi Donasi Default')
                                            ->rows(5)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),
                        \Filament\Schemas\Components\Tabs\Tab::make('SEO')
                            ->schema([
                                \Filament\Schemas\Components\Section::make('SEO Global')
                                    ->schema([
                                        Forms\Components\TextInput::make('default_meta_title')
                                            ->label('Meta Title Bawaan')
                                            ->maxLength(160),
                                        Forms\Components\Textarea::make('default_meta_description')
                                            ->label('Meta Description Bawaan')
                                            ->rows(4)
                                            ->columnSpanFull(),
                                        Forms\Components\FileUpload::make('default_og_image')
                                            ->label('Gambar Open Graph Bawaan')
                                            ->image()
                                            ->disk(config('media.disk'))
                                            ->directory(MediaPath::seoDefaultOg())
                                            ->visibility(config('media.visibility'))
                                            ->acceptedFileTypes(config('media.accepted_image_types'))
                                            ->maxSize((int) config('media.max_sizes_kb.image'))
                                            ->getUploadedFileNameForStorageUsing(
                                                fn (TemporaryUploadedFile $file): string => app(MediaUploadService::class)->generateFilename($file, 'default-og-image')
                                            )
                                            ->imageEditor()
                                            ->openable()
                                            ->downloadable()
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('site_verification_code')
                                            ->label('Kode Verifikasi Situs')
                                            ->rows(4)
                                            ->helperText('Opsional. Dapat diisi meta verification dari Google atau platform lain.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),
                        \Filament\Schemas\Components\Tabs\Tab::make('Footer')
                            ->schema([
                                \Filament\Schemas\Components\Section::make('Footer Website')
                                    ->schema([
                                        Forms\Components\Textarea::make('footer_description')
                                            ->label('Deskripsi Footer')
                                            ->rows(4)
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('footer_copyright')
                                            ->label('Hak Cipta Footer')
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                        Forms\Components\Repeater::make('footer_links')
                                            ->label('Tautan Footer')
                                            ->schema([
                                                Forms\Components\TextInput::make('label')
                                                    ->label('Label')
                                                    ->required()
                                                    ->maxLength(100),
                                                Forms\Components\TextInput::make('url')
                                                    ->label('URL')
                                                    ->required(),
                                            ])
                                            ->columns(2)
                                            ->default([])
                                            ->reorderable()
                                            ->collapsible()
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        \Filament\Schemas\Components\Tabs\Tab::make('Tampilan')
                            ->schema([
                                \Filament\Schemas\Components\Section::make('Tema dan CTA')
                                    ->schema([
                                        Forms\Components\ColorPicker::make('primary_color')
                                            ->label('Warna Primer')
                                            ->required(),
                                        Forms\Components\ColorPicker::make('secondary_color')
                                            ->label('Warna Sekunder')
                                            ->required(),
                                        Forms\Components\ColorPicker::make('accent_color')
                                            ->label('Warna Aksen')
                                            ->required(),
                                        Forms\Components\TextInput::make('default_cta_text')
                                            ->label('Teks Tombol Aksi Bawaan')
                                            ->required()
                                            ->maxLength(100)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(3),
                                \Filament\Schemas\Components\Section::make('Box: Nilai Gerakan (Dark)')
                                    ->schema([
                                        Forms\Components\TextInput::make('homepage_feature_badge')
                                            ->label('Label/Badge Bukaan')
                                            ->maxLength(80),
                                        Forms\Components\Textarea::make('homepage_feature_title')
                                            ->label('Judul Tagline')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('homepage_feature_description')
                                            ->label('Deskripsi Singkat')
                                            ->rows(4)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                                \Filament\Schemas\Components\Section::make('Box: Donasi Cepat')
                                    ->schema([
                                        Forms\Components\TextInput::make('donasi_cepat_title')
                                            ->label('Judul Donasi')
                                            ->maxLength(180)
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('donasi_cepat_description')
                                            ->label('Keterangan Donasi')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ]),
                                \Filament\Schemas\Components\Section::make('Box: Ekosistem Gerakan')
                                    ->schema([
                                        Forms\Components\TextInput::make('ekosistem_gerakan_badge')
                                            ->label('Badge Atas')
                                            ->maxLength(80),
                                        Forms\Components\TextInput::make('ekosistem_gerakan_title')
                                            ->label('Judul Kustom (Opsional)')
                                            ->helperText('Jika kosong, akan memakai Nama Situs.')
                                            ->maxLength(150),
                                        Forms\Components\Textarea::make('ekosistem_gerakan_description')
                                            ->label('Deskripsi Kustom (Opsional)')
                                            ->helperText('Jika kosong, akan memakai Deskripsi Situs.')
                                            ->rows(4)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data')
            ->disabled(fn (): bool => ! $this->canUpdate());
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    EmbeddedSchema::make('form'),
                ])
                    ->id('site-settings-form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make($this->getFormActions())
                            ->alignment(Alignment::Start)
                            ->key('site-settings-actions'),
                    ]),
            ]);
    }

    public function save(): void
    {
        abort_unless($this->canUpdate(), 403);

        $data = $this->getSchema('form')->getState();

        app(SiteSettingService::class)->update($data);

        $this->getSchema('form')->fill(app(SiteSettingService::class)->all());

        Notification::make()
            ->title('Pengaturan situs berhasil disimpan.')
            ->success()
            ->send();
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        if (! $this->canUpdate()) {
            return [];
        }

        return [
            Action::make('save')
                ->label('Simpan Pengaturan')
                ->submit('site-settings-form')
                ->color('primary')
                ->keyBindings(['mod+s']),
        ];
    }

    private function canUpdate(): bool
    {
        $user = auth()->user();

        return $user?->can('update_settings')
            || $user?->can('manage_settings')
            || false;
    }
}
