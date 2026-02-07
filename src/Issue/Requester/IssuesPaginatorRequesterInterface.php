<?php

declare(strict_types=1);

namespace App\Issue\Requester;

use App\Issue\Query\Collection\GetPaginatedRecords;
use Knp\Component\Pager\Pagination\PaginationInterface;

interface IssuesPaginatorRequesterInterface
{
    public function getRecords(GetPaginatedRecords $query): PaginationInterface;
}
