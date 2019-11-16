<?php declare(strict_types=1);

namespace App\Models;

use Zend\Expressive\Authentication\UserInterface;

class User implements UserInterface
{
    /** @var int|null */
    private $id;

    /** @var string */
    private $email;

    /** @var string */
    private $name;

    /** @var string|null */
    private $password;

    public function __construct(?int $id, string $email, string $name)
    {
        $this->id = $id;
        $this->email = $email;
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the unique user identity (id, username, email address or ...)
     */
    public function getIdentity(): string
    {
        return $this->email;
    }

    /**
     * Get all user roles
     *
     * @return Iterable
     */
    public function getRoles(): iterable
    {
        return [];
    }

    /**
     * Get a detail $name if present, $default otherwise
     */
    public function getDetail(string $name, $default = null)
    {
        return '';
    }

    /**
     * Get all the details, if any
     */
    public function getDetails(): array
    {
        return [];
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string|null $password
     */
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }
}