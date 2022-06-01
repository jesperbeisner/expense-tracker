<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\ExpenseType;
use App\Repository\ExpenseRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;

class ExpenseController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ExpenseRepository $expenseRepository,
    ) {}

    #[Route('/expenses', name: 'app_expenses', methods: ['GET'])]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();

        return $this->render('expense/index.html.twig', [
            'expenses' => $this->expenseRepository->findAllByUserAndOrderedByDueDate($user),
            'amount' => $this->expenseRepository->getAmountSumByUser($user),
        ]);
    }

    #[Route('/expenses/{id}/edit', name: 'app_expenses_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();

        if (null === $expense = $this->expenseRepository->findOneBy(['id' => $id, 'user' => $user])) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(ExpenseType::class, $expense);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $expense->updateDueDateTime();
            $expense->setUpdated(new DateTime());

            $this->entityManager->persist($expense);
            $this->entityManager->flush();

            $this->addFlash('success', new TranslatableMessage('controller.expense.edited.successfully'));

            return $this->redirectToRoute('app_expenses');
        }

        return $this->renderForm('expense/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/expenses/{id}/delete', name: 'app_expenses_delete', methods: ['POST'])]
    public function delete(int $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if (null === $expense = $this->expenseRepository->find($id)) {
            $this->addFlash('error', new TranslatableMessage('controller.expense.not.found'));

            return $this->redirectToRoute('app_expenses');
        }

        $this->entityManager->remove($expense);
        $this->entityManager->flush();

        $this->addFlash('success', new TranslatableMessage('controller.expense.deleted.successfully'));

        return $this->redirectToRoute('app_expenses');
    }
}
