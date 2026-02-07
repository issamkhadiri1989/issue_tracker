<?php

declare(strict_types=1);

namespace App\Issue\Workflow;

use App\Entity\Issue;
use App\Enum\IssueStatusEnum;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Workflow\WorkflowInterface;

final readonly class TransitionExtractor
{
    public function __construct(#[Target(name: 'issueStateMachine')] private WorkflowInterface $workflow)
    {
    }

    public function extractPossibleTransitions(Issue $issue): array
    {
        $transitions = $this->workflow->getEnabledTransitions($issue);
        $choices = [];
        foreach ($transitions as $transition) {
            foreach ($transition->getTos() as $toState) {
                $enumCase = IssueStatusEnum::tryFrom($toState);
                if (null !== $enumCase) {
                    $choices[] = $enumCase;
                }
            }
        }

        // always include the current issue's status
        $choices[] = $issue->getStatus();

        return $choices;
    }
}
