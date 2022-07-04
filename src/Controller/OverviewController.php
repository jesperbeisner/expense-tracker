<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\ExpenseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OverviewController extends AbstractController
{
    #[Route('/overview/{year}/{month}', name: 'app_overview', methods: ['GET'])]
    public function index(int $year, int $month, ExpenseRepository $expenseRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();

        $expenses = $expenseRepository->getMonthlyExpenses($user, $year, $month);

        $data = ['total' => 0];
        foreach ($expenses as $expense) {
            $data['categories'][] = $expense['category'] ?? 'Unknown';
            $data['amounts'][] = number_format((int) $expense['amount'] / 100, 2);
            $data['total'] += $expense['amount'];

            $r = rand(0, 255);
            $g = rand(0, 255);
            $b = rand(0, 255);

            $data['background_colors'][] = "rgba($r, $g, $b, 0.2)";
            $data['border_colors'][] = "rgba($r, $g, $b, 1)";
        }

        if ($data['total'] > 0) {
            $monthText = $month === 6 ? 'Juni' : 'Juli';
            $data['total'] = "Ausgaben für $monthText 2022: " . number_format((int) $data['total'] / 100, 2, ',', '.') . ' €';;
        }

        return $this->renderForm('overview/index.html.twig', [
            'data' => $data,
        ]);
    }
}
