<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Entity\User;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;

class CategoryController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CategoryRepository $categoryRepository,
    ) {}

    #[Route('/categories', name: 'app_categories', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(CategoryType::class, $category = new Category());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category->setUser($user);

            $this->entityManager->persist($category);

            try {
                $this->entityManager->flush();
                $this->addFlash('success', new TranslatableMessage("controller.category.added.successfully", ['%category%' => $category->getName()]));
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('error', new TranslatableMessage("controller.category.already.exists", ['%category%' => $category->getName()]));
            }

            return $this->redirectToRoute('app_categories');
        }

        return $this->renderForm('category/index.html.twig', [
            'form' => $form,
            'categories' => $this->categoryRepository->findAllByUserAndOrderedByName($user),
        ]);
    }

    #[Route('/categories/{id}/edit', name: 'app_categories_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();

        if (null === $category = $this->categoryRepository->findOneBy(['id' => $id, 'user' => $user])) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($category);
            $this->entityManager->flush();

            $this->addFlash('success', new TranslatableMessage('controller.expense.edited.successfully'));

            return $this->redirectToRoute('app_categories');
        }

        return $this->renderForm('category/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/categories/{id}/delete', name: 'app_categories_delete', methods: ['POST'])]
    public function delete(int $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();

        if (null === $category = $this->categoryRepository->findOneBy(['id' => $id, 'user' => $user])) {
            $this->addFlash('error', new TranslatableMessage('controller.category.not.found'));

            return $this->redirectToRoute('app_categories');
        }

        $this->entityManager->remove($category);
        $this->entityManager->flush();

        $this->addFlash('success', new TranslatableMessage('controller.category.deleted.successfully', ['%category%' => $category->getName()]));

        return $this->redirectToRoute('app_categories');
    }
}
