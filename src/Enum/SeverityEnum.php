<?php

declare(strict_types=1);

namespace App\Enum;

enum SeverityEnum: int
{
    case CRITICAL = 3;

    case HIGH = 2;

    case MEDIUM = 1;

    case LOW = 0;

    public function color(): string
    {
        return match ($this) {
            SeverityEnum::CRITICAL => '#FF4858',
            SeverityEnum::HIGH => '#F28705',
            SeverityEnum::MEDIUM => '#F2CB05',
            SeverityEnum::LOW => '#A0CD60',
        };
    }

    public function label(): string
    {
        return match ($this) {
            SeverityEnum::CRITICAL => 'Critical',
            SeverityEnum::HIGH => 'High',
            SeverityEnum::MEDIUM => 'Medium',
            SeverityEnum::LOW => 'Low',
        };
    }
}
