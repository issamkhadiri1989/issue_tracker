<?php

declare(strict_types=1);

namespace App\Tests\Issue\Command\Handler;

use App\Entity\Issue;
use App\Issue\Command\CreateIssueCommand;
use App\Issue\Command\Handler\IssueCommandHandler;
use App\Issue\Command\RemoveIssueCommand;
use App\Issue\Command\UpdateIssueCommand;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AllowMockObjectsWithoutExpectations]
class IssueCommandHandlerTest extends KernelTestCase
{
    private object $logger;
    private object $entityManager;
    private object $dispatcher;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);
    }

    /**
     * @return \Generator for better performance
     */
    public static function getIssues(): \Generator
    {
        yield [new Issue()];
    }

    #[DataProvider(methodName: 'getIssues')]
    public function testCreateNewIssueCommand(Issue $issue): void
    {
        self::bootKernel();

        $command = new CreateIssueCommand(
            logger: $this->logger,
            eventDispatcher: $this->dispatcher,
            entityManager: $this->entityManager,
        );

        /** @var IssueCommandHandler $handler */
        $handler = self::getContainer()->get(IssueCommandHandler::class);

        $this->dispatcher->expects($this->exactly(2))->method('dispatch');
        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $isSuccess = $handler->handle(command: $command, issue: $issue);

        $this->assertTrue($isSuccess);
    }

    #[DataProvider(methodName: 'getIssues')]
    public function testCreateNewIssueCommandWithFailure(Issue $issue): void
    {
        $this->entityManager
            ->expects($this->once())
            ->method('flush')
            ->willThrowException(new \Exception());
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatch');
        $this->logger
            ->expects($this->exactly(2))
            ->method('error');

        $command = new CreateIssueCommand(
            logger: $this->logger,
            eventDispatcher: $this->dispatcher,
            entityManager: $this->entityManager,
        );

        /** @var IssueCommandHandler $handler */
        $handler = self::getContainer()->get(IssueCommandHandler::class);

        $isSuccess = $handler->handle(command: $command, issue: $issue);

        $this->assertFalse($isSuccess);
    }

    #[DataProvider(methodName: 'getIssues')]
    public function testUpdateIssueCommand(Issue $issue): void
    {
        self::bootKernel();

        $command = new UpdateIssueCommand(
            logger: $this->logger,
            eventDispatcher: $this->dispatcher,
            entityManager: $this->entityManager,
        );

        /** @var IssueCommandHandler $handler */
        $handler = self::getContainer()->get(IssueCommandHandler::class);

        $this->dispatcher->expects($this->exactly(2))->method('dispatch');
        $this->entityManager->expects($this->never())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $isSuccess = $handler->handle(command: $command, issue: $issue);

        $this->assertTrue($isSuccess);
    }

    #[DataProvider(methodName: 'getIssues')]
    public function testUpdateExistingIssueCommandWithFailure(Issue $issue): void
    {
        $this->entityManager
            ->expects($this->once())
            ->method('flush')
            ->willThrowException(new \Exception());
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatch');
        $this->logger
            ->expects($this->exactly(2))
            ->method('error');

        $command = new UpdateIssueCommand(
            logger: $this->logger,
            eventDispatcher: $this->dispatcher,
            entityManager: $this->entityManager,
        );

        /** @var IssueCommandHandler $handler */
        $handler = self::getContainer()->get(IssueCommandHandler::class);

        $isSuccess = $handler->handle(command: $command, issue: $issue);

        $this->assertFalse($isSuccess);
    }

    #[DataProvider(methodName: 'getIssues')]
    public function testRemoveExistingIssueCommand(Issue $issue): void
    {
        self::bootKernel();

        $command = new RemoveIssueCommand(
            logger: $this->logger,
            eventDispatcher: $this->dispatcher,
            entityManager: $this->entityManager,
        );

        /** @var IssueCommandHandler $handler */
        $handler = self::getContainer()->get(IssueCommandHandler::class);

        $this->dispatcher
            ->expects($this->exactly(2))
            ->method('dispatch');
        $this->entityManager
            ->expects($this->never())
            ->method('persist');
        $this->entityManager
            ->expects($this->once())
            ->method('remove');
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $isSuccess = $handler->handle(command: $command, issue: $issue);

        $this->assertTrue($isSuccess);
    }

    #[DataProvider(methodName: 'getIssues')]
    public function testRemoveExistingIssueCommandWithFailure(Issue $issue): void
    {
        $this->entityManager
            ->expects($this->once())
            ->method('flush')
            ->willThrowException(new \Exception());
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatch');
        $this->logger
            ->expects($this->exactly(2))
            ->method('error');

        $command = new RemoveIssueCommand(
            logger: $this->logger,
            eventDispatcher: $this->dispatcher,
            entityManager: $this->entityManager,
        );

        /** @var IssueCommandHandler $handler */
        $handler = self::getContainer()->get(IssueCommandHandler::class);

        $isSuccess = $handler->handle(command: $command, issue: $issue);

        $this->assertFalse($isSuccess);
    }
}
