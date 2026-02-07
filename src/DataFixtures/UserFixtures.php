<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Member;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new Member();
        $admin->setEmail('admin@issue-tracker.com')
            ->setPassword($this->passwordHasher->hashPassword($admin, '123123'))
            ->setRoles(['ROLE_ADMIN'])
            ->setFullName('Issam KHADIRI');
        $manager->persist($admin);

        $user01 = new Member();
        $user01->setEmail('user01@issue-tracker.com')
            ->setPassword($this->passwordHasher->hashPassword($user01, '123123'))
            ->setRoles([])
            ->setFullName('User 01');
        $manager->persist($user01);

        $user02 = new Member();
        $user02->setEmail('user02@issue-tracker.com')
            ->setPassword($this->passwordHasher->hashPassword($user02, '123123'))
            ->setRoles([])
            ->setFullName('User 01');
        $manager->persist($user02);

        $manager->flush();

        $this->addReference('reporter', $admin);
    }
}
