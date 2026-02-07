<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Member;
use App\Factory\IssueFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture implements DependentFixtureInterface
{
    private const int TOTAL_ISSUES = 160;

    public function load(ObjectManager $manager): void
    {
        $categories = ['Bug Report', 'Feature Request', 'Documentation', 'Support Ticket'];

        //        $items = [];

        foreach ($categories as $category) {
            $object = new Category()->setName($category);
            $manager->persist($object);

            //            $items[] = $object;
        }

        //        $reporter = $this->getReference('reporter', Member::class);
        //
        //        IssueFactory::createMany(self::TOTAL_ISSUES, fn () => [
        //            'category' => $items[\array_rand($items)],
        //            'reporter' => $reporter,
        //        ]);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
