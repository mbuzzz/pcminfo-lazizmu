<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'whatsapp' => [
        'admin_phone' => env('WHATSAPP_ADMIN_PHONE', ''),
        'admin_phone_by_campaign_type' => [],
        'default_template' => env('WHATSAPP_DONATION_TEMPLATE', ''),
        'templates' => [
            'donation' => "Assalamu’alaikum.\nSaya {payer_name} konfirmasi donasi untuk \"{campaign_title}\".\nKode: {transaction_code}\nNominal: {amount}\nMohon diverifikasi. Jazakumullah khair.",
            'zakat' => "Assalamu’alaikum.\nSaya {payer_name} konfirmasi pembayaran zakat untuk \"{campaign_title}\".\nKode: {transaction_code}\nNominal: {amount}\nMohon diverifikasi. Jazakumullah khair.",
            'wakaf' => "Assalamu’alaikum.\nSaya {payer_name} konfirmasi wakaf untuk \"{campaign_title}\".\nKode: {transaction_code}\nNominal: {amount}\nMohon diverifikasi. Jazakumullah khair.",
            'qurban' => "Assalamu’alaikum.\nSaya {payer_name} konfirmasi qurban untuk \"{campaign_title}\".\nKode: {transaction_code}\nNominal: {amount}\nMohon diverifikasi. Jazakumullah khair.",
            'program' => "Assalamu’alaikum.\nSaya {payer_name} konfirmasi donasi program \"{campaign_title}\".\nKode: {transaction_code}\nNominal: {amount}\nMohon diverifikasi. Jazakumullah khair.",
        ],
    ],

    'qris' => [
        'static_image_url' => env('QRIS_STATIC_IMAGE_URL', ''),
    ],
];
