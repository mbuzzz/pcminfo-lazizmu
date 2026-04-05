<?php

declare(strict_types=1);

namespace App\Support\Media;

final class MediaPath
{
    public static function postFeatured(): string
    {
        return (string) config('media.directories.post_featured');
    }

    public static function postContent(): string
    {
        return (string) config('media.directories.post_content');
    }

    public static function institutionLogo(): string
    {
        return (string) config('media.directories.institution_logo');
    }

    public static function institutionCover(): string
    {
        return (string) config('media.directories.institution_cover');
    }

    public static function leaderPhoto(): string
    {
        return (string) config('media.directories.leader_photo');
    }

    public static function organizationUnitLogo(): string
    {
        return (string) config('media.directories.organization_unit_logo');
    }

    public static function userAvatar(): string
    {
        return (string) config('media.directories.user_avatar');
    }

    public static function siteLogo(): string
    {
        return (string) config('media.directories.site_logo');
    }

    public static function siteFavicon(): string
    {
        return (string) config('media.directories.site_favicon');
    }

    public static function donationQris(): string
    {
        return (string) config('media.directories.donation_qris');
    }

    public static function seoDefaultOg(): string
    {
        return (string) config('media.directories.seo_default_og');
    }
}
