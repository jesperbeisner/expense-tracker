<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/categories', name: 'app_categories', methods: ['GET', 'POST'])]
    public function categories(Request $request, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(CategoryType::class, new Category())->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Category $data */
            $data = $form->getData();

            $entityManager->persist($data);

            try {
                $entityManager->flush();
                $this->addFlash('success', "The category '{$data->getName()}' was added successfully");
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('error', "A category with the name '{$data->getName()}' already exists");
            }

            return $this->redirectToRoute('app_categories');
        }

        return $this->renderForm('category/categories.html.twig', [
            'form' => $form,
            'categories' => $categoryRepository->findAllOrderedByName(),
        ]);
    }
}
