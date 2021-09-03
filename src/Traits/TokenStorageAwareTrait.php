<?php

declare(strict_types=1);

namespace App\Traits;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

trait TokenStorageAwareTrait
{
    /**
     * @var TokenStorageInterface
     */
    protected TokenStorageInterface $tokenStorage;

    /**
     * @required
     * @param TokenStorageInterface $tokenStorage
     */
    public function setTokenStorage(TokenStorageInterface $tokenStorage): void
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return User|null
     */
    public function getLoggedInUser(): ?User
    {
        $token = $this->tokenStorage->getToken();

        if ($token !== null) {
            $user = $token->getUser();

            if ($user instanceof User) {
                return $user;
            }
        }

        return null;
    }
}
