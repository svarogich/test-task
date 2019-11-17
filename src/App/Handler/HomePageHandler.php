<?php

declare(strict_types=1);

namespace App\Handler;

use App\Models\UserRepository;
use App\Services\RssReader;
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
    /**
     * @var RssReader
     */
    private $rssReader;

    /**
     * HomePageHandler constructor.
     * @param Router\RouterInterface $router
     * @param TemplateRendererInterface $template
     * @param UserRepository $userRepository
     * @param RssReader $rssReader
     */
    public function __construct(
        Router\RouterInterface $router,
        TemplateRendererInterface $template,
        UserRepository $userRepository,
        RssReader $rssReader
    )
    {
        $this->template = $template;
        $this->router = $router;
        $this->userRepository = $userRepository;
        $this->rssReader = $rssReader;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $news = $this->rssReader->getLastFive();
        return new HtmlResponse($this->template->render('app::home-page', ['news' => $news]));
    }
}
