<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\PasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;

class SettingsController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    #[Route(path: '/settings', name: 'app_settings', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(PasswordType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var array<string, string> $data */
            $data = $form->getData();

            if ($data['password1'] !== $data['password2']) {
                $this->addFlash('error', new TranslatableMessage('Die beiden Passwörter sind nicht identisch'));

                return $this->redirectToRoute('app_settings');
            }

            $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password1']);
            $user->setPassword($hashedPassword);

            $this->entityManager->flush();

            return $this->redirectToRoute('app_logout');
        }

        return $this->renderForm('settings/index.html.twig', [
            'form' => $form,
        ]);
    }
}
