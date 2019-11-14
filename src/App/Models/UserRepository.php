<?php declare(strict_types=1);

namespace App\Models;

use Zend\Db\Adapter\Adapter;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Authentication\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    /**
     * @var Adapter
     */
    private $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function authenticate(string $credential, string $password = null): ?UserInterface
    {
        $result = $this->adapter->query('SELECT * FROM user WHERE email = :email',
            [
                'email' => $credential
            ]
        );

        if (false === $result) {
            throw new \RuntimeException(
                'An error occurred when preparing to fetch user details from ' .
                'the repository; please verify your configuration'
            );
        }
        if ($result->count() > 1) {
            throw new \RuntimeException('Found more then one user');
        }

        $rowObject = $result->current();
        if (!$rowObject) {
            return null;
        }
        $row = $rowObject->getArrayCopy();

        if (password_verify($password ?? '', $row['password'])) {
            return $this->createUser($row);
        }
        return null;
    }

    public function findByEmail(string $login): ?User
    {
        $result = $this->adapter->query('SELECT * FROM user WHERE email = :email',
            [
                'email' => $login
            ]
        );

        if (false === $result) {
            throw new \RuntimeException(
                'An error occurred when preparing to fetch user details from ' .
                'the repository; please verify your configuration'
            );
        }
        if ($result->count() > 1) {
            throw new \RuntimeException('Found more then one user');
        }

        $rowObject = $result->current();
        if (!$rowObject) {
            return null;
        }
        $row = $rowObject->getArrayCopy();

        return $this->createUser($row);
    }

    public function findById(int $id): ?User
    {
        $result = $this->adapter->query('SELECT * FROM user WHERE id = :id',
            [
                'id' => $id
            ]
        );

        if (false === $result) {
            throw new \RuntimeException(
                'An error occurred when preparing to fetch user details from ' .
                'the repository; please verify your configuration'
            );
        }
        if ($result->count() > 1) {
            throw new \RuntimeException('Found more then one user');
        }

        $rowObject = $result->current();
        if (!$rowObject) {
            return null;
        }
        $row = $rowObject->getArrayCopy();

        return $this->createUser($row);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        if (!empty($user->getPassword())) {
            $result = $this->adapter->query(
                'UPDATE user set 
name = :name,
active = :active
WHERE id = :id',
                [
                    'name' => $user->getName(),
                    'password' => $user->getPassword(),
                    'active' => $user->isActive(),
                    'id' => $user->getId(),
                ]);
        } else {
            $result = $this->adapter->query(
                'UPDATE user set 
name = :name,
active = :active
WHERE id = :id',
                [
                    'name' => $user->getName(),
                    'active' => $user->isActive(),
                    'id' => $user->getId(),
                ]);
        }

        $user->setPassword(null);

        return $result->count() === 1 ? true : false;

    }

    private function createUser(array $row): User
    {
        return new User(
            (int)$row['id'],
            $row['email'],
            $row['name'],
            filter_var($row['active'], FILTER_VALIDATE_BOOLEAN)
        );
    }
}