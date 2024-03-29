<?php declare(strict_types=1);

namespace App\Handler;

use App\Models\UserRepository;
use App\Services\RssReader;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class HomePageHandlerFactory
{
    public function __invoke(ContainerInterface $container): RequestHandlerInterface
    {
        return new HomePageHandler(
            $container->get(RouterInterface::class),
            $container->get(TemplateRendererInterface::class),
            $container->get(UserRepository::class),
            $container->get(RssReader::class)
        );
    }
}
