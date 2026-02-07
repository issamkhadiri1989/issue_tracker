<?php

declare(strict_types=1);

namespace App\Issue\Command;

use App\Entity\Issue;

interface IssueCommandInterface
{
    public function execute(Issue $issue): bool;
}
