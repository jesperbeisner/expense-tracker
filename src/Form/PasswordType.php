<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType as PasswordFormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class PasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password1', PasswordFormType::class, [
                'label' => 'form.password1',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'required' => true,
                'constraints' => [
                    new Assert\Length(
                        min: 10,
                        max: 100,
                        minMessage: 'password.min.length',
                        maxMessage: 'password.max.length'
                    ),
                ],
            ])
            ->add('password2', PasswordFormType::class, [
                'label' => 'form.password2',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'required' => true,
                'constraints' => [
                    new Assert\Length(
                        min: 10,
                        max: 100,
                        minMessage: 'password.min.length',
                        maxMessage: 'password.max.length'
                    ),
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Speichern',
                'attr' => ['class' => 'btn btn-primary'],
            ])
        ;
    }
}
