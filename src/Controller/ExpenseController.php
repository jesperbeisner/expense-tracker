<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ExpenseType;
use App\Repository\ExpenseRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExpenseController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ExpenseRepository $expenseRepository,
    ) {}

    #[Route('/expenses', name: 'app_expenses', methods: ['GET'])]
    public function expenses(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('expense/expenses.html.twig', [
            'expenses' => $this->expenseRepository->findAllOrderedByDueDate(),
        ]);
    }

    #[Route('/expenses/{id}/edit', name: 'app_expenses_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (null === $expense = $this->expenseRepository->find($id)) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(ExpenseType::class, $expense);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $expense->updateDueDateTime();
            $expense->setUpdated(new DateTime());

            $this->entityManager->persist($expense);
            $this->entityManager->flush();

            $this->addFlash('success', 'The expense was edited successfully');

            return $this->redirectToRoute('app_expenses');
        }

        return $this->renderForm('expense/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/expenses/{id}/delete', name: 'app_expenses_delete', methods: ['POST'])]
    public function delete(int $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (null === $expense = $this->expenseRepository->find($id)) {
            $this->addFlash('error', 'The expense was not found');

            return $this->redirectToRoute('app_expenses');
        }

        $this->entityManager->remove($expense);
        $this->entityManager->flush();

        $this->addFlash('success', 'The expense was deleted successfully');

        return $this->redirectToRoute('app_expenses');
    }
}
