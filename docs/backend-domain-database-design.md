# Backend Domain and Database Design

## Core Domains

### PCM Module

- artikel / berita
- kategori konten
- agenda
- direktori amal usaha
- e-struktur pimpinan

### Lazismu Module

- campaign dinamis
- payment via QRIS statis
- verifikasi donasi
- laporan distribusi
- transparency report

## Main Models

### Access and Users

- `User`
- `Role`
- `Permission`

### Settings

- `Setting`

### Organization

- `BusinessUnit`
- `Position`
- `LeadershipPeriod`
- `LeadershipMember`

### Content

- `ContentCategory`
- `Article`
- `Agenda`

### Campaign

- `CampaignType`
- `Campaign`
- `CampaignFormSchema`
- `CampaignMetric`

### Donation

- `Donor`
- `PaymentChannel`
- `Donation`
- `DonationItem`
- `DonationVerification`

### Transparency

- `DistributionReport`
- `DistributionReportItem`

## Primary Relationships

### Content

- `ContentCategory` hasMany `Article`
- `Article` belongsTo `ContentCategory`
- `Article` belongsTo `User` as `author`
- `Agenda` belongsTo `User` as `author`

### Organization

- `LeadershipPeriod` hasMany `LeadershipMember`
- `LeadershipMember` belongsTo `LeadershipPeriod`
- `LeadershipMember` belongsTo `Position`

### Campaign

- `CampaignType` hasMany `Campaign`
- `Campaign` belongsTo `CampaignType`
- `Campaign` hasOne `CampaignFormSchema`
- `Campaign` hasMany `CampaignMetric`
- `Campaign` hasMany `Donation`
- `Campaign` hasMany `DistributionReport`

### Donation

- `Donor` hasMany `Donation`
- `PaymentChannel` hasMany `Donation`
- `Donation` belongsTo `Donor`
- `Donation` belongsTo `Campaign`
- `Donation` belongsTo `PaymentChannel`
- `Donation` hasOne `DonationVerification`
- `Donation` hasMany `DonationItem`

### Report

- `DistributionReport` belongsTo `Campaign`
- `DistributionReport` belongsTo `User` as `author`
- `DistributionReport` hasMany `DistributionReportItem`

## Recommended Migrations

### Foundation

1. `create_users_table`
2. `create_cache_table`
3. `create_jobs_table`

### Access Control

4. `create_permission_tables`

### Settings

5. `create_settings_table`

### Content and Organization

6. `create_content_categories_table`
7. `create_articles_table`
8. `create_agendas_table`
9. `create_business_units_table`
10. `create_positions_table`
11. `create_leadership_periods_table`
12. `create_leadership_members_table`

### Campaign Engine

13. `create_campaign_types_table`
14. `create_campaigns_table`
15. `create_campaign_form_schemas_table`
16. `create_campaign_metrics_table`

### Donation

17. `create_donors_table`
18. `create_payment_channels_table`
19. `create_donations_table`
20. `create_donation_items_table`
21. `create_donation_verifications_table`

### Transparency

22. `create_distribution_reports_table`
23. `create_distribution_report_items_table`

## Suggested Table Fields

### `settings`

- `id`
- `group`
- `key`
- `value` JSON nullable
- `type`
- `is_public`
- timestamps

Index:

- unique: `group`, `key`

### `content_categories`

- `id`
- `name`
- `slug`
- `description`
- `is_active`
- timestamps

### `articles`

- `id`
- `content_category_id`
- `author_id`
- `title`
- `slug`
- `excerpt`
- `content`
- `featured_image_path` nullable
- `status`
- `published_at` nullable
- `meta` JSON nullable
- timestamps

Indexes:

- unique: `slug`
- index: `status`, `published_at`

### `agendas`

- `id`
- `author_id`
- `title`
- `slug`
- `description`
- `location`
- `start_at`
- `end_at`
- `status`
- `meta` JSON nullable
- timestamps

### `business_units`

- `id`
- `name`
- `slug`
- `description`
- `address`
- `phone`
- `email`
- `website`
- `map_embed` nullable
- `logo_path` nullable
- `is_active`
- `sort_order`
- timestamps

### `positions`

- `id`
- `name`
- `slug`
- `level`
- `sort_order`
- timestamps

### `leadership_periods`

- `id`
- `name`
- `start_year`
- `end_year`
- `is_active`
- timestamps

### `leadership_members`

- `id`
- `leadership_period_id`
- `position_id`
- `name`
- `photo_path` nullable
- `bio` nullable
- `sort_order`
- `is_active`
- timestamps

### `campaign_types`

- `id`
- `key`
- `name`
- `description` nullable
- `default_progress_type`
- `default_form_schema` JSON nullable
- `is_active`
- timestamps

Unique:

- `key`

### `campaigns`

- `id`
- `campaign_type_id`
- `created_by`
- `updated_by` nullable
- `title`
- `slug`
- `summary`
- `description`
- `status`
- `progress_type`
- `goal_amount` decimal nullable
- `goal_unit` integer nullable
- `collected_amount` decimal default 0
- `collected_unit` integer default 0
- `start_at` nullable
- `end_at` nullable
- `is_featured`
- `cover_image_path` nullable
- `meta` JSON nullable
- `published_at` nullable
- timestamps

Indexes:

- unique: `slug`
- index: `status`, `published_at`
- index: `campaign_type_id`

### `campaign_form_schemas`

- `id`
- `campaign_id`
- `schema_version`
- `schema` JSON
- `is_active`
- timestamps

Unique:

- unique: `campaign_id`, `schema_version`

### `campaign_metrics`

- `id`
- `campaign_id`
- `metric_key`
- `metric_label`
- `metric_type`
- `target_value` decimal nullable
- `current_value` decimal nullable
- `config` JSON nullable
- timestamps

### `donors`

- `id`
- `name`
- `phone`
- `email` nullable
- `is_whatsapp_active`
- `meta` JSON nullable
- timestamps

Indexes:

- index: `phone`
- index: `email`

### `payment_channels`

- `id`
- `name`
- `code`
- `type`
- `account_name` nullable
- `account_number` nullable
- `qr_image_path` nullable
- `instructions` JSON nullable
- `is_active`
- timestamps

Unique:

- unique: `code`

### `donations`

- `id`
- `campaign_id`
- `donor_id`
- `payment_channel_id`
- `verified_by` nullable
- `invoice_number`
- `donation_date`
- `amount` decimal nullable
- `unit_quantity` integer nullable
- `message` nullable
- `proof_path` nullable
- `status`
- `verification_note` nullable
- `verified_at` nullable
- `payload` JSON nullable
- `source`
- timestamps

Indexes:

- unique: `invoice_number`
- index: `campaign_id`, `status`
- index: `donor_id`
- index: `donation_date`

### `donation_items`

- `id`
- `donation_id`
- `item_name`
- `quantity`
- `unit_label`
- `price_per_unit` decimal nullable
- `subtotal` decimal nullable
- `meta` JSON nullable
- timestamps

### `donation_verifications`

- `id`
- `donation_id`
- `verified_by`
- `status`
- `note` nullable
- `verified_at`
- `meta` JSON nullable
- timestamps

### `distribution_reports`

- `id`
- `campaign_id`
- `created_by`
- `title`
- `slug`
- `summary`
- `content`
- `report_date`
- `beneficiary_count` integer nullable
- `distributed_amount` decimal nullable
- `distributed_unit` integer nullable
- `status`
- `published_at` nullable
- timestamps

Indexes:

- unique: `slug`
- index: `campaign_id`, `status`

### `distribution_report_items`

- `id`
- `distribution_report_id`
- `title`
- `description` nullable
- `amount` decimal nullable
- `unit` integer nullable
- `meta` JSON nullable
- timestamps

## JSON-Driven Configuration Strategy

### `campaign_form_schemas.schema`

Dipakai untuk:

- field form donasi dinamis
- visibility rules
- validation rules yang dapat diinterpretasi service
- informasi apakah campaign butuh nominal, unit, atau item detail

Contoh struktur:

```json
{
  "sections": [
    {
      "name": "donor_information",
      "fields": [
        {
          "key": "donor_name",
          "type": "text",
          "label": "Nama Donatur",
          "required": true
        },
        {
          "key": "phone",
          "type": "text",
          "label": "Nomor WhatsApp",
          "required": true
        }
      ]
    },
    {
      "name": "donation_payload",
      "fields": [
        {
          "key": "amount",
          "type": "currency",
          "required": true
        }
      ]
    }
  ]
}
```

### `campaigns.meta`

Dipakai untuk:

- CTA label
- short labels
- extra badges
- landing metadata

Jangan gunakan `meta` untuk data relasional yang akan sering di-query.

## Production Notes

- Gunakan foreign key eksplisit untuk semua relasi inti.
- Gunakan enum cast pada status dan progress type.
- Gunakan soft delete hanya jika memang diperlukan untuk entitas tertentu.
- Siapkan `branch_id` di fase berikutnya, bukan di fase fondasi ini.
- Simpan file path dengan konsep disk-aware agar migrasi ke S3 nantinya tidak merusak desain.
