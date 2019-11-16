<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Application;
use Zend\Expressive\Authentication\AuthenticationInterface;
use Zend\Expressive\Authentication\Exception\InvalidConfigException;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Handler\NotFoundHandler;
use Zend\Expressive\Helper\ServerUrlMiddleware;
use Zend\Expressive\Helper\UrlHelperMiddleware;
use Zend\Expressive\MiddlewareFactory;
use Zend\Expressive\Router\Middleware\DispatchMiddleware;
use Zend\Expressive\Router\Middleware\ImplicitHeadMiddleware;
use Zend\Expressive\Router\Middleware\ImplicitOptionsMiddleware;
use Zend\Expressive\Router\Middleware\MethodNotAllowedMiddleware;
use Zend\Expressive\Router\Middleware\RouteMiddleware;
use Zend\Expressive\Router\RouteResult;
use Zend\Expressive\Session\SessionMiddleware;
use Zend\Stratigility\Middleware\ErrorHandler;

/**
 * Setup middleware pipeline:
 */
return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    $app->pipe(ErrorHandler::class);
    $app->pipe(ServerUrlMiddleware::class);
    $app->pipe(SessionMiddleware::class);
    $app->pipe(RouteMiddleware::class);
    $app->pipe($factory->callable(
    // $container is present within the callback, and refers to the DI container.
        function ($request, $handler) use ($container) {
            /** @var ServerRequestInterface $request */
            /** @var RequestHandlerInterface $handler */
            /** @var RouteResult $routeResult */
            $routeResult = $request->getAttribute(RouteResult::class);
            $config = $container->get('config')['authentication'] ?? [];
            if (!isset($config['non_authenticated_routes'])) {
                throw new InvalidConfigException(
                    'The non authenticated routes configuration is missing'
                );
            }

            // Ignore authentication for all routes from non_authenticated_routes config
            if (
                $routeResult instanceof RouteResult &&
                in_array($routeResult->getMatchedRoute()->getName(), $config['non_authenticated_routes'], true)
            ) {
                $auth = $container->get(AuthenticationInterface::class);
                $user = $auth->authenticate($request);
                if (null !== $user) {
                    // user is authenticated, but try to use AuthenticationHandler
                    return $handler->handle($request->withAttribute(UserInterface::class, $user));
                }
                return $handler->handle($request);
            }

            // All other requests require the authentication middleware
            $authenticationMiddleware = $container->get(
                Zend\Expressive\Authentication\AuthenticationMiddleware::class
            );
            return $authenticationMiddleware->process($request, $handler);
        }
    ));

    $app->pipe(ImplicitHeadMiddleware::class);
    $app->pipe(ImplicitOptionsMiddleware::class);
    $app->pipe(MethodNotAllowedMiddleware::class);
    $app->pipe(UrlHelperMiddleware::class);
    $app->pipe(DispatchMiddleware::class);
    $app->pipe(NotFoundHandler::class);
};
