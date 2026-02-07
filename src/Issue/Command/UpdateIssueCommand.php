<?php

declare(strict_types=1);

namespace App\Issue\Command;

use App\Entity\Issue;
use App\Issue\Event\IssueUpdatedEvent;

final class UpdateIssueCommand extends AbstractIssueCommand
{
    public function execute(Issue $issue): bool
    {
        try {
            $this->eventDispatcher->dispatch(event: $event = new IssueUpdatedEvent($issue), eventName: 'app.issue.pre_update');
            $issue = $event->getIssue();

            $this->save(issue: $issue);

            $this->eventDispatcher->dispatch(event: new IssueUpdatedEvent($issue), eventName: 'app.issue.post_update');

            return true;
        } catch (\Throwable $exception) {
            $this->logger->error($exception->getMessage());

            return false;
        }
    }
}
