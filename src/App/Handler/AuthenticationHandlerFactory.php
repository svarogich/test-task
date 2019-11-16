<?php declare(strict_types=1);

namespace App\Handler;

use App\Models\UserRepository;
use App\Services\UserRegistration;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Authentication\Session\PhpSession;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class AuthenticationHandlerFactory
{
    public function __invoke(ContainerInterface $container): RequestHandlerInterface
    {
        $router = $container->get(RouterInterface::class);
        $template = $container->get(TemplateRendererInterface::class);
        $adapter = $container->get(PhpSession::class);
        $userRepository = $container->get(UserRepository::class);
        $userRegistration = $container->get(UserRegistration::class);

        return new AuthenticationHandler($router, $template, $adapter, $userRepository, $userRegistration);
    }
}
