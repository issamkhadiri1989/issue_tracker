<?php

declare(strict_types=1);

namespace App\Issue\Requester;

use App\Entity\Issue;
use App\Issue\Query\Collection\GetPaginatedRecords;
use App\Repository\IssueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

final class DefaultIssuesRequester implements IssuesPaginatorRequesterInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PaginatorInterface $internalPafinator,
    ) {
    }

    public function getRecords(GetPaginatedRecords $query): PaginationInterface
    {
        /** @var IssueRepository $repository */
        $repository = $this->entityManager->getRepository(Issue::class);

        $pagination = $query->getPagination();

        return $this->internalPafinator
            ->paginate(
                target: $repository->getIssues(),
                limit: $pagination->limit,
                page: $pagination->page,
            );
    }
}
