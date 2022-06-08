<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Expense;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Expense>
 *
 * @method Expense|null find($id, $lockMode = null, $lockVersion = null)
 * @method Expense|null findOneBy(array $criteria, array $orderBy = null)
 * @method Expense[]    findAll()
 * @method Expense[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expense::class);
    }

    /**
     * @return Expense[]
     */
    public function findAllByUserAndOrderedByDueDate(User $user): array
    {
        return $this->findBy(['user' => $user], ['dueDate' => 'DESC']);
    }

    public function getAmountSumByUser(User $user): int
    {
        /** @var int|string|null $result */
        $result = $this->createQueryBuilder('e')
            ->select('SUM(e.amount)')
            ->where('e.user = :user')
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
