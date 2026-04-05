# Laravel 12 + Filament 5 Backend Scaffold

Dokumen ini berisi scaffold backend modular untuk:

- CMS: `categories`, `posts`, `agendas`, `institutions`, `leaders`
- Backend support: `campaigns`, `donations`, `distributions`
- Access: role & permission
- Service layer dasar

Struktur dan kode di bawah mengikuti baseline terbaru yang saat ini lebih aman dipakai:

- Laravel 12
- MySQL
- FilamentPHP v5
- Spatie Laravel Permission

## 1. Struktur File

```text
app/
├── Domain/
│   ├── Access/
│   │   ├── Enums/
│   │   │   └── PermissionEnum.php
│   │   └── Enums/
│   │       └── RoleEnum.php
│   ├── Campaign/
│   │   ├── Enums/
│   │   │   ├── CampaignProgressTypeEnum.php
│   │   │   └── CampaignStatusEnum.php
│   │   ├── Models/
│   │   │   └── Campaign.php
│   │   └── Services/
│   │       └── CampaignService.php
│   ├── Content/
│   │   ├── Enums/
│   │   │   ├── AgendaStatusEnum.php
│   │   │   ├── AgendaTypeEnum.php
│   │   │   ├── CategoryTypeEnum.php
│   │   │   ├── PostStatusEnum.php
│   │   │   └── PostTypeEnum.php
│   │   ├── Models/
│   │   │   ├── Agenda.php
│   │   │   ├── Category.php
│   │   │   └── Post.php
│   │   └── Services/
│   │       ├── AgendaService.php
│   │       ├── CategoryService.php
│   │       └── PostService.php
│   ├── Donation/
│   │   ├── Enums/
│   │   │   ├── DonationStatusEnum.php
│   │   │   └── DonationVerificationStatusEnum.php
│   │   ├── Models/
│   │   │   ├── Donation.php
│   │   │   └── DonationVerification.php
│   │   └── Services/
│   │       └── DonationService.php
│   ├── Organization/
│   │   ├── Enums/
│   │   │   ├── InstitutionStatusEnum.php
│   │   │   ├── InstitutionTypeEnum.php
│   │   │   ├── LeaderOrganizationEnum.php
│   │   │   └── LeaderStatusEnum.php
│   │   ├── Models/
│   │   │   ├── Institution.php
│   │   │   └── Leader.php
│   │   └── Services/
│   │       ├── InstitutionService.php
│   │       └── LeaderService.php
│   ├── Report/
│   │   ├── Enums/
│   │   │   ├── DistributionRecipientTypeEnum.php
│   │   │   ├── DistributionStatusEnum.php
│   │   │   └── DistributionTypeEnum.php
│   │   ├── Models/
│   │   │   └── Distribution.php
│   │   └── Services/
│   │       └── DistributionService.php
│   └── Shared/
│       └── Concerns/
│           └── HasSeoMeta.php
├── Filament/
│   └── Resources/
│       ├── AgendaResource.php
│       ├── CampaignResource.php
│       ├── CategoryResource.php
│       ├── DistributionResource.php
│       ├── DonationResource.php
│       ├── InstitutionResource.php
│       ├── LeaderResource.php
│       └── PostResource.php
database/
├── migrations/
│   ├── 2026_04_04_200000_create_categories_table.php
│   ├── 2026_04_04_200001_create_posts_table.php
│   ├── 2026_04_04_200002_create_agendas_table.php
│   ├── 2026_04_04_200003_create_institutions_table.php
│   ├── 2026_04_04_200004_create_leaders_table.php
│   ├── 2026_04_04_200005_create_campaigns_table.php
│   ├── 2026_04_04_200006_create_donations_table.php
│   ├── 2026_04_04_200007_create_donation_verifications_table.php
│   └── 2026_04_04_200008_create_distributions_table.php
└── seeders/
    └── RoleAndPermissionSeeder.php
```

## 2. Migrations

### `database/migrations/2026_04_04_200000_create_categories_table.php`

Fungsi: master kategori lintas modul CMS dan backend.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('type', 30)->index();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color', 7)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->unique(['type', 'slug']);
            $table->index(['type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
```

### `database/migrations/2026_04_04_200001_create_posts_table.php`

Fungsi: berita/artikel dengan workflow draft-publish, slug, dan SEO JSON.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('institution_id')->nullable()->constrained('institutions')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('type', 30)->default('news')->index();
            $table->string('status', 30)->default('draft')->index();
            $table->string('excerpt')->nullable();
            $table->longText('content');
            $table->string('featured_image')->nullable();
            $table->json('seo_meta')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_featured')->default(false)->index();
            $table->unsignedBigInteger('view_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
```

### `database/migrations/2026_04_04_200002_create_agendas_table.php`

Fungsi: agenda organisasi dan event publik/internal.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('institution_id')->nullable()->constrained('institutions')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('type', 30)->default('other')->index();
            $table->string('status', 30)->default('draft')->index();
            $table->text('description')->nullable();
            $table->dateTime('start_at')->index();
            $table->dateTime('end_at')->nullable()->index();
            $table->string('location_name')->nullable();
            $table->string('location_address')->nullable();
            $table->string('maps_url')->nullable();
            $table->boolean('is_online')->default(false);
            $table->string('meeting_url')->nullable();
            $table->boolean('requires_registration')->default(false);
            $table->unsignedInteger('max_participants')->nullable();
            $table->unsignedInteger('registered_count')->default(0);
            $table->json('meta')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};
```

### `database/migrations/2026_04_04_200003_create_institutions_table.php`

Fungsi: direktori amal usaha atau institusi PCM.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type', 30)->default('other')->index();
            $table->string('status', 30)->default('active')->index();
            $table->string('acronym', 20)->nullable();
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->json('meta')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_featured')->default(false)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institutions');
    }
};
```

### `database/migrations/2026_04_04_200004_create_leaders_table.php`

Fungsi: e-struktur pimpinan organisasi.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leaders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('institution_id')->nullable()->constrained('institutions')->nullOnDelete();
            $table->string('name');
            $table->string('organization', 30)->default('pcm')->index();
            $table->string('position');
            $table->string('period', 20)->index();
            $table->string('photo')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('bio')->nullable();
            $table->string('status', 30)->default('active')->index();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaders');
    }
};
```

### `database/migrations/2026_04_04_200005_create_campaigns_table.php`

Fungsi: campaign Lazismu yang config-driven melalui JSON.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('institution_id')->nullable()->constrained('institutions')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('status', 30)->default('draft')->index();
            $table->string('progress_type', 20)->default('amount')->index();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('target_amount')->nullable();
            $table->unsignedInteger('target_unit')->nullable();
            $table->string('unit_label', 30)->nullable();
            $table->unsignedBigInteger('collected_amount')->default(0);
            $table->unsignedInteger('collected_unit')->default(0);
            $table->unsignedInteger('verified_donor_count')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('config')->nullable();
            $table->json('payment_config')->nullable();
            $table->json('seo_meta')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
```

### `database/migrations/2026_04_04_200006_create_donations_table.php`

Fungsi: transaksi donasi yang mendukung verifikasi admin.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('transaction_code', 50)->unique();
            $table->string('payer_name')->nullable();
            $table->string('payer_email')->nullable();
            $table->string('payer_phone', 20)->nullable();
            $table->boolean('is_anonymous')->default(false)->index();
            $table->unsignedBigInteger('amount')->default(0);
            $table->unsignedInteger('quantity')->default(0);
            $table->string('unit_label', 30)->nullable();
            $table->text('message')->nullable();
            $table->string('payment_method', 50)->default('manual_transfer');
            $table->string('payment_channel', 50)->nullable();
            $table->string('status', 30)->default('pending')->index();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['campaign_id', 'status']);
            $table->index(['payer_email', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
```

### `database/migrations/2026_04_04_200007_create_donation_verifications_table.php`

Fungsi: audit trail verifikasi donasi.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_id')->constrained('donations')->cascadeOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 30)->index();
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_verifications');
    }
};
```

### `database/migrations/2026_04_04_200008_create_distributions_table.php`

Fungsi: penyaluran dan impact transparency.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->nullOnDelete();
            $table->foreignId('institution_id')->nullable()->constrained('institutions')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('distributed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('distribution_code', 50)->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('recipient_type', 30)->default('general')->index();
            $table->string('recipient_name')->nullable();
            $table->unsignedInteger('recipient_count')->default(1);
            $table->unsignedBigInteger('distributed_amount')->default(0);
            $table->unsignedInteger('distributed_unit')->default(0);
            $table->string('unit_label', 30)->nullable();
            $table->string('distribution_type', 30)->default('cash')->index();
            $table->string('status', 30)->default('draft')->index();
            $table->date('distribution_date')->nullable();
            $table->string('location')->nullable();
            $table->json('evidence_files')->nullable();
            $table->json('meta')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('reported_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distributions');
    }
};
```

## 3. Enum / Constant

### `app/Domain/Access/Enums/RoleEnum.php`

Fungsi: konsolidasi role agar tidak hardcoded di banyak tempat.

```php
<?php

namespace App\Domain\Access\Enums;

enum RoleEnum: string
{
    case SuperAdmin = 'super_admin';
    case AdminPcm = 'admin_pcm';
    case AdminLazismu = 'admin_lazismu';
    case Contributor = 'contributor';
}
```

### `app/Domain/Access/Enums/PermissionEnum.php`

Fungsi: daftar permission inti sistem.

```php
<?php

namespace App\Domain\Access\Enums;

enum PermissionEnum: string
{
    case ManageCategories = 'manage_categories';
    case ManagePosts = 'manage_posts';
    case PublishPosts = 'publish_posts';
    case ManageAgendas = 'manage_agendas';
    case PublishAgendas = 'publish_agendas';
    case ManageInstitutions = 'manage_institutions';
    case ManageLeaders = 'manage_leaders';
    case ManageCampaigns = 'manage_campaigns';
    case PublishCampaigns = 'publish_campaigns';
    case ManageDonations = 'manage_donations';
    case VerifyDonations = 'verify_donations';
    case ManageDistributions = 'manage_distributions';
    case ApproveDistributions = 'approve_distributions';
    case ManageSettings = 'manage_settings';
}
```

### `app/Domain/Content/Enums/CategoryTypeEnum.php`

```php
<?php

namespace App\Domain\Content\Enums;

enum CategoryTypeEnum: string
{
    case Post = 'post';
    case Agenda = 'agenda';
    case Campaign = 'campaign';
    case Distribution = 'distribution';
}
```

### `app/Domain/Content/Enums/PostTypeEnum.php`

```php
<?php

namespace App\Domain\Content\Enums;

enum PostTypeEnum: string
{
    case News = 'news';
    case Article = 'article';
    case Announcement = 'announcement';
    case Study = 'study';
}
```

### `app/Domain/Content/Enums/PostStatusEnum.php`

```php
<?php

namespace App\Domain\Content\Enums;

enum PostStatusEnum: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';
}
```

### `app/Domain/Content/Enums/AgendaTypeEnum.php`

```php
<?php

namespace App\Domain\Content\Enums;

enum AgendaTypeEnum: string
{
    case Kajian = 'kajian';
    case Meeting = 'meeting';
    case Social = 'social';
    case Education = 'education';
    case Other = 'other';
}
```

### `app/Domain/Content/Enums/AgendaStatusEnum.php`

```php
<?php

namespace App\Domain\Content\Enums;

enum AgendaStatusEnum: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Cancelled = 'cancelled';
    case Completed = 'completed';
}
```

### `app/Domain/Organization/Enums/InstitutionTypeEnum.php`

```php
<?php

namespace App\Domain\Organization\Enums;

enum InstitutionTypeEnum: string
{
    case School = 'school';
    case Kindergarten = 'kindergarten';
    case Clinic = 'clinic';
    case Mosque = 'mosque';
    case Finance = 'finance';
    case Enterprise = 'enterprise';
    case Social = 'social';
    case Other = 'other';
}
```

### `app/Domain/Organization/Enums/InstitutionStatusEnum.php`

```php
<?php

namespace App\Domain\Organization\Enums;

enum InstitutionStatusEnum: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Development = 'development';
}
```

### `app/Domain/Organization/Enums/LeaderOrganizationEnum.php`

```php
<?php

namespace App\Domain\Organization\Enums;

enum LeaderOrganizationEnum: string
{
    case Pcm = 'pcm';
    case Pcw = 'pcw';
    case Lazismu = 'lazismu';
    case Institution = 'institution';
}
```

### `app/Domain/Organization/Enums/LeaderStatusEnum.php`

```php
<?php

namespace App\Domain\Organization\Enums;

enum LeaderStatusEnum: string
{
    case Active = 'active';
    case Inactive = 'inactive';
}
```

### `app/Domain/Campaign/Enums/CampaignStatusEnum.php`

```php
<?php

namespace App\Domain\Campaign\Enums;

enum CampaignStatusEnum: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Paused = 'paused';
    case Completed = 'completed';
    case Closed = 'closed';
}
```

### `app/Domain/Campaign/Enums/CampaignProgressTypeEnum.php`

```php
<?php

namespace App\Domain\Campaign\Enums;

enum CampaignProgressTypeEnum: string
{
    case Amount = 'amount';
    case Unit = 'unit';
}
```

### `app/Domain/Donation/Enums/DonationStatusEnum.php`

```php
<?php

namespace App\Domain\Donation\Enums;

enum DonationStatusEnum: string
{
    case Pending = 'pending';
    case Verified = 'verified';
    case Rejected = 'rejected';
}
```

### `app/Domain/Donation/Enums/DonationVerificationStatusEnum.php`

```php
<?php

namespace App\Domain\Donation\Enums;

enum DonationVerificationStatusEnum: string
{
    case Verified = 'verified';
    case Rejected = 'rejected';
}
```

### `app/Domain/Report/Enums/DistributionRecipientTypeEnum.php`

```php
<?php

namespace App\Domain\Report\Enums;

enum DistributionRecipientTypeEnum: string
{
    case Fakir = 'fakir';
    case Miskin = 'miskin';
    case Amil = 'amil';
    case Muallaf = 'muallaf';
    case Gharimin = 'gharimin';
    case Fisabilillah = 'fisabilillah';
    case IbnuSabil = 'ibnu_sabil';
    case General = 'general';
    case Institution = 'institution';
}
```

### `app/Domain/Report/Enums/DistributionTypeEnum.php`

```php
<?php

namespace App\Domain\Report\Enums;

enum DistributionTypeEnum: string
{
    case Cash = 'cash';
    case Goods = 'goods';
    case Service = 'service';
    case Mixed = 'mixed';
}
```

### `app/Domain/Report/Enums/DistributionStatusEnum.php`

```php
<?php

namespace App\Domain\Report\Enums;

enum DistributionStatusEnum: string
{
    case Draft = 'draft';
    case Approved = 'approved';
    case Distributed = 'distributed';
    case Reported = 'reported';
}
```

## 4. Shared Concern

### `app/Domain/Shared/Concerns/HasSeoMeta.php`

Fungsi: accessor helper untuk field `seo_meta` JSON.

```php
<?php

namespace App\Domain\Shared\Concerns;

trait HasSeoMeta
{
    public function getSeoTitleAttribute(): ?string
    {
        return $this->seo_meta['title'] ?? null;
    }

    public function getSeoDescriptionAttribute(): ?string
    {
        return $this->seo_meta['description'] ?? null;
    }

    public function getSeoKeywordsAttribute(): array
    {
        return $this->seo_meta['keywords'] ?? [];
    }
}
```

## 5. Models

### `app/Domain/Content/Models/Category.php`

Fungsi: kategori shared untuk post, agenda, campaign, dan distribution.

```php
<?php

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'type',
        'description',
        'icon',
        'color',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
```

### `app/Domain/Content/Models/Post.php`

Fungsi: model artikel/berita CMS dengan publish workflow.

```php
<?php

namespace App\Domain\Content\Models;

use App\Domain\Content\Enums\PostStatusEnum;
use App\Domain\Content\Enums\PostTypeEnum;
use App\Domain\Shared\Concerns\HasSeoMeta;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasSeoMeta;
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'author_id',
        'institution_id',
        'title',
        'slug',
        'type',
        'status',
        'excerpt',
        'content',
        'featured_image',
        'seo_meta',
        'published_at',
        'published_by',
        'is_featured',
        'view_count',
    ];

    protected function casts(): array
    {
        return [
            'type' => PostTypeEnum::class,
            'status' => PostStatusEnum::class,
            'seo_meta' => 'array',
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
            'view_count' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', PostStatusEnum::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
```

### `app/Domain/Content/Models/Agenda.php`

Fungsi: model agenda organisasi.

```php
<?php

namespace App\Domain\Content\Models;

use App\Domain\Content\Enums\AgendaStatusEnum;
use App\Domain\Content\Enums\AgendaTypeEnum;
use App\Domain\Organization\Models\Institution;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agenda extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'institution_id',
        'created_by',
        'title',
        'slug',
        'type',
        'status',
        'description',
        'start_at',
        'end_at',
        'location_name',
        'location_address',
        'maps_url',
        'is_online',
        'meeting_url',
        'requires_registration',
        'max_participants',
        'registered_count',
        'meta',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => AgendaTypeEnum::class,
            'status' => AgendaStatusEnum::class,
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'is_online' => 'boolean',
            'requires_registration' => 'boolean',
            'max_participants' => 'integer',
            'registered_count' => 'integer',
            'meta' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
```

### `app/Domain/Organization/Models/Institution.php`

Fungsi: amal usaha atau unit organisasi.

```php
<?php

namespace App\Domain\Organization\Models;

use App\Domain\Organization\Enums\InstitutionStatusEnum;
use App\Domain\Organization\Enums\InstitutionTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Institution extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'status',
        'acronym',
        'tagline',
        'description',
        'address',
        'phone',
        'email',
        'website',
        'logo',
        'meta',
        'sort_order',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'type' => InstitutionTypeEnum::class,
            'status' => InstitutionStatusEnum::class,
            'meta' => 'array',
            'sort_order' => 'integer',
            'is_featured' => 'boolean',
        ];
    }

    public function leaders(): HasMany
    {
        return $this->hasMany(Leader::class);
    }
}
```

### `app/Domain/Organization/Models/Leader.php`

Fungsi: data pimpinan publik.

```php
<?php

namespace App\Domain\Organization\Models;

use App\Domain\Organization\Enums\LeaderOrganizationEnum;
use App\Domain\Organization\Enums\LeaderStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leader extends Model
{
    protected $fillable = [
        'user_id',
        'institution_id',
        'name',
        'organization',
        'position',
        'period',
        'photo',
        'phone',
        'email',
        'bio',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'organization' => LeaderOrganizationEnum::class,
            'status' => LeaderStatusEnum::class,
            'sort_order' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
}
```

### `app/Domain/Campaign/Models/Campaign.php`

Fungsi: campaign donasi config-driven.

```php
<?php

namespace App\Domain\Campaign\Models;

use App\Domain\Campaign\Enums\CampaignProgressTypeEnum;
use App\Domain\Campaign\Enums\CampaignStatusEnum;
use App\Domain\Shared\Concerns\HasSeoMeta;
use App\Domain\Content\Models\Category;
use App\Domain\Donation\Models\Donation;
use App\Domain\Organization\Models\Institution;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use HasSeoMeta;
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'institution_id',
        'created_by',
        'title',
        'slug',
        'status',
        'progress_type',
        'short_description',
        'description',
        'target_amount',
        'target_unit',
        'unit_label',
        'collected_amount',
        'collected_unit',
        'verified_donor_count',
        'start_date',
        'end_date',
        'config',
        'payment_config',
        'seo_meta',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => CampaignStatusEnum::class,
            'progress_type' => CampaignProgressTypeEnum::class,
            'target_amount' => 'integer',
            'target_unit' => 'integer',
            'collected_amount' => 'integer',
            'collected_unit' => 'integer',
            'verified_donor_count' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'config' => 'array',
            'payment_config' => 'array',
            'seo_meta' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }
}
```

### `app/Domain/Donation/Models/Donation.php`

Fungsi: transaksi donasi dan status verifikasi.

```php
<?php

namespace App\Domain\Donation\Models;

use App\Domain\Campaign\Models\Campaign;
use App\Domain\Donation\Enums\DonationStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Donation extends Model
{
    protected $fillable = [
        'campaign_id',
        'user_id',
        'transaction_code',
        'payer_name',
        'payer_email',
        'payer_phone',
        'is_anonymous',
        'amount',
        'quantity',
        'unit_label',
        'message',
        'payment_method',
        'payment_channel',
        'status',
        'verified_by',
        'submitted_at',
        'verified_at',
        'rejected_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'is_anonymous' => 'boolean',
            'amount' => 'integer',
            'quantity' => 'integer',
            'status' => DonationStatusEnum::class,
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
            'rejected_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(DonationVerification::class);
    }
}
```

### `app/Domain/Donation/Models/DonationVerification.php`

Fungsi: histori keputusan verifikasi.

```php
<?php

namespace App\Domain\Donation\Models;

use App\Domain\Donation\Enums\DonationVerificationStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonationVerification extends Model
{
    protected $fillable = [
        'donation_id',
        'verified_by',
        'status',
        'notes',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'status' => DonationVerificationStatusEnum::class,
            'meta' => 'array',
        ];
    }

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
```

### `app/Domain/Report/Models/Distribution.php`

Fungsi: penyaluran dan dampak program untuk transparansi publik.

```php
<?php

namespace App\Domain\Report\Models;

use App\Domain\Campaign\Models\Campaign;
use App\Domain\Organization\Models\Institution;
use App\Domain\Report\Enums\DistributionRecipientTypeEnum;
use App\Domain\Report\Enums\DistributionStatusEnum;
use App\Domain\Report\Enums\DistributionTypeEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Distribution extends Model
{
    protected $fillable = [
        'campaign_id',
        'institution_id',
        'created_by',
        'approved_by',
        'distributed_by',
        'distribution_code',
        'title',
        'description',
        'recipient_type',
        'recipient_name',
        'recipient_count',
        'distributed_amount',
        'distributed_unit',
        'unit_label',
        'distribution_type',
        'status',
        'distribution_date',
        'location',
        'evidence_files',
        'meta',
        'notes',
        'approved_at',
        'reported_at',
    ];

    protected function casts(): array
    {
        return [
            'recipient_type' => DistributionRecipientTypeEnum::class,
            'distribution_type' => DistributionTypeEnum::class,
            'status' => DistributionStatusEnum::class,
            'recipient_count' => 'integer',
            'distributed_amount' => 'integer',
            'distributed_unit' => 'integer',
            'distribution_date' => 'date',
            'evidence_files' => 'array',
            'meta' => 'array',
            'approved_at' => 'datetime',
            'reported_at' => 'datetime',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
```

## 6. Service Skeleton

### `app/Domain/Content/Services/CategoryService.php`

Fungsi: create/update kategori tanpa business logic di controller/resource.

```php
<?php

namespace App\Domain\Content\Services;

use App\Domain\Content\Models\Category;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CategoryService
{
    public function create(array $payload): Category
    {
        $payload['slug'] = $payload['slug'] ?? Str::slug($payload['name']);

        return Category::query()->create($payload);
    }

    public function update(Category $category, array $payload): Category
    {
        if (blank(Arr::get($payload, 'slug'))) {
            $payload['slug'] = Str::slug((string) Arr::get($payload, 'name', $category->name));
        }

        $category->update($payload);

        return $category->refresh();
    }
}
```

### `app/Domain/Content/Services/PostService.php`

Fungsi: workflow post draft/publish dan slug + SEO JSON.

```php
<?php

namespace App\Domain\Content\Services;

use App\Domain\Content\Enums\PostStatusEnum;
use App\Domain\Content\Models\Post;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostService
{
    public function create(array $payload): Post
    {
        return DB::transaction(function () use ($payload): Post {
            $payload['slug'] = $payload['slug'] ?? Str::slug($payload['title']);
            $payload['status'] = $payload['status'] ?? PostStatusEnum::Draft;
            $payload['seo_meta'] = $this->normalizeSeoMeta(Arr::get($payload, 'seo_meta', []), $payload);

            return Post::query()->create($payload);
        });
    }

    public function update(Post $post, array $payload): Post
    {
        return DB::transaction(function () use ($post, $payload): Post {
            $payload['slug'] = $payload['slug'] ?? Str::slug((string) Arr::get($payload, 'title', $post->title));
            $payload['seo_meta'] = $this->normalizeSeoMeta(Arr::get($payload, 'seo_meta', $post->seo_meta ?? []), $payload, $post);

            $post->update($payload);

            return $post->refresh();
        });
    }

    public function publish(Post $post, User $actor): Post
    {
        $post->update([
            'status' => PostStatusEnum::Published,
            'published_at' => now(),
            'published_by' => $actor->getKey(),
        ]);

        return $post->refresh();
    }

    public function archive(Post $post): Post
    {
        $post->update([
            'status' => PostStatusEnum::Archived,
        ]);

        return $post->refresh();
    }

    private function normalizeSeoMeta(array $seoMeta, array $payload, ?Post $post = null): array
    {
        return [
            'title' => $seoMeta['title'] ?? Arr::get($payload, 'title', $post?->title),
            'description' => $seoMeta['description'] ?? Arr::get($payload, 'excerpt', $post?->excerpt),
            'keywords' => array_values($seoMeta['keywords'] ?? []),
            'canonical_url' => $seoMeta['canonical_url'] ?? null,
            'robots' => $seoMeta['robots'] ?? 'index,follow',
        ];
    }
}
```

### `app/Domain/Content/Services/AgendaService.php`

Fungsi: orkestrasi agenda dan status publish.

```php
<?php

namespace App\Domain\Content\Services;

use App\Domain\Content\Enums\AgendaStatusEnum;
use App\Domain\Content\Models\Agenda;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AgendaService
{
    public function create(array $payload): Agenda
    {
        return DB::transaction(function () use ($payload): Agenda {
            $payload['slug'] = $payload['slug'] ?? Str::slug($payload['title']);
            $payload['status'] = $payload['status'] ?? AgendaStatusEnum::Draft;

            return Agenda::query()->create($payload);
        });
    }

    public function update(Agenda $agenda, array $payload): Agenda
    {
        $payload['slug'] = $payload['slug'] ?? Str::slug((string) ($payload['title'] ?? $agenda->title));

        $agenda->update($payload);

        return $agenda->refresh();
    }

    public function publish(Agenda $agenda): Agenda
    {
        $agenda->update([
            'status' => AgendaStatusEnum::Published,
            'published_at' => now(),
        ]);

        return $agenda->refresh();
    }

    public function complete(Agenda $agenda): Agenda
    {
        $agenda->update(['status' => AgendaStatusEnum::Completed]);

        return $agenda->refresh();
    }
}
```

### `app/Domain/Organization/Services/InstitutionService.php`

Fungsi: service amal usaha.

```php
<?php

namespace App\Domain\Organization\Services;

use App\Domain\Organization\Models\Institution;
use Illuminate\Support\Str;

class InstitutionService
{
    public function create(array $payload): Institution
    {
        $payload['slug'] = $payload['slug'] ?? Str::slug($payload['name']);

        return Institution::query()->create($payload);
    }

    public function update(Institution $institution, array $payload): Institution
    {
        $payload['slug'] = $payload['slug'] ?? Str::slug((string) ($payload['name'] ?? $institution->name));

        $institution->update($payload);

        return $institution->refresh();
    }
}
```

### `app/Domain/Organization/Services/LeaderService.php`

Fungsi: service struktur pimpinan.

```php
<?php

namespace App\Domain\Organization\Services;

use App\Domain\Organization\Models\Leader;

class LeaderService
{
    public function create(array $payload): Leader
    {
        return Leader::query()->create($payload);
    }

    public function update(Leader $leader, array $payload): Leader
    {
        $leader->update($payload);

        return $leader->refresh();
    }
}
```

### `app/Domain/Campaign/Services/CampaignService.php`

Fungsi: create/update campaign dan validasi config JSON level awal.

```php
<?php

namespace App\Domain\Campaign\Services;

use App\Domain\Campaign\Enums\CampaignStatusEnum;
use App\Domain\Campaign\Models\Campaign;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CampaignService
{
    public function create(array $payload): Campaign
    {
        return DB::transaction(function () use ($payload): Campaign {
            $payload['slug'] = $payload['slug'] ?? Str::slug($payload['title']);
            $payload['status'] = $payload['status'] ?? CampaignStatusEnum::Draft;
            $payload['config'] = $this->normalizeConfig(Arr::get($payload, 'config', []));

            return Campaign::query()->create($payload);
        });
    }

    public function update(Campaign $campaign, array $payload): Campaign
    {
        $payload['slug'] = $payload['slug'] ?? Str::slug((string) ($payload['title'] ?? $campaign->title));
        $payload['config'] = $this->normalizeConfig(Arr::get($payload, 'config', $campaign->config ?? []));

        $campaign->update($payload);

        return $campaign->refresh();
    }

    public function publish(Campaign $campaign): Campaign
    {
        $campaign->update([
            'status' => CampaignStatusEnum::Active,
            'published_at' => now(),
        ]);

        return $campaign->refresh();
    }

    private function normalizeConfig(array $config): array
    {
        if (! is_array($config)) {
            throw new InvalidArgumentException('Campaign config must be an array.');
        }

        return [
            'version' => $config['version'] ?? 1,
            'form' => $config['form'] ?? ['fields' => []],
            'behavior' => $config['behavior'] ?? [],
        ];
    }
}
```

### `app/Domain/Donation/Services/DonationService.php`

Fungsi: submit, verify, reject, dan sinkron progress campaign.

```php
<?php

namespace App\Domain\Donation\Services;

use App\Domain\Donation\Enums\DonationStatusEnum;
use App\Domain\Donation\Enums\DonationVerificationStatusEnum;
use App\Domain\Donation\Models\Donation;
use App\Domain\Donation\Models\DonationVerification;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DonationService
{
    public function submit(array $payload): Donation
    {
        return DB::transaction(function () use ($payload): Donation {
            $payload['status'] = $payload['status'] ?? DonationStatusEnum::Pending;
            $payload['submitted_at'] = $payload['submitted_at'] ?? now();

            return Donation::query()->create($payload);
        });
    }

    public function verify(Donation $donation, User $actor, ?string $notes = null): Donation
    {
        return DB::transaction(function () use ($donation, $actor, $notes): Donation {
            $donation->update([
                'status' => DonationStatusEnum::Verified,
                'verified_by' => $actor->getKey(),
                'verified_at' => now(),
                'rejected_at' => null,
            ]);

            DonationVerification::query()->create([
                'donation_id' => $donation->getKey(),
                'verified_by' => $actor->getKey(),
                'status' => DonationVerificationStatusEnum::Verified,
                'notes' => $notes,
            ]);

            if ($donation->campaign) {
                $donation->campaign->increment('collected_amount', (int) $donation->amount);
                $donation->campaign->increment('collected_unit', (int) $donation->quantity);
                $donation->campaign->increment('verified_donor_count');
            }

            return $donation->refresh();
        });
    }

    public function reject(Donation $donation, User $actor, ?string $notes = null): Donation
    {
        return DB::transaction(function () use ($donation, $actor, $notes): Donation {
            $donation->update([
                'status' => DonationStatusEnum::Rejected,
                'verified_by' => $actor->getKey(),
                'rejected_at' => now(),
            ]);

            DonationVerification::query()->create([
                'donation_id' => $donation->getKey(),
                'verified_by' => $actor->getKey(),
                'status' => DonationVerificationStatusEnum::Rejected,
                'notes' => $notes,
            ]);

            return $donation->refresh();
        });
    }
}
```

### `app/Domain/Report/Services/DistributionService.php`

Fungsi: distribusi, approval, dan reporting transparency.

```php
<?php

namespace App\Domain\Report\Services;

use App\Domain\Report\Enums\DistributionStatusEnum;
use App\Domain\Report\Models\Distribution;
use App\Models\User;
use Illuminate\Support\Str;

class DistributionService
{
    public function create(array $payload): Distribution
    {
        $payload['distribution_code'] = $payload['distribution_code'] ?? 'DST-' . Str::upper(Str::random(8));
        $payload['status'] = $payload['status'] ?? DistributionStatusEnum::Draft;

        return Distribution::query()->create($payload);
    }

    public function approve(Distribution $distribution, User $actor): Distribution
    {
        $distribution->update([
            'status' => DistributionStatusEnum::Approved,
            'approved_by' => $actor->getKey(),
            'approved_at' => now(),
        ]);

        return $distribution->refresh();
    }

    public function markDistributed(Distribution $distribution, User $actor): Distribution
    {
        $distribution->update([
            'status' => DistributionStatusEnum::Distributed,
            'distributed_by' => $actor->getKey(),
        ]);

        return $distribution->refresh();
    }

    public function markReported(Distribution $distribution): Distribution
    {
        $distribution->update([
            'status' => DistributionStatusEnum::Reported,
            'reported_at' => now(),
        ]);

        return $distribution->refresh();
    }
}
```

## 7. Filament Resource Skeleton

Catatan:

- Struktur di bawah menekankan boundary domain dan service layer.
- Untuk repo aktif yang memakai Filament 5, sesuaikan signature `form()` dan `table()` dengan API panel yang sekarang dipakai project.
- Resource hanya memanggil model/service.
- Business logic tetap di service layer.

### `app/Filament/Resources/CategoryResource.php`

Fungsi: CRUD kategori.

```php
<?php

namespace App\Filament\Resources;

use App\Domain\Content\Enums\CategoryTypeEnum;
use App\Domain\Content\Models\Category;
use App\Filament\Resources\CategoryResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'CMS';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('parent_id')
                ->relationship('parent', 'name')
                ->searchable()
                ->preload(),
            Forms\Components\TextInput::make('name')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug((string) $state))),
            Forms\Components\TextInput::make('slug')->required(),
            Forms\Components\Select::make('type')
                ->options(collect(CategoryTypeEnum::cases())->mapWithKeys(fn ($case) => [$case->value => $case->name])->all())
                ->required(),
            Forms\Components\Textarea::make('description'),
            Forms\Components\TextInput::make('icon'),
            Forms\Components\TextInput::make('color'),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('type')->badge(),
            Tables\Columns\TextColumn::make('slug'),
            Tables\Columns\IconColumn::make('is_active')->boolean(),
        ])->actions([
            Tables\Actions\EditAction::make(),
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
```

### `app/Filament/Resources/PostResource.php`

Fungsi: CRUD post dan workflow publish.

```php
<?php

namespace App\Filament\Resources;

use App\Domain\Content\Enums\PostStatusEnum;
use App\Domain\Content\Enums\PostTypeEnum;
use App\Domain\Content\Models\Post;
use App\Filament\Resources\PostResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationGroup = 'CMS';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug((string) $state))),
            Forms\Components\TextInput::make('slug')->required(),
            Forms\Components\Select::make('category_id')->relationship('category', 'name')->searchable()->preload(),
            Forms\Components\Select::make('institution_id')->relationship('institution', 'name')->searchable()->preload(),
            Forms\Components\Select::make('type')
                ->options(collect(PostTypeEnum::cases())->mapWithKeys(fn ($case) => [$case->value => $case->name])->all())
                ->required(),
            Forms\Components\Select::make('status')
                ->options(collect(PostStatusEnum::cases())->mapWithKeys(fn ($case) => [$case->value => $case->name])->all())
                ->required(),
            Forms\Components\Textarea::make('excerpt'),
            Forms\Components\RichEditor::make('content')->required(),
            Forms\Components\KeyValue::make('seo_meta')
                ->keyLabel('Key')
                ->valueLabel('Value'),
            Forms\Components\Toggle::make('is_featured')->default(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('type')->badge(),
            Tables\Columns\TextColumn::make('status')->badge(),
            Tables\Columns\TextColumn::make('published_at')->dateTime(),
        ])->actions([
            Tables\Actions\EditAction::make(),
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
```

### `app/Filament/Resources/AgendaResource.php`

Fungsi: manajemen agenda.

```php
<?php

namespace App\Filament\Resources;

use App\Domain\Content\Enums\AgendaStatusEnum;
use App\Domain\Content\Enums\AgendaTypeEnum;
use App\Domain\Content\Models\Agenda;
use App\Filament\Resources\AgendaResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AgendaResource extends Resource
{
    protected static ?string $model = Agenda::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'CMS';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug((string) $state))),
            Forms\Components\TextInput::make('slug')->required(),
            Forms\Components\Select::make('category_id')->relationship('category', 'name')->searchable()->preload(),
            Forms\Components\Select::make('institution_id')->relationship('institution', 'name')->searchable()->preload(),
            Forms\Components\Select::make('type')
                ->options(collect(AgendaTypeEnum::cases())->mapWithKeys(fn ($case) => [$case->value => $case->name])->all())
                ->required(),
            Forms\Components\Select::make('status')
                ->options(collect(AgendaStatusEnum::cases())->mapWithKeys(fn ($case) => [$case->value => $case->name])->all())
                ->required(),
            Forms\Components\DateTimePicker::make('start_at')->required(),
            Forms\Components\DateTimePicker::make('end_at'),
            Forms\Components\Textarea::make('description'),
            Forms\Components\TextInput::make('location_name'),
            Forms\Components\TextInput::make('location_address'),
            Forms\Components\Toggle::make('is_online')->default(false),
            Forms\Components\TextInput::make('meeting_url'),
            Forms\Components\Toggle::make('requires_registration')->default(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('type')->badge(),
            Tables\Columns\TextColumn::make('status')->badge(),
            Tables\Columns\TextColumn::make('start_at')->dateTime(),
        ])->actions([
            Tables\Actions\EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgendas::route('/'),
            'create' => Pages\CreateAgenda::route('/create'),
            'edit' => Pages\EditAgenda::route('/{record}/edit'),
        ];
    }
}
```

### `app/Filament/Resources/InstitutionResource.php`

Fungsi: CRUD amal usaha.

```php
<?php

namespace App\Filament\Resources;

use App\Domain\Organization\Enums\InstitutionStatusEnum;
use App\Domain\Organization\Enums\InstitutionTypeEnum;
use App\Domain\Organization\Models\Institution;
use App\Filament\Resources\InstitutionResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class InstitutionResource extends Resource
{
    protected static ?string $model = Institution::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'CMS';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug((string) $state))),
            Forms\Components\TextInput::make('slug')->required(),
            Forms\Components\Select::make('type')
                ->options(collect(InstitutionTypeEnum::cases())->mapWithKeys(fn ($case) => [$case->value => $case->name])->all())
                ->required(),
            Forms\Components\Select::make('status')
                ->options(collect(InstitutionStatusEnum::cases())->mapWithKeys(fn ($case) => [$case->value => $case->name])->all())
                ->required(),
            Forms\Components\TextInput::make('acronym'),
            Forms\Components\TextInput::make('tagline'),
            Forms\Components\Textarea::make('description'),
            Forms\Components\Textarea::make('address'),
            Forms\Components\TextInput::make('phone'),
            Forms\Components\TextInput::make('email')->email(),
            Forms\Components\TextInput::make('website')->url(),
            Forms\Components\KeyValue::make('meta'),
            Forms\Components\Toggle::make('is_featured')->default(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('type')->badge(),
            Tables\Columns\TextColumn::make('status')->badge(),
            Tables\Columns\IconColumn::make('is_featured')->boolean(),
        ])->actions([
            Tables\Actions\EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInstitutions::route('/'),
            'create' => Pages\CreateInstitution::route('/create'),
            'edit' => Pages\EditInstitution::route('/{record}/edit'),
        ];
    }
}
```

### `app/Filament/Resources/LeaderResource.php`

Fungsi: e-struktur pimpinan.

```php
<?php

namespace App\Filament\Resources;

use App\Domain\Organization\Enums\LeaderOrganizationEnum;
use App\Domain\Organization\Enums\LeaderStatusEnum;
use App\Domain\Organization\Models\Leader;
use App\Filament\Resources\LeaderResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeaderResource extends Resource
{
    protected static ?string $model = Leader::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'CMS';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('institution_id')->relationship('institution', 'name')->searchable()->preload(),
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\Select::make('organization')
                ->options(collect(LeaderOrganizationEnum::cases())->mapWithKeys(fn ($case) => [$case->value => $case->name])->all())
                ->required(),
            Forms\Components\TextInput::make('position')->required(),
            Forms\Components\TextInput::make('period')->required(),
            Forms\Components\TextInput::make('phone'),
            Forms\Components\TextInput::make('email')->email(),
            Forms\Components\Textarea::make('bio'),
            Forms\Components\Select::make('status')
                ->options(collect(LeaderStatusEnum::cases())->mapWithKeys(fn ($case) => [$case->value => $case->name])->all())
                ->required(),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('organization')->badge(),
            Tables\Columns\TextColumn::make('position'),
            Tables\Columns\TextColumn::make('period'),
            Tables\Columns\TextColumn::make('status')->badge(),
        ])->actions([
            Tables\Actions\EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaders::route('/'),
            'create' => Pages\CreateLeader::route('/create'),
            'edit' => Pages\EditLeader::route('/{record}/edit'),
        ];
    }
}
```

### `app/Filament/Resources/CampaignResource.php`

Fungsi: admin builder campaign.

```php
<?php

namespace App\Filament\Resources;

use App\Domain\Campaign\Enums\CampaignProgressTypeEnum;
use App\Domain\Campaign\Enums\CampaignStatusEnum;
use App\Domain\Campaign\Models\Campaign;
use App\Filament\Resources\CampaignResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;
    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static ?string $navigationGroup = 'Lazismu';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug((string) $state))),
            Forms\Components\TextInput::make('slug')->required(),
            Forms\Components\Select::make('category_id')->relationship('category', 'name')->searchable()->preload(),
            Forms\Components\Select::make('institution_id')->relationship('institution', 'name')->searchable()->preload(),
            Forms\Components\Select::make('status')
                ->options(collect(CampaignStatusEnum::cases())->mapWithKeys(fn ($case) => [$case->value => $case->name])->all())
                ->required(),
            Forms\Components\Select::make('progress_type')
                ->options(collect(CampaignProgressTypeEnum::cases())->mapWithKeys(fn ($case) => [$case->value => $case->name])->all())
                ->required(),
            Forms\Components\Textarea::make('short_description'),
            Forms\Components\RichEditor::make('description'),
            Forms\Components\TextInput::make('target_amount')->numeric(),
            Forms\Components\TextInput::make('target_unit')->numeric(),
            Forms\Components\TextInput::make('unit_label'),
            Forms\Components\KeyValue::make('config'),
            Forms\Components\KeyValue::make('payment_config'),
            Forms\Components\KeyValue::make('seo_meta'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('status')->badge(),
            Tables\Columns\TextColumn::make('progress_type')->badge(),
            Tables\Columns\TextColumn::make('collected_amount')->numeric(),
            Tables\Columns\TextColumn::make('verified_donor_count')->numeric(),
        ])->actions([
            Tables\Actions\EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCampaigns::route('/'),
            'create' => Pages\CreateCampaign::route('/create'),
            'edit' => Pages\EditCampaign::route('/{record}/edit'),
        ];
    }
}
```

### `app/Filament/Resources/DonationResource.php`

Fungsi: verifikasi donasi oleh admin.

```php
<?php

namespace App\Filament\Resources;

use App\Domain\Donation\Enums\DonationStatusEnum;
use App\Domain\Donation\Models\Donation;
use App\Filament\Resources\DonationResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DonationResource extends Resource
{
    protected static ?string $model = Donation::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Lazismu';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('campaign_id')->relationship('campaign', 'title')->searchable()->preload()->required(),
            Forms\Components\TextInput::make('transaction_code')->required(),
            Forms\Components\TextInput::make('payer_name'),
            Forms\Components\TextInput::make('payer_email')->email(),
            Forms\Components\TextInput::make('payer_phone'),
            Forms\Components\Toggle::make('is_anonymous')->default(false),
            Forms\Components\TextInput::make('amount')->numeric()->required(),
            Forms\Components\TextInput::make('quantity')->numeric()->default(0),
            Forms\Components\TextInput::make('unit_label'),
            Forms\Components\TextInput::make('payment_method'),
            Forms\Components\TextInput::make('payment_channel'),
            Forms\Components\Select::make('status')
                ->options(collect(DonationStatusEnum::cases())->mapWithKeys(fn ($case) => [$case->value => $case->name])->all())
                ->required(),
            Forms\Components\Textarea::make('message'),
            Forms\Components\KeyValue::make('meta'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('transaction_code')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('campaign.title')->searchable(),
            Tables\Columns\TextColumn::make('payer_name'),
            Tables\Columns\TextColumn::make('amount')->numeric(),
            Tables\Columns\TextColumn::make('status')->badge(),
            Tables\Columns\TextColumn::make('verified_at')->dateTime(),
        ])->actions([
            Tables\Actions\EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDonations::route('/'),
            'create' => Pages\CreateDonation::route('/create'),
            'edit' => Pages\EditDonation::route('/{record}/edit'),
        ];
    }
}
```

### `app/Filament/Resources/DistributionResource.php`

Fungsi: transparansi penyaluran.

```php
<?php

namespace App\Filament\Resources;

use App\Domain\Report\Enums\DistributionRecipientTypeEnum;
use App\Domain\Report\Enums\DistributionStatusEnum;
use App\Domain\Report\Enums\DistributionTypeEnum;
use App\Domain\Report\Models\Distribution;
use App\Filament\Resources\DistributionResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DistributionResource extends Resource
{
    protected static ?string $model = Distribution::class;
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Lazismu';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('campaign_id')->relationship('campaign', 'title')->searchable()->preload(),
            Forms\Components\Select::make('institution_id')->relationship('institution', 'name')->searchable()->preload(),
            Forms\Components\TextInput::make('distribution_code')->required(),
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\Textarea::make('description'),
            Forms\Components\Select::make('recipient_type')
                ->options(collect(DistributionRecipientTypeEnum::cases())->mapWithKeys(fn ($case) => [$case->value => $case->name])->all())
                ->required(),
            Forms\Components\TextInput::make('recipient_name'),
            Forms\Components\TextInput::make('recipient_count')->numeric()->default(1),
            Forms\Components\TextInput::make('distributed_amount')->numeric()->default(0),
            Forms\Components\TextInput::make('distributed_unit')->numeric()->default(0),
            Forms\Components\TextInput::make('unit_label'),
            Forms\Components\Select::make('distribution_type')
                ->options(collect(DistributionTypeEnum::cases())->mapWithKeys(fn ($case) => [$case->value => $case->name])->all())
                ->required(),
            Forms\Components\Select::make('status')
                ->options(collect(DistributionStatusEnum::cases())->mapWithKeys(fn ($case) => [$case->value => $case->name])->all())
                ->required(),
            Forms\Components\DatePicker::make('distribution_date'),
            Forms\Components\TextInput::make('location'),
            Forms\Components\KeyValue::make('meta'),
            Forms\Components\Textarea::make('notes'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('distribution_code')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('title')->searchable(),
            Tables\Columns\TextColumn::make('recipient_type')->badge(),
            Tables\Columns\TextColumn::make('status')->badge(),
            Tables\Columns\TextColumn::make('distributed_amount')->numeric(),
        ])->actions([
            Tables\Actions\EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDistributions::route('/'),
            'create' => Pages\CreateDistribution::route('/create'),
            'edit' => Pages\EditDistribution::route('/{record}/edit'),
        ];
    }
}
```

## 8. Seeder Role dan Permission

### `database/seeders/RoleAndPermissionSeeder.php`

Fungsi: seed role dan permission dasar sesuai domain PCM dan Lazismu.

```php
<?php

namespace Database\Seeders;

use App\Domain\Access\Enums\PermissionEnum;
use App\Domain\Access\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = array_map(
            static fn (PermissionEnum $permission): string => $permission->value,
            PermissionEnum::cases(),
        );

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $superAdmin = Role::findOrCreate(RoleEnum::SuperAdmin->value, 'web');
        $adminPcm = Role::findOrCreate(RoleEnum::AdminPcm->value, 'web');
        $adminLazismu = Role::findOrCreate(RoleEnum::AdminLazismu->value, 'web');
        $contributor = Role::findOrCreate(RoleEnum::Contributor->value, 'web');

        $superAdmin->syncPermissions($permissions);

        $adminPcm->syncPermissions([
            PermissionEnum::ManageCategories->value,
            PermissionEnum::ManagePosts->value,
            PermissionEnum::PublishPosts->value,
            PermissionEnum::ManageAgendas->value,
            PermissionEnum::PublishAgendas->value,
            PermissionEnum::ManageInstitutions->value,
            PermissionEnum::ManageLeaders->value,
        ]);

        $adminLazismu->syncPermissions([
            PermissionEnum::ManageCategories->value,
            PermissionEnum::ManageCampaigns->value,
            PermissionEnum::PublishCampaigns->value,
            PermissionEnum::ManageDonations->value,
            PermissionEnum::VerifyDonations->value,
            PermissionEnum::ManageDistributions->value,
            PermissionEnum::ApproveDistributions->value,
        ]);

        $contributor->syncPermissions([
            PermissionEnum::ManagePosts->value,
            PermissionEnum::ManageAgendas->value,
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
```

## 9. Catatan Implementasi

- `PostService` wajib dipanggil dari controller, action, atau Filament page hook. Jangan pakai `$record->update()` langsung untuk workflow.
- `CampaignService` menormalkan `config` JSON agar schema campaign tetap konsisten.
- `DonationService` adalah pusat workflow verifikasi. Semua perubahan status donasi dan sinkronisasi progress campaign dilakukan di sini.
- `DistributionService` menjaga approval dan reporting flow terpisah dari resource.
- `seo_meta` sengaja dibuat JSON agar fleksibel untuk `title`, `description`, `keywords`, `canonical_url`, dan `robots`.
- Tidak ada field gamification donor seperti badge, leaderboard, streak, poin, atau rank.

## 10. Langkah Lanjut Paling Aman

1. Gunakan baseline repo aktif: Laravel 12 dan Filament 5.
2. Implementasikan domain models dan service.
3. Tambahkan policy per resource.
4. Refactor Filament page hooks agar memakai service layer.
5. Tambahkan test minimal untuk:
   - create post
   - publish post
   - create campaign
   - verify donation
   - mark distribution reported
```
