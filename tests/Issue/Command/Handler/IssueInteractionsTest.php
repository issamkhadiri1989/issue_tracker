<?php

declare(strict_types=1);

namespace App\Tests\Issue\Command\Handler;

use App\Entity\Issue;
use App\Enum\SeverityEnum;
use App\Issue\Command\CreateIssueCommand;
use App\Issue\Command\Handler\IssueCommandHandler;
use App\Issue\Command\RemoveIssueCommand;
use App\Issue\Command\UpdateIssueCommand;
use App\Repository\IssueRepository;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\Depends;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

#[AllowMockObjectsWithoutExpectations]
class IssueInteractionsTest extends KernelTestCase
{
    public function testCreateNewIssue(): Issue
    {
        self::bootKernel();

        /** @var IssueCommandHandler $issueCommandHandler */
        $issueCommandHandler = self::$kernel->getContainer()->get(IssueCommandHandler::class);

        /** @var CreateIssueCommand $command */
        $command = self::$kernel->getContainer()->get(CreateIssueCommand::class);

        $issue = new Issue()
            ->setTitle('test issue title')
            ->setDescription('test issue description')
            ->setSeverity(SeverityEnum::CRITICAL);

        $issueCommandHandler->handle(issue: $issue, command: $command);

        // check first that the issue has been created
        $this->assertNotNull($issue->getId());
        $this->assertInstanceOf(\DateTimeInterface::class, $issue->getCreatedAt());

        // the issue must be saved into the database.
        $issue = $this->getIssueInstance($issue->getId());
        $this->assertInstanceOf(Issue::class, $issue); // this makes sure that the instance is not null
        // check that the updated at is not defined
        $this->assertNull($issue->getUpdatedAt());

        return $issue; // this issue will be used in the following test.
    }

    #[Depends('testCreateNewIssue')]
    public function testEditExistingIssue(Issue $initialIssue): Issue
    {
        self::bootKernel();

        // get a fresh instance from the database. this will trigger the updating of the `updatedAt` field later.
        $issue = $this->getIssueInstance($initialIssue->getId());

        /** @var IssueCommandHandler $issueCommandHandler */
        $issueCommandHandler = self::$kernel->getContainer()->get(IssueCommandHandler::class);

        /** @var UpdateIssueCommand $command */
        $command = self::$kernel->getContainer()->get(UpdateIssueCommand::class);

        // Do some changes in the $issue instance
        $issue->setTitle('test issue title (Edited)')
            ->setDescription('test issue description With random text '.\time())
            ->setSeverity(SeverityEnum::HIGH);

        $isSuccess = $issueCommandHandler->handle(issue: $issue, command: $command);

        // make sure the update operation is OK
        $this->assertTrue($isSuccess);
        // if the update is ok the update date should be defined.
        $this->assertNotNull($issue->getUpdatedAt());

        // make sure that all the edited fields are truly set in the database.
        $updatedIssue = $this->getIssueInstance($issue->getId());
        $this->assertEquals($updatedIssue, $issue); // make sure that the updated issue instance and the one getting from the database are the same

        return $updatedIssue;
    }

    #[Depends('testEditExistingIssue')]
    public function testDeleteExistingIssue(Issue $initialIssue): void
    {
        self::bootKernel();

        // get a fresh instance from the database. this will trigger the updating of the `updatedAt` field later.
        $issue = $this->getIssueInstance($initialIssue->getId());

        /** @var IssueCommandHandler $issueCommandHandler */
        $issueCommandHandler = self::$kernel->getContainer()->get(IssueCommandHandler::class);

        /** @var RemoveIssueCommand $command */
        $command = self::$kernel->getContainer()->get(RemoveIssueCommand::class);

        $identifier = $initialIssue->getId();

        $issueCommandHandler->handle(issue: $issue, command: $command);

        $notExistingIssue = $this->getIssueInstance($identifier);
        $this->assertNull($notExistingIssue);
    }

    private function getIssueInstance(int $identifier): ?Issue
    {
        /** @var IssueRepository $repository */
        $repository = self::$kernel->getContainer()->get(IssueRepository::class);

        return $repository->find($identifier);
    }
}
