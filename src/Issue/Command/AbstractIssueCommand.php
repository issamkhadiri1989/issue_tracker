<?php

declare(strict_types=1);

namespace App\Issue\Command;

use App\Entity\Issue;
use App\Issue\Event\AssigneeChangedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

abstract class AbstractIssueCommand implements IssueCommandInterface
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected LoggerInterface $logger,
        protected EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function save(Issue $issue, bool $forcePersist = false): void
    {
        try {
            if (true === $forcePersist) {
                $this->entityManager->persist($issue);
            }

            $assigneeHasChanged = $this->doDetectAssigneeChanged($issue);

            $this->entityManager->flush();

            if (true === $assigneeHasChanged) {
                $this->eventDispatcher->dispatch(eventName: 'app.issue.assignee_defined', event: new AssigneeChangedEvent($issue));
            }
        } catch (\Throwable $exception) {
            $this->logger->error($exception->getMessage());

            throw $exception;
        }
    }

    private function doDetectAssigneeChanged(Issue $issue): bool
    {
        $unitOfWork = $this->entityManager->getUnitOfWork();
        $unitOfWork->computeChangeSets();

        $changes = $unitOfWork->getEntityChangeSet($issue);

        if (!isset($changes['assignee'])) {
            return false;
        }

        // dispatch AssigneeChangedEvent only when the assignee is changed.
        $newAssignee = $changes['assignee'][1];

        return null !== $newAssignee;
    }
}
