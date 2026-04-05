# Backend Foundation Plan

## Context

Project: Portal Digital PCM Genteng & Lazismu (Gen-Z Edition)

Target utama fase ini:

- membangun fondasi backend Laravel yang rapi dan modular
- mendukung modul PCM dan Lazismu dalam satu monolith
- menghindari hardcoded logic khusus campaign type
- menjaga struktur tetap siap untuk scale-up ke multi-cabang

## Technology Stack

- Laravel 11
- PHP 8.2+
- MySQL
- Livewire 3
- FilamentPHP v3
- Tailwind CSS
- Spatie Laravel Permission
- Laravel Queue dengan database driver
- Local storage dengan desain siap migrasi ke S3/cloud

## Architectural Principles

- Gunakan monolith modular, bukan folder per layer global yang bercampur tanpa domain.
- Simpan business logic pada domain service dan action, bukan di controller, Livewire component, atau Filament resource.
- Jadikan konfigurasi campaign sebagai data, bukan conditional logic besar berbasis tipe campaign.
- Pisahkan klasifikasi bisnis campaign dari mekanisme progress.
- Gunakan DTO, enum, policy, job, dan query object untuk menjaga code tetap terstruktur.
- Semua integrasi eksternal seperti WhatsApp dan storage harus berada di lapisan infrastructure/service adapter.
- Semua proses non-blocking seperti notifikasi dan sinkronisasi progress harus siap dijalankan lewat queue.

## Recommended App Structure

```text
app/
├── Console/
├── Exceptions/
├── Helpers/
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   └── Web/
│   ├── Middleware/
│   ├── Requests/
│   └── Resources/
├── Jobs/
├── Listeners/
├── Notifications/
├── Observers/
├── Policies/
├── Providers/
├── Support/
│   ├── Concerns/
│   ├── DataTransferObjects/
│   ├── Enums/
│   ├── Exceptions/
│   ├── Services/
│   └── ValueObjects/
├── Domains/
│   ├── Shared/
│   │   ├── Actions/
│   │   ├── DTOs/
│   │   ├── Enums/
│   │   └── Services/
│   ├── User/
│   │   ├── Actions/
│   │   ├── Data/
│   │   ├── Filament/
│   │   ├── Models/
│   │   ├── Policies/
│   │   └── Services/
│   ├── AccessControl/
│   │   ├── Actions/
│   │   ├── Enums/
│   │   ├── Services/
│   │   └── Support/
│   ├── Organization/
│   │   ├── Actions/
│   │   ├── Data/
│   │   ├── Filament/
│   │   ├── Models/
│   │   ├── Policies/
│   │   └── Services/
│   ├── Content/
│   │   ├── Actions/
│   │   ├── Data/
│   │   ├── Filament/
│   │   ├── Models/
│   │   ├── Policies/
│   │   ├── Queries/
│   │   └── Services/
│   ├── Campaign/
│   │   ├── Actions/
│   │   ├── Builders/
│   │   ├── Data/
│   │   ├── Enums/
│   │   ├── Filament/
│   │   ├── Models/
│   │   ├── Policies/
│   │   ├── Services/
│   │   └── Support/
│   ├── Donation/
│   │   ├── Actions/
│   │   ├── Data/
│   │   ├── Enums/
│   │   ├── Filament/
│   │   ├── Models/
│   │   ├── Policies/
│   │   └── Services/
│   ├── Report/
│   │   ├── Actions/
│   │   ├── Filament/
│   │   ├── Models/
│   │   ├── Queries/
│   │   └── Services/
│   └── Setting/
│       ├── Actions/
│       ├── Filament/
│       ├── Models/
│       └── Services/
└── Models/
    └── User.php
```

## Domain / Module Breakdown

### 1. User

Tanggung jawab:

- user account
- profil dasar user
- relasi author / creator / verifier

### 2. AccessControl

Tanggung jawab:

- role dan permission
- permission map per modul
- authorization untuk panel admin dan operasi domain

### 3. Organization

Tanggung jawab:

- profil organisasi PCM
- e-struktur pimpinan
- jabatan dan periode
- direktori amal usaha

### 4. Content

Tanggung jawab:

- berita / artikel
- kategori konten
- agenda
- status publish konten

### 5. Campaign

Tanggung jawab:

- engine campaign dinamis
- type campaign
- config schema form JSON
- progress engine amount / unit
- metadata campaign

### 6. Donation

Tanggung jawab:

- donor
- donasi masuk
- upload bukti
- verifikasi admin
- QRIS static flow
- konfirmasi WhatsApp

### 7. Report

Tanggung jawab:

- laporan distribusi
- impact transparency
- agregasi capaian program

### 8. Setting

Tanggung jawab:

- setting aplikasi
- setting organisasi
- setting payment
- setting WhatsApp
- setting storage
- setting fitur yang config-driven

## Naming Convention

### Model

- `Article`
- `ContentCategory`
- `Agenda`
- `BusinessUnit`
- `Position`
- `LeadershipPeriod`
- `LeadershipMember`
- `Campaign`
- `CampaignType`
- `CampaignFormSchema`
- `CampaignMetric`
- `Donor`
- `Donation`
- `DonationItem`
- `PaymentChannel`
- `DonationVerification`
- `DistributionReport`
- `DistributionReportItem`
- `Setting`

### Service

- `ArticleService`
- `AgendaService`
- `CampaignService`
- `CampaignFormService`
- `CampaignProgressService`
- `DonationService`
- `DonationVerificationService`
- `DistributionReportService`
- `SettingService`
- `StorageUrlService`
- `WhatsAppNotificationService`

### Action

- `CreateArticleAction`
- `PublishArticleAction`
- `CreateCampaignAction`
- `UpdateCampaignSchemaAction`
- `SubmitDonationAction`
- `VerifyDonationAction`
- `RejectDonationAction`
- `PublishDistributionReportAction`
- `SyncCampaignProgressAction`

### Enum

- `CampaignTypeEnum`
- `CampaignStatusEnum`
- `CampaignProgressTypeEnum`
- `DonationStatusEnum`
- `PaymentMethodEnum`
- `DonationSourceEnum`
- `ContentStatusEnum`
- `ReportStatusEnum`

### Policy

- `ArticlePolicy`
- `AgendaPolicy`
- `BusinessUnitPolicy`
- `CampaignPolicy`
- `DonationPolicy`
- `DistributionReportPolicy`
- `SettingPolicy`

### Filament Resource

- `ArticleResource`
- `ContentCategoryResource`
- `AgendaResource`
- `BusinessUnitResource`
- `LeadershipPeriodResource`
- `LeadershipMemberResource`
- `CampaignResource`
- `DonationResource`
- `DistributionReportResource`
- `SettingResource`
- `UserResource`
- `RoleResource`

## Service Layer Architecture

### Layering

1. Transport layer
   - controller
   - request
   - Livewire component
   - Filament resource / page

2. Application layer
   - action
   - service
   - DTO

3. Domain layer
   - model
   - enum
   - policy
   - business rules

4. Infrastructure layer
   - job
   - notification
   - external gateway client
   - storage adapter

### Rule of Thumb

- Resource Filament hanya membangun form, table, page, dan memanggil action/service.
- Controller tidak boleh memuat query bisnis kompleks.
- Model boleh punya relasi, scope, cast, dan helper ringan, tetapi tidak menjadi tempat orchestration besar.
- Action menangani satu use case.
- Service mengorkestrasi beberapa action atau dependency.
- Query object dipakai untuk statistik, dashboard, dan pencarian kompleks.

## Config-Driven Campaign Strategy

Pisahkan tiga konsep ini:

- `campaign_type`
  klasifikasi bisnis: donation, qurban, zakat, wakaf, program

- `progress_type`
  metode perhitungan progress: amount atau unit

- `form_schema`
  field dinamis per campaign yang disimpan dalam JSON

Aturan arsitektur:

- Hindari `switch ($campaignType)` untuk semua perilaku.
- Gunakan metadata pada `campaign_types` dan `campaign_form_schemas`.
- Validasi schema JSON melalui `CampaignFormService`.
- Progress dihitung dari `progress_type`, bukan nama tipe campaign.
- Jika suatu tipe membutuhkan perilaku khusus, simpan aturan itu di config schema atau strategy map yang eksplisit.

## Filament Structure Recommendation

Panel dapat dibagi ke navigasi:

- PCM Content
- PCM Organization
- Lazismu Campaign
- Lazismu Donation
- Transparency Report
- Access Control
- Settings

Contoh namespace:

```text
app/Domains/Content/Filament/Resources/ArticleResource.php
app/Domains/Campaign/Filament/Resources/CampaignResource.php
app/Domains/Donation/Filament/Resources/DonationResource.php
```

## Scalable Best Practices

- Gunakan `created_by`, `updated_by`, `verified_by` pada tabel bisnis penting.
- Gunakan slug unik untuk artikel, campaign, dan report.
- Semua notifikasi eksternal masuk ke queue.
- Simpan metadata fleksibel dalam kolom JSON hanya untuk data yang benar-benar dinamis.
- Jangan campur field JSON yang kritikal dengan field query-heavy tanpa alasan.
- Buat service setting yang mendukung cache per group.
- Siapkan storage abstraction sejak awal walaupun disk aktif masih local.
- Hindari dependency antar domain yang terlalu rapat; domain lain mengakses melalui service/action jika perlu.
- Siapkan `branch_id` nullable di fase berikutnya, tetapi jangan menambah kompleksitas tenant sekarang.

## Initial Deliverables

Fondasi backend dianggap siap jika sudah memiliki:

- struktur domain `app/Domains`
- settings system
- role dan permission awal
- CRUD konten dan organisasi dasar
- schema campaign dasar
- donation verification flow dasar
- queue database aktif
