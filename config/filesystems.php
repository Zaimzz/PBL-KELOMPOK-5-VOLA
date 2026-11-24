<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => (env('SUPABASE_STORAGE_BUCKET') && env('SUPABASE_STORAGE_KEY')) ? [
            'driver' => 's3',
            'key' => env('SUPABASE_STORAGE_KEY'),
            'secret' => env('SUPABASE_STORAGE_SECRET'),
            'region' => env('SUPABASE_STORAGE_REGION', 'ap-southeast-1'),
            'bucket' => env('SUPABASE_STORAGE_BUCKET'),
            'url' => rtrim((string) env('SUPABASE_STORAGE_PUBLIC_URL', 'https://wumumuzcpelkqazhdkcx.supabase.co/storage/v1/object/public/posters'), '/'),
            'endpoint' => env('SUPABASE_STORAGE_ENDPOINT', 'https://wumumuzcpelkqazhdkcx.supabase.co/storage/v1/s3'),
            'use_path_style_endpoint' => true,
            'throw' => false,
            'report' => false,
            // Nonaktifkan SSL verify di local dev (Windows cURL tidak punya CA bundle lengkap).
            // Di production (APP_ENV=production) SSL tetap diverifikasi.
            'http' => [
                'verify' => env('APP_ENV') !== 'local',
            ],
        ] : [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('SUPABASE_STORAGE_PUBLIC_URL')
                ? rtrim((string) env('SUPABASE_STORAGE_PUBLIC_URL'), '/')
                : rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

        'supabase' => [
            'driver' => 's3',
            'key' => env('SUPABASE_STORAGE_KEY'),
            'secret' => env('SUPABASE_STORAGE_SECRET'),
            'region' => env('SUPABASE_STORAGE_REGION', 'ap-southeast-1'),
            'bucket' => env('SUPABASE_STORAGE_BUCKET', 'posters'),
            'url' => rtrim((string) env('SUPABASE_STORAGE_PUBLIC_URL', 'https://wumumuzcpelkqazhdkcx.supabase.co/storage/v1/object/public/posters'), '/'),
            'endpoint' => env('SUPABASE_STORAGE_ENDPOINT', 'https://wumumuzcpelkqazhdkcx.storage.supabase.co/storage/v1/s3'),
            'use_path_style_endpoint' => true,
            'throw' => false,
            'report' => false,
            'http' => [
                'verify' => env('APP_ENV') !== 'local',
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
