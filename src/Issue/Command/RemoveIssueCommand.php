<?php

declare(strict_types=1);

namespace App\Issue\Command;

use App\Entity\Issue;

final class RemoveIssueCommand extends AbstractIssueCommand
{
    public function execute(Issue $issue): bool
    {
        try {
            $this->entityManager->remove($issue);

            $this->save(issue: $issue);

            return true;
        } catch (\Throwable $exception) {
            $this->logger->error($exception->getMessage());

            return false;
        }
    }
}
