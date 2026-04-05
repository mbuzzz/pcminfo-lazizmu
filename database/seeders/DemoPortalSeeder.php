<?php

namespace Database\Seeders;

use App\Domain\Setting\Services\SiteSettingService;
use App\Enums\AgendaStatus;
use App\Enums\AgendaType;
use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use App\Enums\DistributionStatus;
use App\Enums\LeaderOrganization;
use App\Enums\OrganizationUnitType;
use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Models\Agenda;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Distribution;
use App\Models\Institution;
use App\Models\Leader;
use App\Models\OrganizationUnit;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DemoPortalSeeder extends Seeder
{
    public function run(SiteSettingService $siteSettingService): void
    {
        $this->call(RoleAndPermissionSeeder::class);

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@pcmgenteng.test'],
            [
                'name' => 'Super Admin PCM Portal',
                'email_verified_at' => now(),
                'password' => 'password',
                'is_active' => true,
            ],
        );

        $admin->assignRole('super_admin');

        $siteAssets = [
            'logo' => $this->storeSvg('demo/site/logo.svg', 'PCM Genteng', '#2C368B', '#10816F', 'PORTAL DIGITAL'),
            'favicon' => $this->storeSvg('demo/site/favicon.svg', 'PCM', '#2C368B', '#01A54D', 'P'),
            'qris_image' => $this->storeQrLikeSvg('demo/site/qris.svg', 'QRIS DONASI'),
            'default_og_image' => $this->storeSvg('demo/site/og-image.svg', 'PCM Genteng & Lazismu', '#2C368B', '#BCD03B', 'GERAKAN BERSAMA'),
        ];

        $siteSettingService->update([
            'site_name' => 'PCM Genteng & Lazismu',
            'site_tagline' => 'Gerakan dakwah, pendidikan, dan filantropi yang lebih terbuka.',
            'site_description' => 'Portal Digital PCM Genteng & Lazismu untuk informasi organisasi, amal usaha, agenda kegiatan, program filantropi, dan transparansi penyaluran.',
            'email' => 'info@pcmgenteng.test',
            'phone' => '0333-123456',
            'whatsapp_number' => '6281234567890',
            'address' => 'Jl. KH. Ahmad Dahlan No. 12, Genteng, Banyuwangi',
            'google_maps_url' => 'https://maps.google.com/?q=PCM+Genteng',
            'instagram' => 'https://instagram.com/pcmgenteng',
            'facebook' => 'https://facebook.com/pcmgenteng',
            'youtube' => 'https://youtube.com/@pcmgenteng',
            'tiktok' => 'https://tiktok.com/@pcmgenteng',
            'donation_whatsapp_number' => '6281234567890',
            'donation_instruction_text' => 'Silakan pilih program yang ingin didukung, transfer sesuai instruksi, lalu kirim konfirmasi agar tim dapat memverifikasi donasi Anda dengan cepat.',
            'donation_whatsapp_message_template' => 'Assalamualaikum, saya ingin konfirmasi donasi untuk program :campaign_title.',
            'default_meta_title' => 'PCM Genteng & Lazismu',
            'default_meta_description' => 'Informasi organisasi, agenda, berita, program filantropi, dan laporan penyaluran PCM Genteng & Lazismu.',
            'footer_description' => 'Satu portal untuk menghubungkan informasi organisasi dan gerakan filantropi secara lebih modern, cepat, dan transparan.',
            'footer_copyright' => '© ' . now()->year . ' PCM Genteng & Lazismu. Seluruh hak cipta dilindungi.',
            'footer_links' => [
                ['label' => 'Tentang', 'url' => '/tentang'],
                ['label' => 'Berita', 'url' => '/berita'],
                ['label' => 'Program', 'url' => '/program'],
                ['label' => 'Kontak', 'url' => '/kontak'],
            ],
            'primary_color' => '#2C368B',
            'secondary_color' => '#10816F',
            'accent_color' => '#BCD03B',
            'default_cta_text' => 'Tunaikan Donasi',
            'homepage_feature_badge' => 'Gerakan Bersama',
            'homepage_feature_title' => 'Kolaborasi yang amanah akan terasa lebih hidup saat informasi, program, dan dampaknya tersaji secara terbuka.',
            'homepage_feature_description' => 'Panel ini sengaja dibuat dinamis dari backend agar tim admin bisa mengubah pesan utama homepage sesuai momentum gerakan, kampanye, atau kebutuhan organisasi.',
            ...$siteAssets,
        ]);

        $postCategories = collect([
            ['name' => 'Berita Organisasi', 'slug' => 'berita-organisasi', 'description' => 'Kabar gerakan dan organisasi.', 'color' => '#2C368B'],
            ['name' => 'Pendidikan', 'slug' => 'pendidikan', 'description' => 'Kabar amal usaha pendidikan.', 'color' => '#10816F'],
            ['name' => 'Filantropi', 'slug' => 'filantropi', 'description' => 'Gerakan donasi dan sosial.', 'color' => '#E8242A'],
        ])->map(fn (array $data) => Category::query()->updateOrCreate(
            ['slug' => $data['slug'], 'type' => 'post'],
            [
                'name' => $data['name'],
                'description' => $data['description'],
                'icon' => 'newspaper',
                'color' => $data['color'],
                'order' => 1,
                'is_active' => true,
            ],
        ));

        $agendaCategories = collect([
            ['name' => 'Kajian', 'slug' => 'kajian', 'color' => '#10816F'],
            ['name' => 'Musyawarah', 'slug' => 'musyawarah', 'color' => '#2C368B'],
            ['name' => 'Aksi Sosial', 'slug' => 'aksi-sosial', 'color' => '#E8242A'],
        ])->map(fn (array $data) => Category::query()->updateOrCreate(
            ['slug' => $data['slug'], 'type' => 'agenda'],
            [
                'name' => $data['name'],
                'description' => 'Agenda ' . strtolower($data['name']),
                'icon' => 'calendar-range',
                'color' => $data['color'],
                'order' => 1,
                'is_active' => true,
            ],
        ));

        $campaignCategories = collect([
            ['name' => 'Beasiswa', 'slug' => 'beasiswa', 'color' => '#2C368B'],
            ['name' => 'Kemanusiaan', 'slug' => 'kemanusiaan', 'color' => '#E8242A'],
            ['name' => 'Pemberdayaan', 'slug' => 'pemberdayaan', 'color' => '#10816F'],
            ['name' => 'Qurban', 'slug' => 'qurban', 'color' => '#F1B12D'],
        ])->map(fn (array $data) => Category::query()->updateOrCreate(
            ['slug' => $data['slug'], 'type' => 'campaign'],
            [
                'name' => $data['name'],
                'description' => 'Program ' . strtolower($data['name']),
                'icon' => 'heart-handshake',
                'color' => $data['color'],
                'order' => 1,
                'is_active' => true,
            ],
        ));

        $institutions = collect([
            [
                'name' => 'SD Muhammadiyah Genteng',
                'slug' => 'sd-muhammadiyah-genteng',
                'type' => 'school',
                'tagline' => 'Sekolah dasar islami yang progresif dan hangat.',
                'city' => 'Banyuwangi',
                'is_featured' => true,
            ],
            [
                'name' => 'SMP Muhammadiyah 1 Genteng',
                'slug' => 'smp-muhammadiyah-1-genteng',
                'type' => 'school',
                'tagline' => 'Pendidikan menengah yang aktif, kreatif, dan berkarakter.',
                'city' => 'Banyuwangi',
                'is_featured' => true,
            ],
            [
                'name' => 'Lazismu Genteng',
                'slug' => 'lazismu-genteng',
                'type' => 'finance',
                'tagline' => 'Pengelolaan zakat, infaq, dan sedekah yang amanah.',
                'city' => 'Banyuwangi',
                'is_featured' => true,
            ],
            [
                'name' => 'Klinik Muhammadiyah Genteng',
                'slug' => 'klinik-muhammadiyah-genteng',
                'type' => 'clinic',
                'tagline' => 'Layanan kesehatan yang cepat, dekat, dan terjangkau.',
                'city' => 'Banyuwangi',
                'is_featured' => true,
            ],
        ])->map(function (array $data, int $index) {
            $logoPath = $this->storeSvg("demo/institutions/{$data['slug']}-logo.svg", $data['name'], '#2C368B', '#10816F', strtoupper(Str::substr($data['name'], 0, 3)));
            $coverPath = $this->storeSvg("demo/institutions/{$data['slug']}-cover.svg", $data['name'], '#EEF2FF', '#DBEAFE', 'AMAL USAHA');

            return Institution::query()->updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'acronym' => Str::upper(Str::substr($data['name'], 0, 4)),
                    'tagline' => $data['tagline'],
                    'description' => $data['tagline'] . ' Berperan aktif dalam ekosistem pendidikan, kesehatan, dan pelayanan sosial di Genteng.',
                    'type' => $data['type'],
                    'status' => 'active',
                    'address' => 'Jl. Pendidikan No. ' . (10 + $index) . ', Genteng',
                    'district' => 'Genteng',
                    'city' => $data['city'],
                    'province' => 'Jawa Timur',
                    'phone' => '0333-1000' . $index,
                    'email' => 'info+' . $data['slug'] . '@pcmgenteng.test',
                    'website' => 'https://example.test/' . $data['slug'],
                    'logo' => $logoPath,
                    'cover_image' => $coverPath,
                    'founded_year' => 1985 + $index,
                    'accreditation' => 'A',
                    'meta' => ['highlight' => true],
                    'is_featured' => $data['is_featured'],
                    'order' => $index + 1,
                ],
            );
        });

        collect([
            [
                'type' => OrganizationUnitType::AutonomousOrganization,
                'name' => 'Pemuda Muhammadiyah Genteng',
                'slug' => 'pemuda-muhammadiyah-genteng',
            ],
            [
                'type' => OrganizationUnitType::AutonomousOrganization,
                'name' => 'Nasyiatul Aisyiyah Genteng',
                'slug' => 'nasyiatul-aisyiyah-genteng',
            ],
            [
                'type' => OrganizationUnitType::Council,
                'name' => 'Majelis Pendidikan Kader',
                'slug' => 'majelis-pendidikan-kader',
            ],
            [
                'type' => OrganizationUnitType::Council,
                'name' => 'Majelis Tabligh',
                'slug' => 'majelis-tabligh',
            ],
            [
                'type' => OrganizationUnitType::Agency,
                'name' => 'Lembaga Dakwah Komunitas',
                'slug' => 'lembaga-dakwah-komunitas',
            ],
            [
                'type' => OrganizationUnitType::Agency,
                'name' => 'Lembaga Lingkungan Hidup dan Penanggulangan Bencana',
                'slug' => 'lembaga-lingkungan-hidup-dan-penanggulangan-bencana',
            ],
        ])->each(function (array $data, int $index): void {
            OrganizationUnit::query()->updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'type' => $data['type'],
                    'name' => $data['name'],
                    'acronym' => Str::upper(Str::substr($data['name'], 0, 4)),
                    'tagline' => 'Unit gerakan yang aktif dan terhubung dengan agenda cabang.',
                    'description' => 'Unit ini mendukung kerja kolaboratif PCM Genteng melalui program rutin, kaderisasi, dan pelayanan publik.',
                    'chairperson' => 'Ketua ' . $data['name'],
                    'secretary' => 'Sekretaris ' . $data['name'],
                    'phone' => '08123' . str_pad((string) $index, 6, '0', STR_PAD_LEFT),
                    'email' => Str::slug($data['slug']) . '@pcmgenteng.test',
                    'website' => 'https://example.test/' . $data['slug'],
                    'address' => 'Genteng, Banyuwangi',
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ],
            );
        });

        $leaderInstitution = $institutions->first();

        collect([
            ['name' => 'Drs. Ahmad Fauzi', 'position' => 'Ketua PCM Genteng', 'organization' => LeaderOrganization::Pcm, 'position_level' => 'leadership'],
            ['name' => 'Nur Hasan, S.Pd.', 'position' => 'Sekretaris PCM Genteng', 'organization' => LeaderOrganization::Pcm, 'position_level' => 'secretary'],
            ['name' => 'Siti Khadijah, S.Pd.', 'position' => 'Ketua PCA Genteng', 'organization' => LeaderOrganization::Pcw, 'position_level' => 'leadership'],
            ['name' => 'M. Rizal Hidayat', 'position' => 'Manajer Lazismu Genteng', 'organization' => LeaderOrganization::Lazismu, 'position_level' => 'leadership'],
            ['name' => 'Fitri Andayani', 'position' => 'Kepala SD Muhammadiyah Genteng', 'organization' => LeaderOrganization::Institution, 'position_level' => 'member'],
        ])->each(function (array $data, int $index) use ($leaderInstitution): void {
            $slug = Str::slug($data['name']);

            Leader::query()->updateOrCreate(
                ['name' => $data['name'], 'position' => $data['position']],
                [
                    'institution_id' => $data['organization'] === LeaderOrganization::Institution ? $leaderInstitution?->getKey() : null,
                    'photo' => $this->storeSvg("demo/leaders/{$slug}.svg", $data['name'], '#E2E8F0', '#CBD5E1', 'PIMPINAN'),
                    'division' => 'Pimpinan Cabang',
                    'nbm' => '12.34.56.' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                    'organization' => $data['organization'],
                    'position_level' => $data['position_level'],
                    'period' => '2022-2027',
                    'phone' => '08111' . str_pad((string) $index, 6, '0', STR_PAD_LEFT),
                    'email' => $slug . '@pcmgenteng.test',
                    'bio' => $data['name'] . ' aktif mendukung penguatan organisasi, layanan umat, dan kolaborasi amal usaha.',
                    'status' => 'active',
                    'order' => $index + 1,
                ],
            );
        });

        $postSeeds = [
            ['title' => 'Silaturahmi PCM Bersama Amal Usaha Muhammadiyah', 'category' => $postCategories[0], 'institution' => $institutions[0], 'type' => PostType::News],
            ['title' => 'Gerakan Beasiswa Santri Kembali Dibuka Tahun Ini', 'category' => $postCategories[2], 'institution' => $institutions[2], 'type' => PostType::Article],
            ['title' => 'Penguatan Kurikulum Al-Islam di Sekolah Muhammadiyah', 'category' => $postCategories[1], 'institution' => $institutions[1], 'type' => PostType::Article],
            ['title' => 'Layanan Klinik Muhammadiyah Makin Dekat dengan Warga', 'category' => $postCategories[0], 'institution' => $institutions[3], 'type' => PostType::News],
            ['title' => 'Kolaborasi Lazismu dan PCM untuk Pemberdayaan UMKM', 'category' => $postCategories[2], 'institution' => $institutions[2], 'type' => PostType::News],
            ['title' => 'Kaderisasi Muda Muhammadiyah Masuk Fase Penguatan', 'category' => $postCategories[0], 'institution' => $institutions[0], 'type' => PostType::Announcement],
        ];

        collect($postSeeds)->each(function (array $data, int $index) use ($admin): void {
            $slug = Str::slug($data['title']);

            Post::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $data['category']->getKey(),
                    'author_id' => $admin->getKey(),
                    'institution_id' => $data['institution']->getKey(),
                    'title' => $data['title'],
                    'excerpt' => 'Ringkasan singkat mengenai ' . Str::lower($data['title']) . ' yang relevan bagi warga dan simpatisan Muhammadiyah di Genteng.',
                    'content' => '<p>' . $data['title'] . ' menjadi salah satu langkah penting untuk memperkuat layanan umat, pendidikan, dan kolaborasi organisasi secara lebih terbuka.</p><p>Konten demo ini dibuat untuk membantu pengujian tampilan frontend publik secara realistis.</p>',
                    'featured_image' => $this->storeSvg("demo/posts/{$slug}.svg", $data['title'], '#2C368B', '#10816F', 'BERITA'),
                    'type' => $data['type'],
                    'status' => PostStatus::Published,
                    'published_at' => now()->subDays(10 - $index),
                    'meta_title' => $data['title'],
                    'meta_description' => 'Meta deskripsi untuk ' . $data['title'],
                    'view_count' => 150 + ($index * 37),
                    'is_featured' => $index < 4,
                    'allow_comments' => false,
                ],
            );
        });

        $agendaSeeds = [
            ['title' => 'Kajian Ahad Pagi PCM Genteng', 'category' => $agendaCategories[0], 'type' => AgendaType::Kajian],
            ['title' => 'Musyawarah Bulanan Pimpinan Cabang', 'category' => $agendaCategories[1], 'type' => AgendaType::Meeting],
            ['title' => 'Aksi Berbagi Paket Ramadan', 'category' => $agendaCategories[2], 'type' => AgendaType::Social],
            ['title' => 'Pelatihan Relawan Filantropi Muda', 'category' => $agendaCategories[2], 'type' => AgendaType::Education],
        ];

        collect($agendaSeeds)->each(function (array $data, int $index) use ($admin, $institutions): void {
            $slug = Str::slug($data['title']);

            Agenda::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $data['category']->getKey(),
                    'institution_id' => $institutions[min($index, $institutions->count() - 1)]->getKey(),
                    'created_by' => $admin->getKey(),
                    'title' => $data['title'],
                    'description' => $data['title'] . ' merupakan agenda publik yang dibuka untuk warga, simpatisan, dan jaringan amal usaha.',
                    'featured_image' => $this->storeSvg("demo/agendas/{$slug}.svg", $data['title'], '#E0F2FE', '#BAE6FD', 'AGENDA'),
                    'type' => $data['type'],
                    'status' => AgendaStatus::Published,
                    'start_at' => now()->addDays(3 + ($index * 4))->setTime(8 + $index, 0),
                    'end_at' => now()->addDays(3 + ($index * 4))->setTime(10 + $index, 0),
                    'location_name' => 'Aula Muhammadiyah Genteng',
                    'location_address' => 'Jl. KH. Ahmad Dahlan No. 12, Genteng',
                    'maps_url' => 'https://maps.google.com/?q=Aula+Muhammadiyah+Genteng',
                    'is_online' => false,
                    'requires_registration' => $index > 1,
                    'max_participants' => 100 + ($index * 25),
                    'registered_count' => 20 + ($index * 8),
                    'is_recurring' => false,
                    'contact_name' => 'Panitia Agenda',
                    'contact_phone' => '08123' . str_pad((string) $index, 6, '0', STR_PAD_LEFT),
                    'is_featured' => $index < 2,
                ],
            );
        });

        $campaigns = collect([
            [
                'title' => 'Program Beasiswa Santri Tahfiz',
                'slug' => 'program-beasiswa-santri-tahfiz',
                'category' => $campaignCategories[0],
                'type' => CampaignType::Scholarship,
                'progress_type' => 'amount',
                'target_amount' => 25000000,
                'collected_amount' => 9800000,
                'verified_donor_count' => 78,
            ],
            [
                'title' => 'Bantuan Pangan untuk Keluarga Rentan',
                'slug' => 'bantuan-pangan-untuk-keluarga-rentan',
                'category' => $campaignCategories[1],
                'type' => CampaignType::Social,
                'progress_type' => 'amount',
                'target_amount' => 18000000,
                'collected_amount' => 12250000,
                'verified_donor_count' => 103,
            ],
            [
                'title' => 'Wakaf Penguatan Sarana Belajar',
                'slug' => 'wakaf-penguatan-sarana-belajar',
                'category' => $campaignCategories[2],
                'type' => CampaignType::Wakaf,
                'progress_type' => 'amount',
                'target_amount' => 40000000,
                'collected_amount' => 16500000,
                'verified_donor_count' => 56,
            ],
            [
                'title' => 'Qurban Kolektif untuk Wilayah Pinggiran',
                'slug' => 'qurban-kolektif-untuk-wilayah-pinggiran',
                'category' => $campaignCategories[3],
                'type' => CampaignType::Qurban,
                'progress_type' => 'unit',
                'target_unit' => 20,
                'collected_unit' => 9,
                'unit_label' => 'Ekor',
                'verified_donor_count' => 31,
            ],
        ])->map(function (array $data, int $index) use ($admin, $institutions) {
            return Campaign::query()->updateOrCreate(
                ['slug' => $data['slug']],
                $this->campaignAttributes($data, $index, $admin->getKey(), $institutions[min($index, $institutions->count() - 1)]->getKey()),
            );
        });

        collect([
            [
                'title' => 'Penyaluran Paket Pangan untuk 120 Keluarga',
                'campaign' => $campaigns[1],
                'amount' => 7500000,
                'location' => 'Genteng Kulon',
            ],
            [
                'title' => 'Distribusi Sarana Belajar untuk Santri Tahfiz',
                'campaign' => $campaigns[0],
                'amount' => 4200000,
                'location' => 'Pesantren Mitra Genteng',
            ],
            [
                'title' => 'Tahap Awal Pengadaan Perlengkapan Pembelajaran',
                'campaign' => $campaigns[2],
                'amount' => 6000000,
                'location' => 'Amal Usaha Pendidikan',
            ],
        ])->each(function (array $data, int $index) use ($admin, $institutions): void {
            Distribution::query()->updateOrCreate(
                ['distribution_code' => 'DST-' . now()->format('Y') . '-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT)],
                [
                    'campaign_id' => $data['campaign']->getKey(),
                    'institution_id' => $institutions[2]->getKey(),
                    'title' => $data['title'],
                    'description' => $data['title'] . ' dicatat sebagai bagian dari transparansi penyaluran program pada portal.',
                    'recipient_type' => 'general',
                    'recipient_name' => 'Penerima manfaat wilayah Genteng',
                    'recipient_count' => 45 + ($index * 20),
                    'distributed_amount' => $data['amount'],
                    'distributed_unit' => 0,
                    'unit_label' => 'Paket',
                    'distribution_type' => 'cash',
                    'status' => $index === 2 ? DistributionStatus::Distributed : DistributionStatus::Reported,
                    'distribution_date' => now()->subDays(6 - ($index * 2))->toDateString(),
                    'location' => $data['location'],
                    'evidence_files' => [
                        $this->storeSvg('demo/distributions/evidence-' . ($index + 1) . '.svg', $data['title'], '#DCFCE7', '#86EFAC', 'LAPORAN'),
                    ],
                    'notes' => 'Catatan lapangan demo untuk kebutuhan preview frontend.',
                    'meta' => ['channel' => 'demo'],
                    'created_by' => $admin->getKey(),
                    'approved_by' => $admin->getKey(),
                    'approved_at' => now()->subDays(5 - $index),
                    'distributed_by' => $admin->getKey(),
                ],
            );
        });
    }

    private function storeSvg(string $path, string $title, string $primaryColor, string $secondaryColor, string $badge): string
    {
        $safeTitle = htmlspecialchars(Str::limit($title, 42, ''), ENT_QUOTES | ENT_XML1);
        $safeBadge = htmlspecialchars($badge, ENT_QUOTES | ENT_XML1);

        $svg = <<<SVG
<svg width="1200" height="720" viewBox="0 0 1200 720" fill="none" xmlns="http://www.w3.org/2000/svg">
  <rect width="1200" height="720" rx="40" fill="#F8FAFC"/>
  <rect x="28" y="28" width="1144" height="664" rx="32" fill="url(#paint0_linear)"/>
  <circle cx="1042" cy="116" r="112" fill="white" fill-opacity="0.12"/>
  <circle cx="198" cy="588" r="144" fill="white" fill-opacity="0.10"/>
  <rect x="72" y="76" width="208" height="48" rx="24" fill="white" fill-opacity="0.20"/>
  <text x="104" y="107" fill="white" font-size="24" font-family="Arial, sans-serif" font-weight="700">{$safeBadge}</text>
  <text x="72" y="242" fill="white" font-size="56" font-family="Arial, sans-serif" font-weight="800">{$safeTitle}</text>
  <text x="72" y="308" fill="white" fill-opacity="0.78" font-size="24" font-family="Arial, sans-serif">Preview media demo untuk portal publik</text>
  <rect x="72" y="548" width="260" height="88" rx="28" fill="white" fill-opacity="0.15"/>
  <text x="112" y="603" fill="white" font-size="22" font-family="Arial, sans-serif" font-weight="700">PCM Genteng</text>
  <defs>
    <linearGradient id="paint0_linear" x1="86" y1="66" x2="1112" y2="674" gradientUnits="userSpaceOnUse">
      <stop stop-color="{$primaryColor}"/>
      <stop offset="1" stop-color="{$secondaryColor}"/>
    </linearGradient>
  </defs>
</svg>
SVG;

        Storage::disk('public')->put($path, $svg);

        return $path;
    }

    /**
     * @param  array{
     *     category:\App\Models\Category,
     *     title:string,
     *     slug:string,
     *     type:\App\Enums\CampaignType,
     *     progress_type:string,
     *     target_amount?:int,
     *     target_unit?:int,
     *     collected_amount?:int,
     *     collected_unit?:int,
     *     unit_label?:string,
     *     verified_donor_count:int
     * }  $data
     * @return array<string, mixed>
     */
    private function campaignAttributes(array $data, int $index, int $adminId, int $institutionId): array
    {
        $attributes = [
            'category_id' => $data['category']->getKey(),
            'institution_id' => $institutionId,
            'created_by' => $adminId,
            'title' => $data['title'],
            'short_description' => 'Program ' . Str::lower($data['title']) . ' untuk mendorong manfaat yang lebih luas.',
            'description' => '<p>' . $data['title'] . ' merupakan program demo yang dirancang untuk pengujian tampilan frontend publik dan blok donasi yang dinamis.</p><p>Data progres, kategori, dan penyaluran akan dipakai oleh halaman program, homepage, dan transparansi.</p>',
            'featured_image' => $this->storeSvg("demo/campaigns/{$data['slug']}.svg", $data['title'], '#FDE68A', '#F59E0B', 'PROGRAM'),
            'type' => $data['type'],
            'status' => CampaignStatus::Active,
            'unit_label' => $data['unit_label'] ?? 'Paket',
            'collected_amount' => $data['collected_amount'] ?? 0,
            'collected_unit' => $data['collected_unit'] ?? 0,
            'start_date' => now()->subDays(15 + $index)->toDateString(),
            'end_date' => now()->addDays(35 + ($index * 8))->toDateString(),
            'config' => ['highlight' => true, 'show_updates' => true],
            'payment_config' => ['use_global' => true],
            'beneficiary_name' => 'Masyarakat dan penerima manfaat Genteng',
            'beneficiary_description' => 'Penerima manfaat ditentukan sesuai kebutuhan program dan hasil asesmen lapangan.',
            'meta_title' => $data['title'],
            'meta_description' => 'Meta deskripsi untuk ' . $data['title'],
            'is_featured' => $index < 3,
            'allow_anonymous' => true,
            'show_donor_list' => false,
        ];

        if (Schema::hasColumn('campaigns', 'goal_type')) {
            $attributes['goal_type'] = $data['progress_type'] === 'unit' ? 'unit' : 'nominal';
            $attributes['goal_amount'] = $data['target_amount'] ?? null;
            $attributes['goal_unit'] = $data['target_unit'] ?? null;
            $attributes['donor_count'] = $data['verified_donor_count'];
        } else {
            $attributes['progress_type'] = $data['progress_type'];
            $attributes['target_amount'] = $data['target_amount'] ?? null;
            $attributes['target_unit'] = $data['target_unit'] ?? null;
            $attributes['verified_donor_count'] = $data['verified_donor_count'];
        }

        if (Schema::hasColumn('campaigns', 'published_at')) {
            $attributes['published_at'] = now()->subDays(14 - $index);
        }

        return $attributes;
    }

    private function storeQrLikeSvg(string $path, string $title): string
    {
        $safeTitle = htmlspecialchars($title, ENT_QUOTES | ENT_XML1);

        $svg = <<<SVG
<svg width="900" height="900" viewBox="0 0 900 900" fill="none" xmlns="http://www.w3.org/2000/svg">
  <rect width="900" height="900" rx="40" fill="#ffffff"/>
  <rect x="70" y="70" width="760" height="760" rx="24" fill="#F8FAFC" stroke="#CBD5E1" stroke-width="16"/>
  <rect x="120" y="120" width="180" height="180" rx="18" fill="#0F172A"/>
  <rect x="600" y="120" width="180" height="180" rx="18" fill="#0F172A"/>
  <rect x="120" y="600" width="180" height="180" rx="18" fill="#0F172A"/>
  <rect x="360" y="360" width="60" height="60" rx="8" fill="#0F172A"/>
  <rect x="450" y="360" width="60" height="60" rx="8" fill="#0F172A"/>
  <rect x="540" y="360" width="60" height="60" rx="8" fill="#0F172A"/>
  <rect x="360" y="450" width="60" height="60" rx="8" fill="#0F172A"/>
  <rect x="540" y="450" width="60" height="60" rx="8" fill="#0F172A"/>
  <rect x="360" y="540" width="60" height="60" rx="8" fill="#0F172A"/>
  <rect x="450" y="540" width="60" height="60" rx="8" fill="#0F172A"/>
  <rect x="540" y="540" width="60" height="60" rx="8" fill="#0F172A"/>
  <text x="450" y="840" text-anchor="middle" fill="#0F172A" font-size="34" font-family="Arial, sans-serif" font-weight="700">{$safeTitle}</text>
</svg>
SVG;

        Storage::disk('public')->put($path, $svg);

        return $path;
    }
}
