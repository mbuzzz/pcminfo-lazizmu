<?php

declare(strict_types=1);

namespace Tests\Feature\Organization;

use App\Domain\Organization\Services\OrganizationUnitService;
use App\Enums\OrganizationUnitType;
use App\Models\OrganizationUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationUnitManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_dapat_membuat_unit_organisasi_dengan_slug_unik_per_jenis(): void
    {
        $service = app(OrganizationUnitService::class);

        $otonom = $service->create([
            'name' => 'Pemuda Muhammadiyah',
            'slug' => 'pemuda-muhammadiyah',
        ], OrganizationUnitType::AutonomousOrganization);

        $majelis = $service->create([
            'name' => 'Majelis Pendidikan',
            'slug' => 'pemuda-muhammadiyah',
        ], OrganizationUnitType::Council);

        $this->assertSame('autonomous_organization', $otonom->type->value);
        $this->assertSame('council', $majelis->type->value);
        $this->assertDatabaseCount('organization_units', 2);
    }

    public function test_resource_query_hanya_mengambil_data_sesuai_jenis(): void
    {
        OrganizationUnit::factory()->create([
            'type' => OrganizationUnitType::AutonomousOrganization,
            'name' => 'NA Cabang Genteng',
        ]);

        OrganizationUnit::factory()->create([
            'type' => OrganizationUnitType::Agency,
            'name' => 'Lazismu Cabang Genteng',
        ]);

        $this->assertSame(1, \App\Filament\Resources\AutonomousOrganizationResource::getEloquentQuery()->count());
        $this->assertSame(1, \App\Filament\Resources\AgencyResource::getEloquentQuery()->count());
    }
}
