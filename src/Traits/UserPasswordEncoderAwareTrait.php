<?php

declare(strict_types=1);

namespace App\Traits;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

trait UserPasswordEncoderAwareTrait
{
    /**
     * @var UserPasswordEncoderInterface
     */
    protected UserPasswordEncoderInterface $passwordEncoder;

    /**
     * @required
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function setPasswordEncoder(UserPasswordEncoderInterface $passwordEncoder): void
    {
        $this->passwordEncoder = $passwordEncoder;
    }
}
