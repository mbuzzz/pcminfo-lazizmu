<?php

declare(strict_types=1);

namespace App\Domain\Access\Services;

use App\Domain\Access\Support\CoreRole;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class UserService
{
    public function __construct(
        private readonly AvatarService $avatarService,
    ) {
    }

    public function create(array $data, ?User $actor = null): User
    {
        return DB::transaction(function () use ($data, $actor): User {
            $payload = $this->normalize($data);
            $roleNames = $this->resolveRoleNames($payload['role_ids']);

            $this->guardSuperAdminAssignment($roleNames, $actor);

            $user = User::query()->create([
                'name' => $payload['name'],
                'email' => $payload['email'],
                'avatar' => $payload['avatar'],
                'password' => $payload['password'],
                'is_active' => $payload['is_active'],
                'email_verified_at' => $payload['email_verified_at'],
            ]);

            $user->syncRoles($roleNames);

            return $user->refresh();
        });
    }

    public function update(User $user, array $data, ?User $actor = null): User
    {
        return DB::transaction(function () use ($user, $data, $actor): User {
            $avatarFieldWasSubmitted = array_key_exists('avatar', $data);
            $payload = $this->normalize($data, $user);
            $roleNames = $this->resolveRoleNames($payload['role_ids']);

            $this->guardSuperAdminAssignment($roleNames, $actor);

            if (! $payload['is_active'] && $actor?->is($user)) {
                throw ValidationException::withMessages([
                    'is_active' => 'Anda tidak dapat menonaktifkan akun sendiri.',
                ]);
            }

            $this->avatarService->sync($user->avatar, $payload['avatar'], $avatarFieldWasSubmitted);

            $attributes = [
                'name' => $payload['name'],
                'email' => $payload['email'],
                'avatar' => $payload['avatar'],
                'is_active' => $payload['is_active'],
                'email_verified_at' => $payload['email_verified_at'],
            ];

            if ($payload['password'] !== null) {
                $attributes['password'] = $payload['password'];
            }

            $user->update($attributes);
            $user->syncRoles($roleNames);

            return $user->refresh();
        });
    }

    public function delete(User $user, User $actor): void
    {
        if ($actor->is($user)) {
            throw ValidationException::withMessages([
                'user' => 'Anda tidak dapat menghapus akun sendiri.',
            ]);
        }

        if ($user->isCoreAdministrator()) {
            throw ValidationException::withMessages([
                'user' => 'Akun Super Admin tidak dapat dihapus.',
            ]);
        }

        $user->delete();
    }

    /**
     * @param array<string, mixed> $data
     * @return array{name: string, email: string, avatar: ?string, password: ?string, is_active: bool, email_verified_at: mixed, role_ids: array<int, int>}
     */
    private function normalize(array $data, ?User $user = null): array
    {
        return [
            'name' => trim((string) Arr::get($data, 'name', $user?->name)),
            'email' => strtolower(trim((string) Arr::get($data, 'email', $user?->email))),
            'avatar' => Arr::get($data, 'avatar', $user?->avatar),
            'password' => filled(Arr::get($data, 'password')) ? (string) $data['password'] : null,
            'is_active' => (bool) Arr::get($data, 'is_active', $user?->is_active ?? true),
            'email_verified_at' => Arr::get($data, 'email_verified_at', $user?->email_verified_at),
            'role_ids' => array_map('intval', Arr::wrap($data['roles'] ?? $data['role_ids'] ?? $user?->roles->pluck('id')->all() ?? [])),
        ];
    }

    /**
     * @param array<int, int> $roleIds
     * @return array<int, string>
     */
    private function resolveRoleNames(array $roleIds): array
    {
        if ($roleIds === []) {
            return [];
        }

        return Role::query()
            ->whereKey($roleIds)
            ->pluck('name')
            ->all();
    }

    /**
     * @param array<int, string> $roleNames
     */
    private function guardSuperAdminAssignment(array $roleNames, ?User $actor): void
    {
        if (! in_array(CoreRole::SuperAdmin, $roleNames, true)) {
            return;
        }

        if ($actor?->isCoreAdministrator()) {
            return;
        }

        throw ValidationException::withMessages([
            'roles' => 'Role Super Admin hanya dapat diberikan oleh Super Admin.',
        ]);
    }
}
