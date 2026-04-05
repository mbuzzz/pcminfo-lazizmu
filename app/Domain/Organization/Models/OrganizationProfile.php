<?php

declare(strict_types=1);

namespace App\Domain\Organization\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationProfile extends Model
{
    protected $table = 'organization_profiles';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }
}
