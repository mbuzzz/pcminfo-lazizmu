# Backend Structure Architecture

## Catatan Baseline Repo

Workspace saat ini sudah menggunakan:

- Laravel 12
- Filament 5
- Livewire 4

Dokumen ini tetap valid untuk pendekatan modular Laravel 11, tetapi struktur yang diusulkan saya sesuaikan agar tetap kompatibel dengan kondisi repo sekarang.

## 1. Struktur Folder `app/` yang Ideal

```text
app/
├── Application/
│   ├── Contracts/
│   ├── DTOs/
│   └── Support/
├── Domain/
│   ├── Access/
│   │   ├── Actions/
│   │   ├── Enums/
│   │   ├── Policies/
│   │   ├── Queries/
│   │   └── Services/
│   ├── Content/
│   │   ├── Actions/
│   │   ├── Data/
│   │   ├── Enums/
│   │   ├── Models/
│   │   ├── Policies/
│   │   ├── Queries/
│   │   └── Services/
│   ├── Organization/
│   │   ├── Actions/
│   │   ├── Data/
│   │   ├── Enums/
│   │   ├── Models/
│   │   ├── Policies/
│   │   ├── Queries/
│   │   └── Services/
│   ├── Campaign/
│   │   ├── Actions/
│   │   ├── Data/
│   │   ├── Enums/
│   │   ├── Models/
│   │   ├── Policies/
│   │   ├── Queries/
│   │   ├── Rules/
│   │   └── Services/
│   ├── Donation/
│   │   ├── Actions/
│   │   ├── Data/
│   │   ├── Enums/
│   │   ├── Models/
│   │   ├── Policies/
│   │   ├── Queries/
│   │   ├── Rules/
│   │   └── Services/
│   ├── Report/
│   │   ├── Actions/
│   │   ├── Data/
│   │   ├── Enums/
│   │   ├── Models/
│   │   ├── Policies/
│   │   ├── Queries/
│   │   └── Services/
│   ├── Setting/
│   │   ├── Actions/
│   │   ├── Data/
│   │   ├── Enums/
│   │   ├── Models/
│   │   ├── Policies/
│   │   ├── Queries/
│   │   └── Services/
│   ├── User/
│   │   ├── Actions/
│   │   ├── Data/
│   │   ├── Enums/
│   │   ├── Models/
│   │   ├── Policies/
│   │   ├── Queries/
│   │   └── Services/
│   └── Shared/
│       ├── Concerns/
│       ├── Enums/
│       ├── Exceptions/
│       ├── Rules/
│       └── ValueObjects/
├── Filament/
│   ├── Pages/
│   ├── Resources/
│   └── Widgets/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
├── Infrastructure/
│   ├── Media/
│   ├── Notifications/
│   ├── Payments/
│   ├── Persistence/
│   ├── Storage/
│   └── WhatsApp/
├── Jobs/
├── Listeners/
├── Notifications/
├── Observers/
├── Providers/
└── Support/
    ├── Helpers/
    ├── Pagination/
    └── Response/
```

## Prinsip Struktur

- `Domain/` adalah pusat bisnis per modul.
- `Actions/` menyimpan use case write yang spesifik dan mudah ditest.
- `Queries/` menyimpan read model, statistik, filter table, dan agregasi.
- `Services/` hanya untuk orkestrasi atau business service lintas action, bukan tempat semua logic ditumpuk.
- `Infrastructure/` khusus adapter pihak ketiga seperti storage, payment gateway, QRIS, WhatsApp, dan media.
- `Filament/` tetap tipis. Resource hanya memanggil action, query, atau service.

## 2. Pembagian Domain / Module

### `User`

Tanggung jawab:

- akun admin dan kontributor
- profil internal user
- relasi author, creator, verifier

Model utama:

- `User`
- `UserProfile`

### `Access`

Tanggung jawab:

- role
- permission
- permission map per panel dan modul
- policy helper

Model utama:

- `Role`
- `Permission`

Catatan:

- karena memakai Spatie Permission, domain ini lebih banyak berupa action, policy, dan service daripada custom model utama

### `Organization`

Tanggung jawab:

- profil PCM
- struktur pimpinan
- jabatan
- periode kepengurusan
- amal usaha

Model utama:

- `OrganizationProfile`
- `BusinessUnit`
- `LeadershipPeriod`
- `Position`
- `LeadershipMember`

### `Content`

Tanggung jawab:

- artikel
- kategori konten
- agenda
- publish workflow

Model utama:

- `Article`
- `ArticleCategory`
- `Agenda`

### `Campaign`

Tanggung jawab:

- campaign builder dinamis
- metadata campaign
- target progress nominal atau unit
- schema form JSON
- progress summary

Model utama:

- `Campaign`
- `CampaignCategory`
- `CampaignTarget`
- `CampaignForm`
- `CampaignProgressSnapshot`

Catatan:

- `CampaignType` boleh dipakai bila memang ada master type tetap
- bila campaign sangat dinamis, lebih aman gunakan `CampaignCategory` + `progress_type` + `form_schema`

### `Donation`

Tanggung jawab:

- donor
- donasi masuk
- item donasi
- bukti transfer
- verifikasi
- payment channel

Model utama:

- `Donor`
- `Donation`
- `DonationItem`
- `DonationVerification`
- `PaymentChannel`

### `Report`

Tanggung jawab:

- laporan distribusi
- transparansi penyaluran
- impact reporting
- agregasi per campaign

Model utama:

- `DistributionReport`
- `DistributionReportItem`
- `CampaignImpactSummary`

### `Setting`

Tanggung jawab:

- setting aplikasi
- setting organisasi
- setting payment
- setting media dan storage
- setting WhatsApp

Model utama:

- `Setting`

## 3. Naming Convention

### Model

Gunakan singular, business-oriented, tanpa suffix teknis yang tidak perlu.

Contoh:

- `User`
- `OrganizationProfile`
- `BusinessUnit`
- `LeadershipPeriod`
- `LeadershipMember`
- `Article`
- `ArticleCategory`
- `Agenda`
- `Campaign`
- `CampaignTarget`
- `CampaignForm`
- `CampaignProgressSnapshot`
- `Donor`
- `Donation`
- `DonationItem`
- `DonationVerification`
- `PaymentChannel`
- `DistributionReport`
- `DistributionReportItem`
- `Setting`

### Service

Gunakan suffix `Service` hanya bila benar-benar berupa service domain atau orchestration service.

Contoh:

- `CampaignService`
- `CampaignFormService`
- `CampaignProgressService`
- `DonationService`
- `DonationVerificationService`
- `DistributionReportService`
- `SettingService`
- `RolePermissionService`

Hindari:

- `HelperService`
- `GlobalService`
- `CommonService`

### Action

Gunakan format `VerbNounAction`.

Contoh:

- `CreateCampaignAction`
- `UpdateCampaignAction`
- `PublishCampaignAction`
- `ArchiveCampaignAction`
- `SubmitDonationAction`
- `VerifyDonationAction`
- `RejectDonationAction`
- `CreateDistributionReportAction`
- `PublishArticleAction`

### Enum

Gunakan format `NounEnum` atau `NounStatusEnum`.

Contoh:

- `CampaignStatusEnum`
- `CampaignProgressTypeEnum`
- `CampaignVisibilityEnum`
- `DonationStatusEnum`
- `DonationVerificationStatusEnum`
- `ContentStatusEnum`
- `SettingGroupEnum`
- `UserRoleEnum`

### Policy

Gunakan nama model + `Policy`.

Contoh:

- `UserPolicy`
- `ArticlePolicy`
- `AgendaPolicy`
- `CampaignPolicy`
- `DonationPolicy`
- `DistributionReportPolicy`
- `BusinessUnitPolicy`

### Filament Resource

Gunakan nama model + `Resource`.

Contoh:

- `UserResource`
- `ArticleResource`
- `AgendaResource`
- `BusinessUnitResource`
- `CampaignResource`
- `DonationResource`
- `DistributionReportResource`
- `SettingResource`

Untuk page kustom:

- `ManageCampaignDonations`
- `ManageCampaignProgress`
- `ManageOrganizationProfile`
- `ViewDonationVerification`

## 4. Arsitektur Service Layer

## Aturan Umum

- Controller, Livewire component, dan Filament resource tidak boleh menyimpan business rule utama.
- Semua write flow yang menyentuh lebih dari satu model masuk ke `Action`.
- Semua read flow yang berat atau agregatif masuk ke `Query`.
- `Service` dipakai bila ada orkestrasi beberapa action, aturan lintas aggregate, atau reusable business capability.

## Lapisan yang Disarankan

### `Action`

Karakter:

- satu use case
- fokus write
- transactional
- mudah ditest

Contoh:

- `CreateCampaignAction`
- `SubmitDonationAction`
- `VerifyDonationAction`

### `Domain Service`

Karakter:

- rule bisnis inti
- tidak terikat UI
- dapat dipanggil dari action lain

Contoh:

- `CampaignProgressService`
- `CampaignFormService`
- `DonationVerificationService`

### `Query Service`

Karakter:

- fokus read
- statistik
- dashboard summary
- table/filter optimization

Contoh:

- `CampaignStatsQuery`
- `DonationTableQuery`
- `DistributionReportSummaryQuery`

### `Infrastructure Service`

Karakter:

- integrasi vendor atau resource luar
- mudah diganti adapter-nya

Contoh:

- `WhatsAppGateway`
- `MediaUploader`
- `StorageUrlGenerator`
- `QrisPaymentGateway`

## Flow yang Sehat

Contoh verifikasi donasi:

1. Filament page memanggil `VerifyDonationAction`
2. action membuka transaction
3. action update status donasi
4. action memanggil `CampaignProgressService`
5. action dispatch job notifikasi
6. action simpan activity log

Contoh submit campaign:

1. resource form memanggil `CreateCampaignAction`
2. action validasi struktur DTO
3. action panggil `CampaignFormService` untuk validasi schema JSON
4. action simpan `campaigns`, `campaign_targets`, dan `campaign_forms`
5. action commit transaction

## Bentuk Class yang Direkomendasikan

```php
final class VerifyDonationAction
{
    public function __construct(
        private DonationVerificationService $verificationService,
        private CampaignProgressService $campaignProgressService,
    ) {
    }

    public function execute(Donation $donation, User $actor, array $payload = []): Donation
    {
        return DB::transaction(function () use ($donation, $actor, $payload) {
            $verifiedDonation = $this->verificationService->verify($donation, $actor, $payload);

            $this->campaignProgressService->syncFromVerifiedDonation($verifiedDonation);

            return $verifiedDonation->refresh();
        });
    }
}
```

## 5. Best Practice Agar Scalable

### Domain dan Data

- jadikan campaign sebagai entity config-driven, bukan kumpulan `if campaign_type === ...`
- pisahkan `campaign target`, `progress type`, dan `form schema`
- simpan JSON schema dengan field `schema_version`
- gunakan table terpisah untuk snapshot atau summary progress jika query publik mulai berat
- siapkan `organization_id` nullable sejak awal jika ada kemungkinan multi-cabang

### Kode

- pindahkan model keluar dari `app/Models` ke `app/Domain/*/Models` secara bertahap
- gunakan DTO untuk payload action yang kompleks
- gunakan enum untuk semua state penting
- hindari service raksasa; pecah per capability
- buat `Query` class untuk dashboard dan tabel admin agar resource tidak gemuk

### Database

- tambahkan index untuk semua foreign key, status, slug, dan kolom pencarian utama
- gunakan transaction pada flow donasi, verifikasi, dan publish report
- buat unique constraint untuk slug, code, dan kombinasi field bisnis yang harus unik
- simpan nominal dalam integer terkecil bila payment sudah masuk fase serius

### Authorization

- role dipakai untuk grouping akses
- permission dipakai untuk operasi nyata
- policy tetap dipakai walaupun sudah ada permission agar rule per-record tetap rapi
- batasi akses panel Filament berdasarkan role dan policy, bukan hanya middleware login

### Filament

- resource hanya menangani form schema, table schema, dan action hook ringan
- business logic panggil action atau service
- statistik dashboard panggil query service, bukan query inline berulang
- gunakan relation manager hanya untuk CRUD relasi yang sederhana

### Observability dan Reliability

- aktifkan `spatie/laravel-activitylog` untuk write penting
- event dan job untuk notifikasi WhatsApp, email, atau sinkronisasi non-kritis
- semua adapter eksternal berada di `Infrastructure/`
- gunakan idempotent job untuk proses verifikasi atau sinkronisasi progress

### Testing

- unit test untuk enum, rule, dan service penting
- feature test untuk action utama
- minimal cover:
  - create campaign
  - update campaign form
  - submit donation
  - verify donation
  - publish distribution report

## Rekomendasi Eksekusi Tahap Berikutnya

Urutan implementasi yang paling aman:

1. rapikan namespace target dan struktur `app/`
2. buat module `Setting`, `Access`, dan `User`
3. bangun module `Organization` dan `Content`
4. bangun fondasi `Campaign`
5. bangun `Donation`
6. sambungkan `Report`
7. tambahkan queue, activity log, dan test coverage

## Keputusan Arsitektur yang Saya Rekomendasikan

Untuk project ini, pilihan paling stabil adalah:

- modular monolith
- domain-first folder structure
- action + query + service separation
- Filament sebagai admin delivery layer, bukan business layer
- campaign engine berbasis konfigurasi data, bukan branching kode
