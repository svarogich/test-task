<?php declare(strict_types=1);

namespace App\Services;

use App\Models\RegisterApplication;
use App\Models\RegisterApplicationRepository;
use App\Models\User;
use App\Models\UserRepository;
use RuntimeException;
use Zend\Expressive\Helper\ServerUrlHelper;
use Zend\Expressive\Router\RouterInterface;
use Zend\Mail\Message;
use Zend\Mail\Transport\TransportInterface;

class UserRegistration
{
    /**
     * @var TransportInterface
     */
    private $emailTransport;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var RegisterApplicationRepository
     */
    private $applicationRepository;

    /**
     * @var string
     */
    private $fromEmail;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ServerUrlHelper
     */
    private $serverUrlHelper;

    /**
     * AuthenticationHandler constructor.
     * @param UserRepository $userRepository
     * @param RegisterApplicationRepository $applicationRepository
     * @param TransportInterface $emailTransport
     * @param RouterInterface $router
     * @param ServerUrlHelper $serverUrlHelper
     * @param string $fromEmail
     */
    public function __construct(
        UserRepository $userRepository,
        RegisterApplicationRepository $applicationRepository,
        TransportInterface $emailTransport,
        RouterInterface $router,
        ServerUrlHelper $serverUrlHelper,
        string $fromEmail
    )
    {
        $this->userRepository = $userRepository;
        $this->applicationRepository = $applicationRepository;
        $this->emailTransport = $emailTransport;
        $this->fromEmail = $fromEmail;
        $this->router = $router;
        $this->serverUrlHelper = $serverUrlHelper;
    }

    /**
     * @param string $email
     * @return RegisterApplication
     */
    public function preRegister(string $email): RegisterApplication
    {
        $user = $this->userRepository->findByEmail($email);
        if (null !== $user) {
            throw new RuntimeException(sprintf('User with email %s already exist', $email));
        }

        $application = $this->applicationRepository->create($email);

        $message = new Message();
        $message->addTo($email);
        $message->addFrom($this->fromEmail);
        $message->setSubject('Email verification');
        $message->setBody(
            sprintf(
                "To continue registration, please use link:\n %s",
                $this->generateRegistrationUrl($application->getToken())
            )
        );

        $this->emailTransport->send($message);

        return $application;
    }

    /**
     * @param string $token
     * @return bool
     */
    public function verifyToken(string $token): bool
    {
        // TODO check if token is last generated for given email
        $application = $this->applicationRepository->findByToken($token);
        return (null !== $application);
    }

    /**
     * @param $token
     * @param $password
     * @return User
     */
    public function register($token, $password):User
    {
        // TODO check if token is last generated for given email
        $application = $this->applicationRepository->findByToken($token);

        return $this->userRepository->createUserFromApplication($application,$password);
    }

    /**
     * @param $token
     * @return string
     */
    private function generateRegistrationUrl($token): string
    {
        $path = $this->router->generateUri('get.registration');
        return sprintf("%s?token=%s", $this->serverUrlHelper->generate($path), $token);
    }
}
