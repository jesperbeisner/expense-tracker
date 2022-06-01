<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Category;
use App\Entity\FixedCost;
use App\Entity\User;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class FixedCostType extends AbstractType
{
    public function __construct(
        private readonly Security $security,
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $builder
            ->add('amount', MoneyType::class, [
                'label' => 'form.amount',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'required' => true,
                'currency' => false,
                'divisor' => 100,
            ])
            ->add('note', TextareaType::class, [
                'label' => 'form.note',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'required' => false,
            ])
            ->add('category', EntityType::class, [
                'label' => 'form.categories',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'required' => false,
                'class' => Category::class,
                'query_builder' => function (CategoryRepository $repository) use ($user) {
                    return $repository->createQueryBuilder('c')
                        ->where('c.user = :user')
                        ->orderBy('c.name', 'ASC')
                        ->setParameter('user', $user)
                    ;
                },
                'choice_label' => 'name',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'form.save',
                'attr' => ['class' => 'btn btn-primary'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FixedCost::class,
        ]);
    }
}
