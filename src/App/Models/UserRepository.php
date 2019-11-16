<?php declare(strict_types=1);

namespace App\Models;

use App\Helpers\PasswordHelper;
use App\InputFilters\ClientInputFilter;
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

    /**
     * @param string $credential
     * @param string|null $password
     * @return UserInterface|null
     */
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
            return $this->createObject($row);
        }
        return null;
    }

    /**
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        $result = $this->adapter->query('SELECT * FROM user WHERE email = :email',
            [
                'email' => $email
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

        return $this->createObject($row);
    }

    /**
     * @param int $id
     * @return User|null
     */
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

        return $this->createObject($row);
    }

    /**
     * @param RegisterApplication $application
     * @param string $password
     * @return User
     */
    public function createUserFromApplication(RegisterApplication $application, string $password): User
    {
        $this->adapter->query(
            'INSERT INTO user (`email`, `name`, `password`) 
VALUES (:email, :name, :password)',
            [
                'email' => $application->getEmail(),
                'name' => $application->getEmail(),
                'password' => PasswordHelper::getHash($password),
            ]);

        //TODO check for null
        return $this->findById((int)$this->adapter->getDriver()->getLastGeneratedValue());
    }

    /**
     * @param array $row
     * @return User
     */
    private function createObject(array $row): User
    {
        return new User(
            (int)$row['id'],
            $row['email'],
            $row['name']
        );
    }
}