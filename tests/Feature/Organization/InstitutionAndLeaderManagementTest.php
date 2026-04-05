<?php

declare(strict_types=1);

namespace Tests\Feature\Organization;

use App\Domain\Organization\Services\InstitutionService;
use App\Domain\Organization\Services\LeaderService;
use App\Models\Institution;
use App\Models\Leader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class InstitutionAndLeaderManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_institution_service_menolak_slug_duplikat(): void
    {
        Institution::query()->create([
            'name' => 'SMP Muhammadiyah 1',
            'slug' => 'smp-muhammadiyah-1',
            'type' => 'school',
            'status' => 'active',
        ]);

        $this->expectException(ValidationException::class);

        app(InstitutionService::class)->create([
            'name' => 'SMP Muhammadiyah 1 Baru',
            'slug' => 'smp-muhammadiyah-1',
            'type' => 'school',
            'status' => 'active',
        ]);
    }

    public function test_leader_service_menyimpan_urutan_sebagai_integer(): void
    {
        $leader = app(LeaderService::class)->create([
            'name' => 'Ahmad',
            'position' => 'Ketua',
            'organization' => 'pcm',
            'position_level' => 'leadership',
            'period' => '2022-2027',
            'status' => 'active',
            'order' => '5',
        ]);

        $this->assertSame(5, $leader->order);

        $leader = app(LeaderService::class)->update($leader, [
            'order' => '8',
        ]);

        $this->assertSame(8, $leader->order);
    }
}
