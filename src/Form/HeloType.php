<?php

namespace App\Form;

use App\Entity\Helo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HeloType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class ,['attr'=>['class'=>'form-control','autofocus'=>'true']])
            ->add('age', TelType::class , ['attr'=>['class'=>'form-control','autofocus'=>'true']])
//            ->add('submit',SubmitType::class,['label'=>'create'])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Helo::class,
        ]);
    }
}