<?php

declare(strict_types=1);

namespace App\Issue\Event;

use App\Entity\Issue;
use App\Entity\Member;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractEvent extends Event
{
    public function __construct(private Issue $issue, private ?Member $assignee = null, private ?string $url = null)
    {
        $this->assignee ??= $issue->getAssignee();
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    public function setIssue(Issue $issue): void
    {
        $this->issue = $issue;
    }

    public function getIssue(): Issue
    {
        return $this->issue;
    }

    public function getAssignee(): ?Member
    {
        return $this->assignee;
    }
}
