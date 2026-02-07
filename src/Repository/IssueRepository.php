<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Issue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

class IssueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Issue::class);
    }

    /**
     * I used this in order to control  the number of queries to be executed.
     */
    public function getIssues(): Query
    {
        $queryBuilder = $this->createQueryBuilder('i');

        return $queryBuilder
            ->select('i, c')
            ->orderBy('i.severity', 'DESC')
            ->join('i.category', 'c')
            ->getQuery();
    }
}
