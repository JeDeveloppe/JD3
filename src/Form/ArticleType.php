<?php

namespace App\Form;

use App\Entity\Article;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('search', TextType::class, [
                'attr' => [
                    'placeholder' => 'Rechercher...',
                    'class' => 'form-control border-2 rounded-2 custom_shadow'
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 3]),
                ],
                'invalid_message' => 'Minimum %num% caractères.',
                'invalid_message_parameters' => ['%num%' => 3],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
