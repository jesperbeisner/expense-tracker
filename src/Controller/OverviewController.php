<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\ExpenseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OverviewController extends AbstractController
{
    #[Route('/overview', name: 'app_overview', methods: ['GET'])]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('overview/index.html.twig');
    }

    #[Route('/api/expense-chart-data', name: 'app_expense_chart_data', methods: ['POST'])]
    public function expenseChartData(Request $request, ExpenseRepository $expenseRepository): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();

        try {
            $requestData = $request->toArray();
        } catch (JsonException $e) {
            return new JsonResponse([
                'status' => 'Failure',
                'message' => 'No valid request body'
            ], 400);
        }

        $month = $requestData['month'] ?? null;
        $year = $requestData['year'] ?? null;

        if ($month === null || $year === null) {
            return new JsonResponse([
                'status' => 'Failure',
                'message' => 'No valid request body'
            ], 400);
        }

        $expenses = $expenseRepository->getMonthlyExpenses($user, (int) $year, (int) $month);

        $data = ['categories' => [], 'amounts' => [], 'total' => 0];
        foreach ($expenses as $expense) {
            $data['categories'][] = $expense['category'] ?? 'Unbekannt';
            $data['amounts'][] = round((int) $expense['amount'] / 100, 2);
            $data['total'] += (int) $expense['amount'];
        }

        if ($data['total'] !== 0) {
            $data['total'] = round($data['total'] / 100, 2);
        }

        return $this->json($data);
    }
}
