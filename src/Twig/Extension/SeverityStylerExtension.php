<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Enum\SeverityEnum;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SeverityStylerExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('severity', $this->severityFilter(...)),
        ];
    }

    public function severityFilter(SeverityEnum $severity): string
    {
        return \sprintf('<span class="badge rounded-pill px-3 py-2" style="background-color: %s">%s</span>', $severity->color(), $severity->label());
    }
}
