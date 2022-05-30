<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ExpenseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExpenseController extends AbstractController
{
    #[Route('/expenses', name: 'app_expenses', methods: ['GET'])]
    public function expenses(ExpenseRepository $expenseRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('expense/expenses.html.twig', [
            'expenses' => $expenseRepository->findAllOrderedByDueDate(),
        ]);
    }
}
