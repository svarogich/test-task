<?php declare(strict_types=1);

namespace App\Services;

use App\Models\RegisterApplicationRepository;
use App\Models\UserRepository;
use Psr\Container\ContainerInterface;
use Zend\Diactoros\Uri;
use Zend\Expressive\Helper\ServerUrlHelper;
use Zend\Expressive\Router\RouterInterface;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;

class UserRegistrationFactory
{
    public function __invoke(ContainerInterface $container): UserRegistration
    {
        $userRepository = $container->get(UserRepository::class);
        $applicationRepository = $container->get(RegisterApplicationRepository::class);

        $config = $container->get('config')['smtp'] ?? [];

        if (!isset($config['host'], $config['port'], $config['user'], $config['password'], $config['from'], $config['base_url'])) {
            throw new \RuntimeException(
                'The smtp configuration is missing'
            );
        }

        $options = [
            'host' => $config['host'],
            'port' => $config['port'],
            'connection_class' => 'login',
            'connection_config' => [
                'username' => $config['user'],
                'password' => $config['password'],
            ],
        ];

        if (!empty($config['user']) && !empty($config['password'])) {
            $options['connection_config'] = [
                'username' => $config['user'],
                'password' => $config['password'],
            ];
        }

        $transport = new Smtp();
        $transport->setOptions(new SmtpOptions($options));

        $router = $container->get(RouterInterface::class);

        $helper = new ServerUrlHelper();
        $helper->setUri(new Uri($config['base_url']));

        return new UserRegistration($userRepository, $applicationRepository, $transport, $router, $helper, $config['from']);
    }
}
