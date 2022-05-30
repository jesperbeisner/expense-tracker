<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Expense;
use App\Entity\User;
use App\Form\ExpenseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(ExpenseType::class, new Expense());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();

            /** @var Expense $data */
            $data = $form->getData();
            $data->setUser($user);

            $this->updateDueDateTime($data);

            $entityManager->persist($data);
            $entityManager->flush();

            $this->addFlash('success', 'The expense was added successfully');

            return $this->redirectToRoute('app_index');
        }

        return $this->renderForm('index/index.html.twig', [
            'form' => $form,
        ]);
    }

    // Fast workaround
    public function updateDueDateTime(Expense $expense): void
    {
        $dueDate = $expense->getDueDate();
        $created = $expense->getCreated();

        $hour = (int) $created->format('H');
        $minute = (int) $created->format('i');
        $second = (int) $created->format('s');

        $dueDate->setTime($hour, $minute, $second);

        $expense->setDueDate($dueDate);
    }
}
