<?php

declare(strict_types=1);

namespace Tests\Feature\Activity;

use App\Domain\Access\Services\RoleService;
use App\Domain\Access\Services\UserService;
use App\Domain\Organization\Services\OrganizationUnitService;
use App\Enums\OrganizationUnitType;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class ActivityLogIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_perubahan_user_tercatat_di_activity_log(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $actor = User::factory()->create();
        $actor->assignRole('super_admin');
        $this->actingAs($actor);

        $user = app(UserService::class)->create([
            'name' => 'Admin PCM',
            'email' => 'admin.pcm@example.test',
            'password' => 'rahasia123',
            'roles' => [
                Role::query()->where('name', 'admin_pcm')->value('id'),
            ],
        ], $actor);

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'akses',
            'subject_type' => User::class,
            'subject_id' => $user->getKey(),
            'event' => 'created',
        ]);
    }

    public function test_role_dan_unit_organisasi_tercatat_di_activity_log(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $role = app(RoleService::class)->create([
            'name' => 'editor_wilayah',
            'permissions' => ['manage_articles'],
        ]);

        $unit = app(OrganizationUnitService::class)->create([
            'name' => 'Pemuda Muhammadiyah',
            'slug' => 'pemuda-muhammadiyah',
        ], OrganizationUnitType::AutonomousOrganization);

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'akses',
            'subject_type' => Role::class,
            'subject_id' => $role->getKey(),
            'event' => 'created',
        ]);

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'organisasi',
            'subject_type' => \App\Models\OrganizationUnit::class,
            'subject_id' => $unit->getKey(),
            'event' => 'created',
        ]);

        $this->assertGreaterThanOrEqual(2, Activity::query()->count());
    }
}
