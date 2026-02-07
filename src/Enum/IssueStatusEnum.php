<?php

declare(strict_types=1);

namespace App\Enum;

enum IssueStatusEnum: string
{
    case OPENED = 'opened';
    case CLOSED = 'closed';
    case BLOCKED = 'blocked';
    case WIP = 'wip';
    case REVIEWED = 'reviewed';
    case TESTED = 'tested';
    case DONE = 'done';
    case UNDONE = 'undone';
    case BACKLOG = 'backlog';

    public function humanify(): string
    {
        return match ($this) {
            self::OPENED => 'Open',
            self::CLOSED => 'Closed',
            self::BLOCKED => 'Blocked, need help or info',
            self::WIP => 'Work in progress',
            self::REVIEWED => 'Code review',
            self::TESTED => 'Started Testing',
            self::DONE => 'Done',
            self::UNDONE => 'KO. Need rework',
            self::BACKLOG => 'Backlog',
        };
    }
}
