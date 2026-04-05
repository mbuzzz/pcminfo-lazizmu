<?php

declare(strict_types=1);

namespace App\Domain\Access\Services;

use App\Models\Role;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class RoleService
{
    public function create(array $data): Role
    {
        return DB::transaction(function () use ($data): Role {
            $payload = $this->normalize($data);
            $this->ensureUniqueName($payload['name']);

            $role = Role::query()->create([
                'name' => $payload['name'],
                'guard_name' => 'web',
            ]);

            $role->syncPermissions($payload['permissions']);

            return $role->refresh();
        });
    }

    public function update(Role $role, array $data): Role
    {
        return DB::transaction(function () use ($role, $data): Role {
            $payload = $this->normalize($data);

            if ($role->isCoreRole() && $role->name !== $payload['name']) {
                throw ValidationException::withMessages([
                    'name' => 'Role inti tidak boleh diganti namanya.',
                ]);
            }

            $this->ensureUniqueName($payload['name'], $role);

            $role->update([
                'name' => $payload['name'],
            ]);

            $role->syncPermissions($payload['permissions']);

            return $role->refresh();
        });
    }

    public function delete(Role $role): void
    {
        if ($role->isCoreRole()) {
            throw ValidationException::withMessages([
                'role' => 'Role inti tidak dapat dihapus.',
            ]);
        }

        if ($role->users()->exists()) {
            throw ValidationException::withMessages([
                'role' => 'Role yang masih dipakai pengguna tidak dapat dihapus.',
            ]);
        }

        $role->delete();
    }

    /**
     * @param array<string, mixed> $data
     * @return array{name: string, permissions: array<int, string>}
     */
    private function normalize(array $data): array
    {
        return [
            'name' => strtolower(trim((string) Arr::get($data, 'name'))),
            'permissions' => array_values(array_filter(Arr::wrap($data['permissions'] ?? []))),
        ];
    }

    private function ensureUniqueName(string $name, ?Role $ignore = null): void
    {
        $query = Role::query()->where('name', $name);

        if ($ignore !== null) {
            $query->whereKeyNot($ignore->getKey());
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'name' => 'Nama role sudah digunakan.',
            ]);
        }
    }
}
