<?php

declare(strict_types=1);

namespace App\User\Domain\Entity;

use App\Shared\Domain\AggregateRoot;
use App\User\Domain\ValueObject\UserId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
#[ORM\UniqueConstraint(name: 'UNIQ_USERS_EMAIL', columns: ['email'])]
final class User extends AggregateRoot implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private string $email;

    #[ORM\Column(type: 'string')]
    private string $password;

    /** @var list<string> */
    #[ORM\Column(type: 'json')]
    private array $roles;

    private function __construct(
        UserId $id,
        string $email,
        string $hashedPassword,
        array $roles,
    ) {
        $this->id = $id->value;
        $this->email = $email;
        $this->password = $hashedPassword;
        $this->roles = $roles;
    }

    public static function create(string $email, string $hashedPassword, array $roles = []): self
    {
        return new self(
            UserId::generate(),
            $email,
            $hashedPassword,
            $roles,
        );
    }

    public function getId(): UserId
    {
        return UserId::fromString($this->id);
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /** @return list<string> */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_values(array_unique($roles));
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function eraseCredentials(): void
    {
    }
}
