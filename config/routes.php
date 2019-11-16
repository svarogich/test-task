<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Zend\Expressive\Application;
use Zend\Expressive\MiddlewareFactory;

return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container) : void {
    $app->get('/login', App\Handler\AuthenticationHandler::class, 'get.login');
    $app->post('/login', App\Handler\AuthenticationHandler::class, 'post.login');

    $app->get('/registration', App\Handler\AuthenticationHandler::class, 'get.registration');
    $app->post('/registration', App\Handler\AuthenticationHandler::class, 'post.registration');

    $app->get('/pre-registration', App\Handler\AuthenticationHandler::class, 'get.pre-registration');
    $app->post('/pre-registration', App\Handler\AuthenticationHandler::class, 'post.pre-registration');

    $app->post('/check-email', App\Handler\AuthenticationHandler::class, 'post.check-email');

    $app->get('/', App\Handler\HomePageHandler::class, 'get.home');
};