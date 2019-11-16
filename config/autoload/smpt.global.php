<?php declare(strict_types=1);

return [
    'smtp' => [
        'host' => getenv('SMTP_HOST'),
        'port' => getenv('SMTP_PORT'),
        'user' => getenv('SMTP_USER'),
        'password' => getenv('SMTP_PASSWORD'),
        'from' => 'test@example.com',
        'base_url' => 'http://localhost'
    ],
];