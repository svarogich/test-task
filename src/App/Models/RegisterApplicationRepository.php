<?php declare(strict_types=1);

namespace App\Models;

use Zend\Db\Adapter\Adapter;

class RegisterApplicationRepository
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
     * @param string $token
     * @param bool $active
     * @return RegisterApplication|null
     */
    public function findByToken(string $token, bool $active = true): ?RegisterApplication
    {
        $result = $this->adapter->query('SELECT * FROM register_application WHERE token = :token and active = :active',
            [
                'token' => $token,
                'active' => (int)$active
            ]
        );

        if (false === $result) {
            throw new \RuntimeException(
                'An error occurred when preparing to fetch register application details from ' .
                'the repository; please verify your configuration'
            );
        }
        if ($result->count() > 1) {
            throw new \RuntimeException('Found more then one register application');
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
     * @return RegisterApplication|null
     */
    public function findById(int $id): ?RegisterApplication
    {
        $result = $this->adapter->query('SELECT * FROM register_application WHERE id = :id',
            [
                'id' => $id
            ]
        );

        if (false === $result) {
            throw new \RuntimeException(
                'An error occurred when preparing to fetch register application details from ' .
                'the repository; please verify your configuration'
            );
        }
        if ($result->count() > 1) {
            throw new \RuntimeException('Found more then one register application');
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
     * @return bool
     */
    public function unActivate(RegisterApplication $application): bool
    {
        $result = $this->adapter->query(
            'UPDATE register_application set
active = :active
WHERE id = :id',
            [
                'active' => 0,
                'id' => $application->getId(),
            ]);

        return $result->count() === 1 ? true : false;
    }

    /**
     * @param string $email
     * @return RegisterApplication
     */
    public function create(string $email): RegisterApplication
    {
        $this->adapter->query(
            'INSERT INTO register_application (`email`, `token`, `active`) 
values (:email,:token, :active)',
            ['email' => $email,
                'token' => bin2hex(openssl_random_pseudo_bytes(50)), // N.B. db field is only 100 chars
                'active' => 1,
            ]);

        //TODO check for null
        return $this->findById((int)$this->adapter->getDriver()->getLastGeneratedValue());
    }

    /**
     * @param array $row
     * @return RegisterApplication
     */
    private function createObject(array $row): RegisterApplication
    {
        return new RegisterApplication(
            (int)$row['id'],
            $row['email'],
            $row['token'],
            filter_var($row['active'], FILTER_VALIDATE_BOOLEAN)
        );
    }
}