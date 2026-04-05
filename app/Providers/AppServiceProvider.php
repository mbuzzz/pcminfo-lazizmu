<?php

namespace App\Providers;

use App\Application\Contracts\PaymentGateway;
use App\Application\Contracts\StorageUrlGenerator;
use App\Application\Contracts\WhatsAppGateway;
use App\Domain\Campaign\Models\Campaign;
use App\Domain\Campaign\Policies\CampaignPolicy;
use App\Domain\Donation\Models\Donation;
use App\Domain\Donation\Policies\DonationPolicy;
use App\Domain\Setting\View\Composers\PublicSiteSettingsComposer;
use App\Infrastructure\Payments\NullPaymentGateway;
use App\Infrastructure\Storage\DefaultStorageUrlGenerator;
use App\Infrastructure\WhatsApp\LogWhatsAppGateway;
use App\Models\Institution;
use App\Models\Leader;
use App\Models\OrganizationUnit;
use App\Models\Role;
use App\Models\User;
use App\Policies\InstitutionPolicy;
use App\Policies\LeaderPolicy;
use App\Policies\OrganizationUnitPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PaymentGateway::class, NullPaymentGateway::class);
        $this->app->singleton(StorageUrlGenerator::class, DefaultStorageUrlGenerator::class);
        $this->app->singleton(WhatsAppGateway::class, LogWhatsAppGateway::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Campaign::class, CampaignPolicy::class);
        Gate::policy(Donation::class, DonationPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(OrganizationUnit::class, OrganizationUnitPolicy::class);
        Gate::policy(Institution::class, InstitutionPolicy::class);
        Gate::policy(Leader::class, LeaderPolicy::class);

        View::composer('*', PublicSiteSettingsComposer::class);
    }
}
