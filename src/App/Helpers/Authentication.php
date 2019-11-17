<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\User;
use App\Models\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Authentication\AuthenticationInterface;
use Zend\Expressive\Authentication\Session\Exception\MissingSessionContainerException;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Authentication\UserRepositoryInterface;
use Zend\Expressive\Session\SessionInterface;
use Zend\Expressive\Session\SessionMiddleware;
use function strtoupper;

class Authentication implements AuthenticationInterface
{
    /**
     * @var UserRepositoryInterface|UserRepository
     *
     */
    private $repository;

    /**
     * @var array
     */
    private $config;

    /**
     * @var callable
     */
    private $responseFactory;

    public function __construct(
        UserRepositoryInterface $repository,
        array $config,
        callable $responseFactory
    )
    {
        $this->repository = $repository;
        $this->config = $config;

        // Ensures type safety of the composed factory
        $this->responseFactory = function () use ($responseFactory) : ResponseInterface {
            return $responseFactory();
        };
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(ServerRequestInterface $request): ?UserInterface
    {
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        if (!$session) {
            throw MissingSessionContainerException::create();
        }

        if ($session->has(UserInterface::class)) {
            return $this->createUserFromSession($session);
        }

        if ('POST' !== strtoupper($request->getMethod())) {
            return null;
        }

        $params = $request->getParsedBody();
        $username = $this->config['username'] ?? 'username';
        $password = $this->config['password'] ?? 'password';
        if (!isset($params[$username]) || !isset($params[$password])) {
            return null;
        }

        $user = $this->repository->authenticate(
            $params[$username],
            $params[$password]
        );

        /** @var User $user */
        if (null !== $user) {
            $session->set(UserInterface::class, ['id' => $user->getId(), 'username' => $user->getIdentity()]);
            $session->regenerate();
        }
        return $user;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function unauthorizedResponse(ServerRequestInterface $request): ResponseInterface
    {
        return ($this->responseFactory)()
            ->withHeader(
                'Location',
                $this->config['redirect']
            )
            ->withStatus(302);
    }

    /**
     * @param SessionInterface $session
     * @return UserInterface|null
     */
    private function createUserFromSession(SessionInterface $session): ?UserInterface
    {
        $userInfo = $session->get(UserInterface::class);

        $user =  $this->repository->findById((int)$userInfo['id']);

        return $user;
    }
}
