<?php

declare(strict_types=1);

namespace Tests\Feature\Leader;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class LeaderIndexPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_admin_leader_dapat_diakses_tanpa_error(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin_pcm');

        $response = $this->actingAs($user)->get('/admin/leaders');

        $response->assertOk();
        $response->assertSee('Struktur Pimpinan');
    }
}
