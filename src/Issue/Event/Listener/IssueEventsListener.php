<?php

declare(strict_types=1);

namespace App\Issue\Event\Listener;

use App\Entity\Issue;
use App\Entity\Member;
use App\Issue\Event\AbstractEvent;
use App\Issue\Event\IssueCreatedEvent;
use App\Notification\Mailer;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\UriSigner;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Workflow\WorkflowInterface;

final readonly class IssueEventsListener
{
    public function __construct(
        private Security $security,
        #[Target(name: 'issueStateMachine')]
        private WorkflowInterface $workflow,
        private Mailer $mailer,
        private UriSigner $signer,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    #[AsEventListener(event: 'app.issue.pre_create', priority: 100)]
    public function assignReporter(IssueCreatedEvent $event): void
    {
        $user = $this->security->getUser();

        // making sure that the user is connected
        if (!$user instanceof Member) {
            return;
        }

        $object = $event->getIssue();
        $object->setReporter($user);

        $event->setIssue($object);
    }

    #[AsEventListener(event: 'app.issue.pre_create', priority: 75)]
    public function openIssue(IssueCreatedEvent $event): void
    {
        $object = $event->getIssue();
        $this->workflow->apply(subject: $object, transitionName: 'open_issue');

        $event->setIssue($object);
    }

    #[AsEventListener(event: 'app.issue.assignee_defined', priority: 100)]
    public function signIssueUrl(AbstractEvent $event): void
    {
        $object = $event->getIssue();
        $signedUrl = $this->doSignIssueLink($object);
        $event->setUrl($signedUrl);
    }

    #[AsEventListener(event: 'app.issue.assignee_defined', priority: 75)]
    public function sendNotificationToAssignee(AbstractEvent $event): void
    {
        // here we need to check if the assignee is attached. if yes, then email the appropriate person
        $object = $event->getIssue();
        $assignee = $event->getAssignee();

        if (null !== $assignee) {
            $issueUrl = $this->doSignIssueLink($object);

            // send the Email
            $this->mailer->send(
                toAddress: $assignee->getEmail(),
                templateVariables: [
                    'issue_id' => $object->getId(),
                    'user_name' => $object->getAssignee()->getFullName(),
                    'issue_title' => $object->getTitle(),
                    'severity' => $object->getSeverity()->label(),
                    'url' => $issueUrl,
                ],
                contentTemplate: 'email/assigned_member.html.twig',
                subject: \sprintf('Issue #%d was assigned to you', $object->getId())
            );
        }
    }

    private function doSignIssueLink(Issue $issue): false|string
    {
        if (null === $issue->getId()) {
            return false;
        }

        $unsignedUrl = $this->urlGenerator->generate(
            referenceType: UrlGeneratorInterface::ABSOLUTE_URL,
            name: 'app.issues.show',
            parameters: ['id' => $issue->getId()],
        );

        return $this->signer->sign(uri: $unsignedUrl, expiration: new \DateTime('+1 day'));
    }
}
