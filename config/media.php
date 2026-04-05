<?php

return [
    'disk' => env('MEDIA_DISK', 'public'),

    'visibility' => env('MEDIA_VISIBILITY', 'public'),

    'accepted_image_types' => [
        'image/jpeg',
        'image/png',
        'image/webp',
    ],

    'max_sizes_kb' => [
        'image' => 2048,
    ],

    'directories' => [
        'post_featured' => 'posts/featured',
        'post_content' => 'posts/content',
        'institution_logo' => 'institutions/logo',
        'institution_cover' => 'institutions/cover',
        'leader_photo' => 'leaders/photo',
        'organization_unit_logo' => 'organization-units/logo',
        'user_avatar' => 'users/avatar',
        'site_logo' => 'settings/site',
        'site_favicon' => 'settings/favicon',
        'donation_qris' => 'settings/donation',
        'seo_default_og' => 'settings/seo',
    ],
];
