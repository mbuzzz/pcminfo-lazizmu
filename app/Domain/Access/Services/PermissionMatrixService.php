<?php

declare(strict_types=1);

namespace App\Domain\Access\Services;

use App\Models\Permission;
use Illuminate\Support\Str;

final class PermissionMatrixService
{
    /**
     * @return array<string, string>
     */
    public function options(): array
    {
        return Permission::query()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (Permission $permission): array => [
                $permission->name => $this->label($permission->name),
            ])
            ->all();
    }

    public function label(string $permission): string
    {
        return $this->group($permission) . ' · ' . Str::headline(str_replace('_', ' ', $permission));
    }

    private function group(string $permission): string
    {
        return match (true) {
            str_contains($permission, 'users') => 'Pengguna',
            str_contains($permission, 'roles') => 'Peran',
            str_contains($permission, 'settings') => 'Pengaturan',
            str_contains($permission, 'articles'), str_contains($permission, 'posts') => 'Artikel',
            str_contains($permission, 'categories') => 'Kategori',
            str_contains($permission, 'agendas') => 'Agenda',
            str_contains($permission, 'business_units'), str_contains($permission, 'institutions') => 'Amal Usaha',
            str_contains($permission, 'leadership'), str_contains($permission, 'leaders') => 'Struktur Pimpinan',
            str_contains($permission, 'autonomous_organizations') => 'Organisasi Otonom',
            str_contains($permission, 'councils') => 'Majelis',
            str_contains($permission, 'agencies') => 'Lembaga',
            str_contains($permission, 'campaigns') => 'Program Donasi',
            str_contains($permission, 'donations') => 'Donasi',
            str_contains($permission, 'distributions') => 'Penyaluran',
            default => 'Lainnya',
        };
    }
}
