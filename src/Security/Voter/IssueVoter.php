<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Issue;
use App\Entity\Member;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class IssueVoter extends Voter
{
    public function __construct(private Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Issue && \in_array($attribute, ['CAN_DELETE_ISSUE', 'CAN_EDIT_ISSUE']);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $currentUser = $token->getUser();

        if (!$currentUser instanceof Member) { // make sure that the user is an instance of Member. this makes sure that the user is logged in
            return false;
        }

        /** @var Issue $issue */
        $issue = $subject;

        return match ($attribute) {
            'CAN_DELETE_ISSUE' => $this->canDeleteIssue(member: $currentUser, issue: $issue),
            'CAN_EDIT_ISSUE' => $this->canEditIssue(member: $currentUser, issue: $issue),
            default => false,
        };
    }

    private function canDeleteIssue(Member $member, Issue $issue): bool
    {
        // make sure that only the admin and the reporter can delete the issue
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return $issue->getReporter() === $member;
    }

    private function canEditIssue(Member $member, Issue $issue): bool
    {
        // make sure that the issue can be edited by everyone (to change the status or make some updates)
        // admin can do anything
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return true === $issue->canBeChanged();
    }
}
