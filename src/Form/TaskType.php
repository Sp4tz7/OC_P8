<?php

namespace App\Form;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Saisir un nom de tâche',
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Description',
            ])
            ->add('assigned_to', EntityType::class, [
                'class' => User::class,
                'label' => 'Assigner la tâche à'
            ]);
    }
}
