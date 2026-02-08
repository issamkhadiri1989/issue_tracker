<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture implements DependentFixtureInterface
{
    private const int TOTAL_ISSUES = 160;

    public function load(ObjectManager $manager): void
    {
        $categories = ['Bug Report', 'Feature Request', 'Documentation', 'Support Ticket'];

        foreach ($categories as $category) {
            $object = new Category()->setName($category);
            $manager->persist($object);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
