<?php

namespace App\Form;

use App\Config\UserStatusConfig;
use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskFormType extends AbstractType
{
    /** @var UserStatusConfig */
    private $userStatusConfig;

    public function __construct(UserStatusConfig $userStatusConfig)
    {
        $this->userStatusConfig = $userStatusConfig;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $statusList = $this->userStatusConfig->getStatusTitles();
        $builder
            ->add('title')
            ->add('link', TextType::class, [
                'required' => false
            ])
            ->add('reminder', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('status', ChoiceType::class, [
                'choices' => array_flip($statusList)
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class
        ]);
    }
}
