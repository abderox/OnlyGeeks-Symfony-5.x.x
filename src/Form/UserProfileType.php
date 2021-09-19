<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user',UserType::class)
            ->add('profile',ProfileType::class)
        ;
    }
    public function getName()
    {
        return 'userprofile';
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            ['data_class' => null]
        ]);
    }
}
