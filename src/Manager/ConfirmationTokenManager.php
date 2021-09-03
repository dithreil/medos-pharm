<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Client;
use App\Entity\ConfirmationToken;
use App\Repository\ConfirmationTokenRepository;
use Doctrine\ORM\EntityManagerInterface;

class ConfirmationTokenManager
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var ConfirmationTokenRepository
     */
    private ConfirmationTokenRepository $repository;

    /**
     * ConfirmationTokenManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param ConfirmationTokenRepository $repository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ConfirmationTokenRepository $repository
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }

    /**
     * @param Client $user
     * @param string $tokenType
     * @return ConfirmationToken
     */
    public function create(Client $user, string $tokenType): ConfirmationToken
    {
        $token = new ConfirmationToken($user, $tokenType);
        $this->entityManager->persist($token);

        return $token;
    }

    /**
     * @param Client $user
     * @return ConfirmationToken
     */
    public function createEmailConfirmation(Client $user): ConfirmationToken
    {
        return $this->create($user, ConfirmationToken::TOKEN_TYPE_EMAIL_CONFIRMATION);
    }

    /**
     * @param string $token
     * @return ConfirmationToken|null
     */
    public function getEmailConfirmationToken(string $token): ?ConfirmationToken
    {
        return $this->repository->findOneBy([
            'token' => $token,
            'tokenType' => ConfirmationToken::TOKEN_TYPE_EMAIL_CONFIRMATION
        ]);
    }
}
