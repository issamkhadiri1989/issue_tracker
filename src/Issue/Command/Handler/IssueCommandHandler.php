<?php

declare(strict_types=1);

namespace App\Issue\Command\Handler;

use App\Entity\Issue;
use App\Issue\Command\IssueCommandInterface;

class IssueCommandHandler
{
    public function handle(IssueCommandInterface $command, Issue $issue): bool
    {
        // @todo: dispatch here a Pre Execution Event
        $result = $command->execute($issue);
        // @todo: dispatch here a Post Execution Event

        return $result;
    }
}
