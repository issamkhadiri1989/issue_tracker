<?php

declare(strict_types=1);

namespace App\Issue\Command;

use App\Entity\Issue;
use App\Issue\Event\IssueCreatedEvent;

final class CreateIssueCommand extends AbstractIssueCommand
{
    public function execute(Issue $issue): bool
    {
        try {
            $this->eventDispatcher->dispatch(event: $event = new IssueCreatedEvent($issue), eventName: 'app.issue.pre_create');

            $issue = $event->getIssue();
            $this->save(issue: $issue, forcePersist: true);

            $this->eventDispatcher->dispatch(event: new IssueCreatedEvent($issue), eventName: 'app.issue.post_create');

            return true;
        } catch (\Throwable $exception) {
            $this->logger->error($exception->getMessage());

            return false;
        }
    }
}
