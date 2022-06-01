<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\FixedCost;
use App\Entity\User;
use App\Form\FixedCostType;
use App\Repository\FixedCostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;

class FixedCostController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FixedCostRepository $fixedCostRepository,
    ) {}

    #[Route('/fixed-costs', name: 'app_fixed_costs', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(FixedCostType::class, $fixedCost = new FixedCost());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $fixedCost->setUser($user);

            $this->entityManager->persist($fixedCost);
            $this->entityManager->flush();

            $this->addFlash('success', new TranslatableMessage('controller.fixed_cost.added.successfully'));

            return $this->redirectToRoute('app_fixed_costs');
        }

        return $this->renderForm('fixed-cost/index.html.twig', [
            'form' => $form,
            'fixedCosts' => $this->fixedCostRepository->findAllByUserAndOrderedByAmount($user),
            'amount' => $this->fixedCostRepository->getAmountSumByUser($user),
        ]);
    }

    #[Route('/fixed-costs/{id}/edit', name: 'app_fixed_costs_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();

        if (null === $fixedCost = $this->fixedCostRepository->findOneBy(['id' => $id, 'user' => $user])) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(FixedCostType::class, $fixedCost);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($fixedCost);
            $this->entityManager->flush();

            $this->addFlash('success', new TranslatableMessage('controller.fixed_cost.edited.successfully'));

            return $this->redirectToRoute('app_fixed_costs');
        }

        return $this->renderForm('fixed-cost/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/fixed-costs/{id}/delete', name: 'app_fixed_costs_delete', methods: ['POST'])]
    public function delete(int $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();

        if (null === $fixedCost = $this->fixedCostRepository->findOneBy(['id' => $id, 'user' => $user])) {
            $this->addFlash('error', new TranslatableMessage('controller.fixed_cost.not.found'));

            return $this->redirectToRoute('app_fixed_costs');
        }

        $this->entityManager->remove($fixedCost);
        $this->entityManager->flush();

        $this->addFlash('success', new TranslatableMessage('controller.fixed_cost.deleted.successfully'));

        return $this->redirectToRoute('app_fixed_costs');
    }
}
