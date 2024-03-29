<?php declare(strict_types=1);

namespace App\Models;

use Psr\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;

class UserRepositoryFactory
{
    /**
     * @param ContainerInterface $container
     * @return UserRepository
     */
    public function __invoke(ContainerInterface $container): UserRepository
    {
        $db = $container->get(Adapter::class);

        return new UserRepository($db);
    }
}