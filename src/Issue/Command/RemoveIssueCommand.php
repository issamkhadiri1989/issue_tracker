<?php

declare(strict_types=1);

namespace App\Issue\Command;

use App\Entity\Issue;
use App\Issue\Event\IssueRemovedEvent;

final class RemoveIssueCommand extends AbstractIssueCommand
{
    public function execute(Issue $issue): bool
    {
        try {
            $this->eventDispatcher->dispatch(eventName: 'app.issue.pre_remove', event: new IssueRemovedEvent($issue));

            $this->entityManager->remove($issue);
            $this->save(issue: $issue);

            $this->eventDispatcher->dispatch(eventName: 'app.issue.post_remove', event: new IssueRemovedEvent($issue));

            return true;
        } catch (\Throwable $exception) {
            $this->logger->error($exception->getMessage());

            return false;
        }
    }
}
