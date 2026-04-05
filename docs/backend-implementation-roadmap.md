# Backend Implementation Roadmap

## Objective

Membangun fondasi backend production-ready sampai campaign engine dinamis dapat berjalan stabil.

## Required Packages

### Wajib pada fase awal

- `filament/filament:^3.2`
- `livewire/livewire:^3`
- `spatie/laravel-permission`
- `spatie/laravel-medialibrary`
- `spatie/laravel-sluggable`
- `spatie/laravel-activitylog`
- `league/flysystem-aws-s3-v3`

### Opsional tetapi direkomendasikan

- `intervention/image`
- `maatwebsite/excel`

Catatan:

- `spatie/laravel-settings` opsional. Untuk project ini, settings table custom juga valid dan lebih terkendali jika ingin tetap ramping.

## Service Layer Plan

### Core Services

- `SettingService`
- `AccessControlService`
- `ArticleService`
- `AgendaService`
- `BusinessUnitService`
- `LeadershipStructureService`
- `CampaignService`
- `CampaignFormService`
- `CampaignProgressService`
- `DonationService`
- `DonationVerificationService`
- `DistributionReportService`
- `WhatsAppNotificationService`
- `StorageUrlService`

### Core Actions

- `CreateCampaignAction`
- `UpdateCampaignAction`
- `PublishCampaignAction`
- `SubmitDonationAction`
- `VerifyDonationAction`
- `RejectDonationAction`
- `CreateDistributionReportAction`
- `PublishDistributionReportAction`
- `SyncCampaignProgressAction`

### Jobs

- `SendDonationConfirmationWhatsAppJob`
- `SyncCampaignProgressJob`
- `ProcessDonationVerificationJob`
- `PublishScheduledContentJob`

## Recommended Development Workflow

### Phase 1. Foundation Setup

Target:

- Laravel 11 siap
- MySQL siap
- queue database siap
- Filament panel siap

Checklist:

- install package inti
- publish config package
- jalankan migration awal
- setup admin panel
- buat super admin seed

### Phase 2. Role and Permission

Role awal:

- `super_admin`
- `admin_pcm`
- `admin_lazismu`
- `kontributor`

Permission group:

- `manage_users`
- `manage_roles`
- `manage_settings`
- `manage_articles`
- `manage_agendas`
- `manage_business_units`
- `manage_leadership_structure`
- `manage_campaigns`
- `manage_donations`
- `verify_donations`
- `manage_distribution_reports`

Output:

- seeder role
- seeder permission
- policy dasar
- guard Filament terkunci dengan permission

### Phase 3. Settings Core

Group setting:

- `app`
- `organization`
- `lazismu`
- `payment`
- `whatsapp`
- `storage`

Output:

- table settings
- service get/set setting
- cache setting per group
- halaman settings di Filament

### Phase 4. PCM Content and Organization

Build:

- kategori konten
- artikel
- agenda
- business unit
- leadership period
- leadership member

Output:

- migration
- model
- policy
- service/action
- Filament resource

### Phase 5. Campaign Engine Foundation

Build:

- campaign types
- campaigns
- campaign form schema
- campaign metrics

Output:

- engine campaign config-driven
- form schema validator
- progress engine dasar
- resource Filament untuk campaign builder

### Phase 6. Donation Flow

Build:

- donor
- payment channel
- donation submission
- proof upload
- pending verification
- verify / reject

Output:

- status pipeline yang rapi
- update progress campaign setelah verifikasi
- event atau job untuk notifikasi WhatsApp

### Phase 7. Transparency and Report

Build:

- distribution report
- report items
- statistik dan summary dasar

Output:

- laporan distribusi terhubung ke campaign
- data transparansi siap dipublikasikan

### Phase 8. Hardening

Build:

- activity log
- audit trail minimum
- queue retry policy
- storage abstraction
- test coverage domain penting

## Best Implementation Order

Urutan paling aman dari awal sampai campaign engine berjalan:

1. setup package inti
2. setup Filament panel
3. setup role dan permission
4. bangun settings system
5. bangun content dan organization module
6. bangun `campaign_types`
7. bangun `campaigns`
8. bangun `campaign_form_schemas`
9. bangun `campaign_metrics`
10. implement `CampaignFormService`
11. implement `CampaignProgressService`
12. buat Filament resource campaign
13. bangun `donors`, `payment_channels`, dan `donations`
14. implement donation verification flow
15. integrasikan queue notification WhatsApp
16. bangun `distribution_reports`
17. tambah logging, tests, dan hardening

## Campaign Engine Acceptance Criteria

Campaign engine dianggap berjalan jika:

- admin dapat membuat campaign baru dari panel
- campaign memilih type tanpa hardcoded branching besar
- schema form dinamis tersimpan di JSON
- progress dapat dihitung berdasarkan amount atau unit
- donation yang diverifikasi otomatis mengubah progress
- data campaign tetap bisa diperluas tanpa refactor struktur besar

## Production-Oriented Practices

- Semua write penting dibungkus dalam transaction bila melibatkan lebih dari satu tabel.
- Status flow gunakan enum dan transition yang jelas.
- Upload bukti transfer jangan dicampur dengan nama file asli tanpa normalisasi.
- Gunakan queue untuk semua notifikasi dan proses non-kritis.
- Buat test minimal untuk action utama: create campaign, submit donation, verify donation, publish report.
- Hindari resource Filament menjadi tempat bisnis logic.
- Jaga query statistik tetap di query service atau query object.

## Suggested Immediate Next Execution

Urutan eksekusi praktis setelah dokumen ini:

1. install package inti
2. setup Filament panel dan auth
3. setup Spatie Permission
4. buat settings table + service
5. buat domain folder `app/Domains`
6. implement modul Content dan Organization dasar
7. lanjut ke campaign engine

## Milestone Definition

### Milestone A

Foundation admin siap:

- admin login
- role permission aktif
- settings aktif

### Milestone B

PCM module siap:

- artikel
- agenda
- amal usaha
- struktur pimpinan

### Milestone C

Campaign engine siap:

- CRUD campaign
- schema dinamis
- progress engine

### Milestone D

Donation flow siap:

- submit donasi
- verifikasi admin
- update progress
- notifikasi WhatsApp
