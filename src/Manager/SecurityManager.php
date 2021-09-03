<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\User;
use App\Exception\AppException;
use App\Manager\Mail\MailManager;
use App\Traits\EntityManagerAwareTrait;
use App\Traits\MailSenderAwareTrait;
use App\Traits\TokenStorageAwareTrait;
use App\Traits\UserPasswordEncoderAwareTrait;
use Symfony\Component\HttpFoundation\Response;

class SecurityManager
{
    use EntityManagerAwareTrait;
    use MailSenderAwareTrait;
    use TokenStorageAwareTrait;
    use UserPasswordEncoderAwareTrait;

    /**
     * @param string $oldPassword
     * @param string $newPassword
     * @throws AppException
     */
    public function changePassword(string $oldPassword, string $newPassword): void
    {
        $user = $this->getLoggedInUser();

        if (!$user instanceof User) {
            throw new AppException('Auth error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if ($this->passwordEncoder->isPasswordValid($user, $oldPassword)) {
            $encoded = $this->passwordEncoder->encodePassword($user, $newPassword);
            $user->setPassword($encoded);
            $this->entityManager->flush();

            $this->mailManager->sendTwigMailHtml(
                MailManager::NEW_PASSWORD_TEMPLATE,
                ['mail_to' => [$user->getEmail()]],
                ['user' => $user, 'password' => $newPassword]
            );
        } else {
            throw new AppException('Old password is incorrect', Response::HTTP_BAD_REQUEST);
        }
    }
}
