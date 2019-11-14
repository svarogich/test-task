<?php

declare(strict_types=1);

namespace App\Handler;

use App\Models\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template\TemplateRendererInterface;

class HomePageHandler implements RequestHandlerInterface
{
    /** @var Router\RouterInterface */
    private $router;

    /** @var null|TemplateRendererInterface */
    private $template;

    /** @var UserRepository */
    private $userRepository;

    public function __construct(
        Router\RouterInterface $router,
        TemplateRendererInterface $template,
        UserRepository $userRepository
    )
    {
        $this->template = $template;
        $this->router = $router;
        $this->userRepository = $userRepository;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = [];
        return new HtmlResponse($this->template->render('app::home-page', $data));
    }
}
