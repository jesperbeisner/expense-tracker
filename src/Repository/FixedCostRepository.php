<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\FixedCost;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FixedCost>
 *
 * @method FixedCost|null find($id, $lockMode = null, $lockVersion = null)
 * @method FixedCost|null findOneBy(array $criteria, array $orderBy = null)
 * @method FixedCost[]    findAll()
 * @method FixedCost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FixedCostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FixedCost::class);
    }

    public function findAllByUserAndOrderedByAmount(User $user): array
    {
        return $this->findBy(['user' => $user], ['amount' => 'DESC']);
    }

    public function getAmountSumByUser(User $user): int
    {
        $result = $this->createQueryBuilder('fc')
            ->select('SUM(fc.amount)')
            ->where('fc.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        if ($result === null) {
            return 0;
        }

        return (int) $result;
    }
}
