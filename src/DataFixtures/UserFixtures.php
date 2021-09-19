<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\DataProvider\UserDataProvider;
use App\Entity\Employee;
use App\Entity\Store;
use App\Traits\UserPasswordEncoderAwareTrait;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    use UserPasswordEncoderAwareTrait;

    public const ADMIN_USER_REFERENCE = 'admin-user';

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        /** @var Store $store */
        $store = $this->getReference(StoreFixtures::MAIN_OFFICE_REFERENCE);

        $userAdmin = new Employee(
            $store,
            "a@b.c",
            'Админ',
            'Админ',
            'Админ',
            "0999999999",
            [UserDataProvider::ROLE_ADMIN]
        );

        $encoded = $this->passwordEncoder->encodePassword($userAdmin, "123");
        $userAdmin->setPassword($encoded);

        $manager->persist($userAdmin);
        $manager->flush();

        $this->addReference(self::ADMIN_USER_REFERENCE, $userAdmin);
    }

    /**
     * @return array|string[]
     */
    public function getDependencies(): array
    {
        return [
            StoreFixtures::class,
        ];
    }
}
