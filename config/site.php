<?php

/*
 * Public business details shown in the website header, footer and contact page.
 * SAMPLE VALUES - replace them with your real details (or set them in .env).
 */
return [
    'tagline' => env('SITE_TAGLINE', 'Trusted home services, booked in minutes'),
    'phone' => env('SITE_PHONE', '+91 81000 11661'),
    'whatsapp' => env('SITE_WHATSAPP', '918100011661'), // digits only, used for wa.me links
    'email' => env('SITE_EMAIL', 'support@example.com'),
    'address' => env('SITE_ADDRESS', 'Kolkata, West Bengal, India'),
    'hours' => env('SITE_HOURS', 'Mon – Sun, 8 AM – 9 PM'),

    'cities' => ['Kolkata', 'Mumbai', 'Delhi NCR', 'Bangalore', 'Hyderabad', 'Chennai', 'Pune'],

    'socials' => [
        'facebook' => env('SITE_FACEBOOK', '#'),
        'instagram' => env('SITE_INSTAGRAM', '#'),
        'youtube' => env('SITE_YOUTUBE', '#'),
    ],
];
