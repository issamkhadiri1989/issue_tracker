<?php

declare(strict_types=1);

namespace App\Tests\Notification;

use App\Notification\Mailer;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;

#[AllowMockObjectsWithoutExpectations]
class MailerTest extends TestCase
{
    private object $innerMailMock;
    private object $logger;

    protected function setUp(): void
    {
        $this->innerMailMock = $this->createMock(MailerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    public function testSendEmailWithSuccess(): void
    {
        $this->innerMailMock->expects($this->once())
            ->method('send');
        $this->logger->expects($this->never())
            ->method('error');

        $mailerService = new Mailer(logger: $this->logger, innerMailer: $this->innerMailMock);

        $mailerService->send(
            toAddress: 'test@some-test.com',
            subject: 'this is a mailer',
            contentTemplate: 'mailer.html.twig',
        );
    }

    public function testSendEmailFailureDueToError(): void
    {
        $this->innerMailMock->method('send')
            ->willThrowException(new TransportException());

        $this->innerMailMock
            ->expects($this->once())
            ->method('send');

        $this->logger->expects($this->once())
            ->method('error');

        $mailerService = new Mailer(logger: $this->logger, innerMailer: $this->innerMailMock);

        $mailerService->send(
            toAddress: 'test@some-test.com',
            subject: 'this is a mailer',
            contentTemplate: 'mailer.html.twig',
        );
    }
}
