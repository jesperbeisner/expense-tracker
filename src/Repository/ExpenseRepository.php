<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Expense;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

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

    /**
     * @return array<array-key, array<string, int|string|null>>
     */
    public function getMonthlyExpenses(UserInterface $user, int $year, int $month): array
    {
        $startDate = new DateTime("$year-$month-01");

        $endDate = new DateTime("$year-$month-01");
        $endDate->modify('+1 month');

        /** @var array<array-key, array<string, int|string|null>> $result */
        $result = $this->createQueryBuilder('e')
            ->select('SUM(e.amount) AS amount, c.name AS category')
            ->leftJoin('e.category', 'c')
            ->where('e.user = :user')
            ->andWhere('e.created > :startDate')
            ->andWhere('e.created < :endDate')
            ->groupBy('c.name')
            ->setParameter('user', $user)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getArrayResult()
        ;

        return $result;
    }
}
