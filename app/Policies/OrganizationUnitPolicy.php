<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\OrganizationUnitType;
use App\Models\OrganizationUnit;
use App\Models\User;

class OrganizationUnitPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage_autonomous_organizations')
            || $user->can('manage_councils')
            || $user->can('manage_agencies');
    }

    public function view(User $user, OrganizationUnit $organizationUnit): bool
    {
        return $user->can($this->permissionForType($organizationUnit->type));
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, OrganizationUnit $organizationUnit): bool
    {
        return $user->can($this->permissionForType($organizationUnit->type));
    }

    public function delete(User $user, OrganizationUnit $organizationUnit): bool
    {
        return $user->can($this->permissionForType($organizationUnit->type));
    }

    public function deleteAny(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function restore(User $user, OrganizationUnit $organizationUnit): bool
    {
        return $user->can($this->permissionForType($organizationUnit->type));
    }

    public function restoreAny(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function forceDelete(User $user, OrganizationUnit $organizationUnit): bool
    {
        return $user->can($this->permissionForType($organizationUnit->type));
    }

    public function forceDeleteAny(User $user): bool
    {
        return $this->viewAny($user);
    }

    private function permissionForType(OrganizationUnitType|string $type): string
    {
        $value = $type instanceof OrganizationUnitType ? $type->value : $type;

        return match ($value) {
            OrganizationUnitType::AutonomousOrganization->value => 'manage_autonomous_organizations',
            OrganizationUnitType::Council->value => 'manage_councils',
            OrganizationUnitType::Agency->value => 'manage_agencies',
            default => 'manage_autonomous_organizations',
        };
    }
}
