<?php

declare(strict_types=1);

namespace App\Domain\Content\Services;

use App\Models\Category;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final class CategoryService
{
    public function create(array $data): Category
    {
        return Category::query()->create($this->normalize($data));
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($this->normalize($data, $category));

        return $category->refresh();
    }

    private function normalize(array $data, ?Category $category = null): array
    {
        $data['slug'] = filled(Arr::get($data, 'slug'))
            ? Str::slug((string) $data['slug'])
            : Str::slug((string) Arr::get($data, 'name', $category?->name));

        $data['order'] = (int) Arr::get($data, 'order', $category?->order ?? 0);
        $data['is_active'] = (bool) Arr::get($data, 'is_active', $category?->is_active ?? true);

        return $data;
    }
}
