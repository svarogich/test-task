<?php

declare(strict_types=1);

namespace App;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.zendframework.com/zend-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates' => $this->getTemplates(),
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies(): array
    {
        return [
            'invokables' => [
            ],
            'factories' => [
                Handler\HomePageHandler::class => Handler\HomePageHandlerFactory::class,
                Handler\AuthenticationHandler::class => Handler\AuthenticationHandlerFactory::class,

                Models\UserRepository::class => Models\UserRepositoryFactory::class,
                Models\RegisterApplicationRepository::class => Models\RegisterApplicationRepositoryFactory::class,

                Helpers\Authentication::class => Helpers\AuthenticationFactory::class,

                Services\UserRegistration::class => Services\UserRegistrationFactory::class,
                Services\RssReader::class => Services\RssReaderFactory::class,
            ],
        ];
    }

    /**
     * Returns the templates configuration
     */
    public function getTemplates(): array
    {
        return [
            'paths' => [
                'app' => [__DIR__ . '/Templates/app'],
                'error' => [__DIR__ . '/Templates/error'],
                'layout' => [__DIR__ . '/Templates/layout'],
            ],
        ];
    }
}
