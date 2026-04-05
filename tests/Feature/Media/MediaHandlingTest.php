<?php

declare(strict_types=1);

namespace Tests\Feature\Media;

use App\Services\Media\MediaUploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaHandlingTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_menghapus_file_lama_saat_field_dikosongkan(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('leaders/photo/lama.jpg', 'dummy');

        app(MediaUploadService::class)->sync('leaders/photo/lama.jpg', null, true);

        Storage::disk('public')->assertMissing('leaders/photo/lama.jpg');
    }

    public function test_sync_tidak_menghapus_file_jika_field_tidak_dikirim(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('leaders/photo/lama.jpg', 'dummy');

        app(MediaUploadService::class)->sync('leaders/photo/lama.jpg', null, false);

        Storage::disk('public')->assertExists('leaders/photo/lama.jpg');
    }
}
