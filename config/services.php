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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    "personal_access_client_id" => env('PASSPORT_PERSONAL_ACCESS_CLIENT_ID', 'abc'),
    "personal_access_client_secret" => env('PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET', 'abc'),

    //
    'image_tmp' => "public/tmp", // url para guardar imagenes temporales en storage

    'apisite' => [
        'base_uri' => env('API_PER_SITE_URL'),
        'secret' => env('API_PER_SITE_SECRET')
    ],

    // tipo de imagen, path y tamaños
    'image' => [
        'tmp' => [
            // 'url' => 'public/file/logo', // path , esto está linkiado en public desde la carpeta storage
            // 'size' => ['80', '160', '300', '450'], // size, Example, In the end the image will be located in: file/logo/80/miimagen.jpg
            // 'oritation' => 'h', // mantener proporcion h => horizontal, v => vertical, o => ambos (cuadratica)
            'url' => env('URL_IMAGE_TEMP') // ubicación de la imagen para renderizar.
        ]
    ]

];
