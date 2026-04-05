<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'manage_users',
            'manage_roles',
            'view_any_users',
            'create_users',
            'update_users',
            'delete_users',
            'restore_users',
            'view_any_roles',
            'create_roles',
            'update_roles',
            'delete_roles',
            'assign_role_permissions',
            'manage_settings',
            'view_settings',
            'update_settings',
            'manage_articles',
            'manage_categories',
            'manage_agendas',
            'manage_business_units',
            'manage_leadership_structure',
            'manage_autonomous_organizations',
            'manage_councils',
            'manage_agencies',
            'manage_campaigns',
            'manage_donations',
            'verify_donations',
            'manage_distribution_reports',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::query()->firstOrCreate(['name' => 'super_admin']);
        $adminPcm = Role::query()->firstOrCreate(['name' => 'admin_pcm']);
        $adminLazismu = Role::query()->firstOrCreate(['name' => 'admin_lazismu']);
        $contributor = Role::query()->firstOrCreate(['name' => 'kontributor']);

        $superAdmin->syncPermissions($permissions);

        $adminPcm->syncPermissions([
            'view_settings',
            'view_any_users',
            'manage_articles',
            'manage_categories',
            'manage_agendas',
            'manage_business_units',
            'manage_leadership_structure',
            'manage_autonomous_organizations',
            'manage_councils',
            'manage_agencies',
        ]);

        $adminLazismu->syncPermissions([
            'view_settings',
            'manage_categories',
            'manage_campaigns',
            'manage_donations',
            'verify_donations',
            'manage_distribution_reports',
        ]);

        $contributor->syncPermissions([
            'manage_articles',
            'manage_agendas',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
