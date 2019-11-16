<?php declare(strict_types=1);

namespace App\Handler;

use App\Models\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Authentication\Session\PhpSession;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Router;
use Zend\Expressive\Session\SessionInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class AuthenticationHandler implements RequestHandlerInterface
{
    private const REDIRECT_ATTRIBUTE = 'authentication:redirect';

    /** @var Router\RouterInterface */
    private $router;

    /** @var null|TemplateRendererInterface */
    private $template;
    /**
     * @var PhpSession
     */
    private $adapter;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * AuthenticationHandler constructor.
     * @param Router\RouterInterface $router
     * @param TemplateRendererInterface $template
     * @param PhpSession $adapter
     * @param UserRepository $userRepository
     */
    public function __construct(
        Router\RouterInterface $router,
        TemplateRendererInterface $template,
        PhpSession $adapter,
        UserRepository $userRepository
    )
    {
        $this->router = $router;
        $this->template = $template;
        $this->adapter = $adapter;
        $this->userRepository = $userRepository;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var Router\RouteResult $routeResult */
        $routeResult = $request->getAttribute(Router\RouteResult::class);

        switch ($routeResult->getMatchedRoute()->getName()) {
            case 'get.login':
                return $this->handleGetLogin($request);
            case 'post.login':
                return $this->handlePostLogin($request);
            case 'get.registration':
                return $this->handleGetRegistration($request);
            case 'post.registration':
                return $this->handlePostRegistration($request);
            case 'get.pre-registration':
                return $this->handleGetPreRegistration($request);
            case 'post.pre-registration':
                return $this->handlePostPreRegistration($request);
            case 'post.check-email':
                return $this->handleCheckEmail($request);
            default:
                throw new \DomainException();
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @param SessionInterface $session
     * @return string
     */
    private function getRedirect(ServerRequestInterface $request, SessionInterface $session): string
    {
        $redirect = $session->get(self::REDIRECT_ATTRIBUTE);

        if (!$redirect) {
            $redirect = $request->getHeaderLine('Referer');
        }

        if (
            in_array($redirect, ['', '/login'], true)
            || substr($redirect, -6) === '/login'
        ) {
            $redirect = '/';
        }


        return $redirect;
    }

    /**
     * @param ServerRequestInterface $request
     * @param SessionInterface $session
     * @param string $redirect
     * @return ResponseInterface
     */
    private function handleLoginAttempt(ServerRequestInterface $request, SessionInterface $session, string $redirect): ResponseInterface
    {
        // User session takes precedence over user/pass POST in
        // the auth adapter so we remove the session prior
        // to auth attempt
        $session->unset(UserInterface::class);

        // Login was successful
        if ($this->adapter->authenticate($request)) {
            $session->unset(self::REDIRECT_ATTRIBUTE);
            return new RedirectResponse($redirect);
        }

        // Login failed
        return new HtmlResponse($this->template->render(
            'app::login',
            ['error' => 'Invalid credentials; please try again']
        ));
    }

    /**
     * @param ServerRequestInterface $request
     * @return HtmlResponse|RedirectResponse
     */
    private function handleGetLogin(ServerRequestInterface $request)
    {
        $session = $request->getAttribute('session');
        $redirect = $this->getRedirect($request, $session);

        // Redirect to main if already has auth
        $user = $request->getAttribute(UserInterface::class);
        if ($user instanceof UserInterface) {
            return new RedirectResponse($redirect);
        }

        // Display initial login form
        $session->set(self::REDIRECT_ATTRIBUTE, $redirect);
        return new HtmlResponse($this->template->render(
            'app::login',
            []
        ));
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface|RedirectResponse
     */
    private function handlePostLogin(ServerRequestInterface $request)
    {
        $session = $request->getAttribute('session');
        $redirect = $this->getRedirect($request, $session);


        // Redirect to main if already has auth
        $user = $request->getAttribute(UserInterface::class);
        if ($user instanceof UserInterface) {
            return new RedirectResponse($redirect);
        }

        return $this->handleLoginAttempt($request, $session, $redirect);
    }

    private function handleGetRegistration(ServerRequestInterface $request)
    {
        return new HtmlResponse($this->template->render(
            'app::registration',
            []
        ));
    }

    private function handleGetPreRegistration(ServerRequestInterface $request)
    {
        return new HtmlResponse($this->template->render(
            'app::pre_registration',
            []
        ));
    }

    private function handleCheckEmail(ServerRequestInterface $request)
    {
        // TODO refactor with InputFilter
        $email = $request->getParsedBody()['email'];
        // TODO check field

        $user = $this->userRepository->findByEmail($email);

        return new JsonResponse(['canBeUsed' => null === $user]);
    }

    private function handlePostPreRegistration(ServerRequestInterface $request)
    {
        // TODO refactor with InputFilter
        $email = $request->getParsedBody()['email'];
        // TODO check field

        $user = $this->userRepository->findByEmail($email);
    }
}
