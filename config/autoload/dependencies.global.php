<?php

declare(strict_types=1);

use App\Helpers\Authentication;
use App\Models\UserRepository;
use Zend\Expressive\Authentication\AuthenticationInterface;
use Zend\Expressive\Authentication\UserRepositoryInterface;

return [
    'dependencies' => [
        'aliases' => [
            AuthenticationInterface::class => Authentication::class,
            UserRepositoryInterface::class => UserRepository::class,
        ],
        'invokables' => [
        ],
        'factories'  => [
        ],
    ],
];
