<?php

declare(strict_types=1);

namespace App\Tests\Issue\Requester;

use App\DTO\PaginationRequest;
use App\Issue\Query\Collection\GetPaginatedRecords;
use App\Issue\Requester\DefaultIssuesRequester;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DefaultIssuesRequesterTest extends KernelTestCase
{
    public function testSomething(): void
    {
        self::bootKernel();

        /** @var DefaultIssuesRequester $requester */
        $requester = self::$kernel->getContainer()->get(DefaultIssuesRequester::class);

        $records = $requester->getRecords(new GetPaginatedRecords($pagination = new PaginationRequest()));

        $this->assertInstanceOf(expected: PaginationInterface::class, actual: $records);
        // this to make sure that the result can be traversable.
        $this->assertIsIterable($records);

        // items should not exceed tha pagination limit (default to 10 here)
        $this->assertLessThanOrEqual(maximum: $pagination->limit, actual: $records->count());
    }
}
