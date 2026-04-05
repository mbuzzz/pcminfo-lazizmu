<?php

declare(strict_types=1);

namespace Tests\Feature\Access;

use App\Domain\Access\Services\UserService;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

final class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_dapat_membuat_user_dan_assign_role(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $actor = User::factory()->create();
        $actor->assignRole('super_admin');

        $user = app(UserService::class)->create([
            'name' => 'Admin PCM',
            'email' => 'admin.pcm@example.test',
            'password' => 'rahasia123',
            'is_active' => true,
            'roles' => [
                Role::query()->where('name', 'admin_pcm')->value('id'),
            ],
        ], $actor);

        $this->assertSame('Admin PCM', $user->name);
        $this->assertTrue($user->hasRole('admin_pcm'));
        $this->assertTrue($user->is_active);
        $this->assertTrue(password_verify('rahasia123', $user->password));
    }

    public function test_service_menolak_hapus_akun_sendiri(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $actor = User::factory()->create();
        $actor->assignRole('super_admin');

        $this->expectException(ValidationException::class);

        app(UserService::class)->delete($actor, $actor);
    }
}
