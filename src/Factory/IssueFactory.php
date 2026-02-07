<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Issue;
use App\Enum\SeverityEnum;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

final class IssueFactory extends PersistentObjectFactory
{
    public function __construct()
    {
    }

    #[\Override]
    public static function class(): string
    {
        return Issue::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        $cases = SeverityEnum::cases();

        return [
            'description' => self::faker()->text(),
            'title' => self::faker()->text(100),
            'severity' => $cases[\array_rand($cases)],
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this// ->afterInstantiate(function(Issue $issue): void {})
        ;
    }
}
