<?php

declare(strict_types=1);

namespace App\Notification;

use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class Mailer
{
    public function __construct(private LoggerInterface $logger, private MailerInterface $innerMailer)
    {
    }

    public function send(string $toAddress, string $subject, string $contentTemplate, array $templateVariables = []): void
    {
        $message = new TemplatedEmail();

        $message->to($toAddress)
            ->subject($subject)
            ->htmlTemplate($contentTemplate)
            ->context($templateVariables);

        try {
            $this->innerMailer->send($message);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
