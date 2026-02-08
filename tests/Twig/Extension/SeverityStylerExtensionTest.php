<?php

declare(strict_types=1);

namespace App\Tests\Twig\Extension;

use App\Enum\SeverityEnum;
use App\Twig\Extension\SeverityStylerExtension;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Twig\TwigFilter;

class SeverityStylerExtensionTest extends TestCase
{
    #[DataProvider('getSeverities')]
    public function testSomething(SeverityEnum $severityEnum, string $expectedOutput): void
    {
        $extension = new SeverityStylerExtension();

        $output = $extension->severityFilter($severityEnum);
        $this->assertSame($expectedOutput, $output);

        $filters = $extension->getFilters();

        $this->assertIsArray($filters);
        $this->assertTrue(\array_all(array: $filters, callback: fn ($item) => $item instanceof TwigFilter));
    }

    public static function getSeverities(): \Generator
    {
        yield [SeverityEnum::LOW, '<span class="badge rounded-pill px-3 py-2" style="background-color: #A0CD60">Low</span>'];
        yield [SeverityEnum::CRITICAL, '<span class="badge rounded-pill px-3 py-2" style="background-color: #FF4858">Critical</span>'];
        yield [SeverityEnum::MEDIUM, '<span class="badge rounded-pill px-3 py-2" style="background-color: #F2CB05">Medium</span>'];
        yield [SeverityEnum::HIGH, '<span class="badge rounded-pill px-3 py-2" style="background-color: #F28705">High</span>'];
    }
}
