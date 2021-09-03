<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConfirmationTokenRepository")
 * @ORM\Table(name="confirmation_tokens")
 */
class ConfirmationToken
{
    const TOKEN_TYPE_EMAIL_CONFIRMATION = 'email.confirmation';

    /**
     * @var string
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id()
     */
    private string $id;

    /**
     * @var Client
     * @ORM\OneToOne(targetEntity=Client::class)
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    private Client $client;

    /**
     * @var string
     * @ORM\Column(name="token_type", type="string")
     */
    private string $tokenType;

    /**
     * @var string
     * @ORM\Column(name="token", type="string")
     */
    private string $token;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(name="create_time", type="datetime_immutable")
     */
    private \DateTimeImmutable $createTime;

    /**
     * ConfirmationToken constructor.
     * @param Client $client
     * @param string $tokenType
     */
    public function __construct(Client $client, string $tokenType)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->client = $client;
        $this->tokenType = $tokenType;
        $this->token = md5(uniqid('', true));
        $this->createTime = new \DateTimeImmutable('now');
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @return string
     */
    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreateTime(): \DateTimeImmutable
    {
        return $this->createTime;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return true;
    }
}
