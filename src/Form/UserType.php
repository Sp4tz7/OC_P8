<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastname', TextType::class, ['label' => "Nom"])
            ->add('firstname', TextType::class, ['label' => "Prénom"])
            ->add('mobile_number', TextType::class, ['label' => "Numéro de portable"])
            ->add('date_of_birth', DateType::class, ['label' => "Date de naissance",'years' => range(date('Y')-65, date('Y')),])
            ->add('username', TextType::class, ['label' => "Nom d'utilisateur"])
            ->add('password', RepeatedType::class, [
                'type'            => PasswordType::class,
                'invalid_message' => 'Les deux mots de passe doivent correspondre.',
                'required'        => false,
                'first_options'   => ['label' => 'Mot de passe'],
                'second_options'  => ['label' => 'Tapez le mot de passe à nouveau'],
            ])
            ->add('email', EmailType::class, ['label' => 'Adresse email'])
            ->add('occupation', TextType::class, ['label' => "Poste"])
            ->add(
                'roles',
                ChoiceType::class,
                [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices'  => [
                        'Admin' => 'ROLE_ADMIN',
                        'User'  => 'ROLE_USER',
                    ],
                    'mapped'   => true,
                    'label'    => 'Privilèges',
                ]
            )->add('avatar', FileType::class, [
                'label' => 'Image',
                'multiple' => false,
                'mapped' => false,
                'required' => false, 'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Only jpeg or png format are allowed.',
                    ]),
                ],
            ]);

        // Data transformer
        $builder->get('roles')
            ->addModelTransformer(
                new CallbackTransformer(
                    function ($rolesArray) {
                        // transform the array to a string
                        return is_array($rolesArray) ? $rolesArray[0] : 'null';
                    },
                    function ($rolesString) {
                        // transform the string back to an array
                        return [$rolesString];
                    }
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
            ]
        );
    }
}
