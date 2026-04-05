# Filament Admin Resource Blueprint

Dokumen ini memetakan resource Filament utama untuk admin panel Portal Digital PCM Genteng & Lazismu.

## Resource yang tersedia

- `CampaignResource`
- `DonationResource`
- `DistributionResource`
- `PostResource`
- `CategoryResource`
- `AgendaResource`
- `InstitutionResource`
- `LeaderResource`

## Ringkasan desain

### Campaign
- Form utama:
  - informasi campaign
  - target dan progress
  - builder field donasi via `config.form.fields`
  - behavior dan payment config
- Kolom table:
  - title, type, status, goal type, progress, amount terkumpul, donor, end date
- Filter:
  - type, status, institution, featured, expired
- Action:
  - edit, activate, pause, complete

### Donation
- Form utama:
  - campaign, kode transaksi, data donatur, nominal, metode bayar, status verifikasi
- Kolom table:
  - transaction code, campaign, donor, amount, payment method, payment status, status, confirmed at
- Filter:
  - campaign, payment method, payment status, status, manual payment, month
- Action:
  - edit, approve, reject

### Distribution
- Form utama:
  - sumber dana, penerima, nilai, status, tanggal distribusi, metadata
- Kolom table:
  - code, title, recipient type, status, amount, recipient count, distribution date
- Filter:
  - status, campaign, institution
- Action:
  - edit, approve, mark distributed, mark reported

### Post
- Form utama:
  - title, slug, category, institution, author, type, status, published_at, excerpt, content, SEO
- Kolom table:
  - title, type, status, author, published_at, featured, view_count
- Filter:
  - type, status, category, featured
- Action:
  - edit, submit review, publish, archive

### Category
- Form utama:
  - parent, name, slug, type, description, icon, color, order, is_active
- Kolom table:
  - name, type, color, order, is_active
- Filter:
  - type, is_active
- Action:
  - edit

### Agenda
- Form utama:
  - title, slug, category, institution, type, status, schedule, location, registration, recurrence
- Kolom table:
  - title, type, status, start_at, registered_count, featured
- Filter:
  - type, status, institution, requires_registration
- Action:
  - edit, publish, complete

### Institution
- Form utama:
  - profile, type, status, location, contact, accreditation, meta
- Kolom table:
  - name, type, status, phone, founded_year, featured
- Filter:
  - type, status, featured
- Action:
  - edit

### Leader
- Form utama:
  - link user, institution, profile, organization, position level, period, contact, bio
- Kolom table:
  - name, organization, institution, period, status, order
- Filter:
  - organization, institution, status
- Action:
  - edit

## UX recommendation

- Gunakan navigation group yang jelas: `Konten`, `Lazismu`, `Organisasi`.
- Pakai badge enum di table untuk mempercepat scanning status.
- Prioritaskan `Create`, `Edit`, dan status actions di action group agar panel tidak ramai.
- Untuk campaign builder, admin diarahkan ke repeater field daripada edit JSON mentah.
- Donation verification harus tampil jelas via filter `manual payment` dan action `Verifikasi` / `Tolak`.
- Post editorial workflow harus mengutamakan `Draft`, `Review`, dan `Published`.
- Permission diterapkan per resource sehingga menu otomatis tersembunyi untuk role yang tidak relevan.
