<?php

declare(strict_types=1);

namespace App\Domain\Setting\View\Composers;

use App\Domain\Setting\Support\PublicSiteSettings;
use Illuminate\View\View;

final class PublicSiteSettingsComposer
{
    public function __construct(
        private readonly PublicSiteSettings $siteSettings,
    ) {
    }

    public function compose(View $view): void
    {
        $view->with('siteSettings', $this->siteSettings);
    }
}
