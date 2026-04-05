<?php

declare(strict_types=1);

namespace App\Support;

final class PublicNavigation
{
    /**
     * @return array<int, array{label: string, route: string, icon: string}>
     */
    public function primaryItems(): array
    {
        return [
            ['label' => 'Beranda', 'route' => 'home', 'icon' => 'house'],
            ['label' => 'Berita', 'route' => 'posts.index', 'icon' => 'newspaper'],
            ['label' => 'Agenda', 'route' => 'agendas.index', 'icon' => 'calendar-range'],
            ['label' => 'Amal Usaha', 'route' => 'institutions.index', 'icon' => 'building-2'],
            ['label' => 'Program', 'route' => 'campaigns.index', 'icon' => 'heart-handshake'],
            ['label' => 'Tentang', 'route' => 'pages.about', 'icon' => 'info'],
        ];
    }

    /**
     * @return array<int, array{label: string, route: string, icon: string, featured?: bool, query?: array<string, string>}>
     */
    public function mobileBottomItems(): array
    {
        return [
            ['label' => 'Beranda', 'route' => 'home', 'icon' => 'house'],
            ['label' => 'Berita', 'route' => 'posts.index', 'icon' => 'newspaper'],
            ['label' => 'Donasi', 'route' => 'campaigns.index', 'icon' => 'heart-handshake', 'featured' => true, 'query' => ['jenis' => 'donation']],
            ['label' => 'Agenda', 'route' => 'agendas.index', 'icon' => 'calendar-range'],
            ['label' => 'Cari', 'route' => 'search.index', 'icon' => 'search'],
        ];
    }
}
