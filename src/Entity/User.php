<?php

declare(strict_types=1);

namespace App\Entity;

use App\DataProvider\UserDataProvider;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\MappedSuperclass()
 */
class User implements UserInterface, \Serializable
{

    /**
     * @var string
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id()
     */
    protected string $id;

    /**
     * @var string
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    protected string $firstName;

    /**
     * @var string
     * @ORM\Column(name="last_name", type="string", length=255)
     */
    protected string $lastName;

    /**
     * @var string|null
     * @ORM\Column(name="patronymic", type="string", length=255, nullable=true)
     */
    protected ?string $patronymic;

    /**
     * @var array|string[]
     * @ORM\Column(name="roles", type="json")
     */
    protected array $roles;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=255)
     */
    protected string $email;

    /**
     * @var string
     * @ORM\Column(name="phone_number", type="string", length=11)
     */
    protected string $phoneNumber;

    /**
     * @var bool
     * @ORM\Column(name="is_active", type="boolean")
     */
    protected bool $active;

    /**
     * @var string
     * @ORM\Column(name="salt", type="string", length=255)
     */
    protected string $salt;

    /**
     * @var string
     * @ORM\Column(name="password", type="string", length=255)
     */
    protected string $password;


    /**
     * @var int
     * @ORM\Column(name="login_attempts_counter", type="smallint")
     */
    protected int $loginAttemptsCounter;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(name="create_time", type="datetime_immutable")
     */
    protected \DateTimeImmutable $createTime;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(name="update_time", type="datetime_immutable")
     */
    protected \DateTimeImmutable $updateTime;

    /**
     * User constructor.
     * @param string $email
     * @param string $lastName
     * @param string $firstName
     * @param string|null $patronymic
     * @param string $phoneNumber
     * @param array $roles
     */
    public function __construct(
        string $email,
        string $lastName,
        string $firstName,
        ?string $patronymic,
        string $phoneNumber,
        array $roles
    ) {
        $this->id = Uuid::uuid4()->toString();
        $this->salt = md5(uniqid('', true));
        $this->active = true;
        $this->email = $email;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->patronymic = $patronymic;
        $this->phoneNumber = $phoneNumber;
        $this->roles = $roles;
        $this->password = '';
        $this->loginAttemptsCounter = 0;

        $date = new \DateTimeImmutable();
        $this->createTime = $date;
        $this->updateTime = $date;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string|null
     */
    public function getPatronymic(): ?string
    {
        return $this->patronymic;
    }

    /**
     * @param string $patronymic
     */
    public function setPatronymic(string $patronymic): void
    {
        $this->patronymic = $patronymic;
    }

    /**
     * @return array|string[]
     */
    public function getRoles(): array
    {
        return array_unique([UserDataProvider::ROLE_USER, ...$this->roles]);
    }

    /**
     * @param string $role
     * @return bool
     */
    public function isGranted(string $role): bool
    {
        return in_array($role, $this->getRoles());
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
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
    public function getRealEmail(): string
    {
        return preg_match("/@example/i", $this->email) ? "" : $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    /**
     * @return string
     */
    public function getRealPhoneNumber(): string
    {
        return preg_match("/@example/i", $this->email) ? "" : $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     */
    public function setPhoneNumber(string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }


    public function clearLoginAttempts(): void
    {
        $this->loginAttemptsCounter = 0;
    }

    public function addLoginAttempt(): void
    {
        $this->loginAttemptsCounter++;
    }

    /**
     * @return bool
     */
    public function isLoginAttemptsOverLimit(): bool
    {
        return $this->loginAttemptsCounter > UserDataProvider::LOGIN_ATTEMPTS_LIMIT ?: false;
    }

    /**
     * @return string
     */
    public function getSalt(): string
    {
        return $this->salt;
    }

    /**
     * @param string $salt
     */
    public function setSalt(string $salt): void
    {
        $this->salt = $salt;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @ORM\PrePersist()
     */
    public function onPrePersist(): void
    {
        $this->createTime = new \DateTimeImmutable('now');
        $this->updateTime = new \DateTimeImmutable('now');
    }

    /**
     * @ORM\PreUpdate()
     */
    public function onPreUpdate(): void
    {
        $this->updateTime = new \DateTimeImmutable('now');
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreateTime(): \DateTimeImmutable
    {
        return $this->createTime;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getUpdateTime(): \DateTimeImmutable
    {
        return $this->updateTime;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        $fullName = $this->lastName . ' ' . $this->firstName;

        if ($this->patronymic !== null) {
            $fullName .= ' ' . $this->patronymic;
        }

        return $fullName;
    }

    /**
     * @return string|null
     */
    public function serialize(): ?string
    {
        return serialize([
            $this->id,
            $this->email,
            $this->password,
            $this->salt
        ]);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->email,
            $this->password,
            $this->salt
        ) = unserialize($serialized, array('allowed_classes' => false));
    }
}
