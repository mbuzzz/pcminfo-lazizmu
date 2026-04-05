<?php

declare(strict_types=1);

namespace Tests\Feature\Access;

use App\Domain\Access\Services\RoleService;
use App\Models\Role;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

final class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_dapat_membuat_role_dinamis_dengan_permission(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $role = app(RoleService::class)->create([
            'name' => 'editor_khusus',
            'permissions' => ['manage_articles', 'manage_categories'],
        ]);

        $this->assertSame('editor_khusus', $role->name);
        $this->assertTrue($role->hasPermissionTo('manage_articles'));
        $this->assertTrue($role->hasPermissionTo('manage_categories'));
    }

    public function test_service_menolak_hapus_role_inti(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $this->expectException(ValidationException::class);

        app(RoleService::class)->delete(Role::query()->where('name', 'super_admin')->firstOrFail());
    }
}
