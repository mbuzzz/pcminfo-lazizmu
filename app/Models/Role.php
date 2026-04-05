<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Access\Support\CoreRole;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use LogsActivity;

    public function isCoreRole(): bool
    {
        return in_array($this->name, CoreRole::values(), true);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('akses')
            ->logOnly(['name'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
