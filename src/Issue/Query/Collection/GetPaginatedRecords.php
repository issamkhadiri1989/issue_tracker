<?php

declare(strict_types=1);

namespace App\Issue\Query\Collection;

use App\DTO\PaginationRequest;

class GetPaginatedRecords
{
    public function __construct(private PaginationRequest $pagination)
    {
    }

    public function getPagination(): PaginationRequest
    {
        return $this->pagination;
    }
}
