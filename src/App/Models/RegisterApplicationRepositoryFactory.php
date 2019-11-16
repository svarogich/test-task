<?php declare(strict_types=1);

namespace App\Models;

use Psr\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;

class RegisterApplicationRepositoryFactory
{
    /**
     * @param ContainerInterface $container
     * @return RegisterApplicationRepository
     */
    public function __invoke(ContainerInterface $container): RegisterApplicationRepository
    {
        $db = $container->get(Adapter::class);

        return new RegisterApplicationRepository($db);
    }
}