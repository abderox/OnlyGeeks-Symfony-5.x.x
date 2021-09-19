<?php

namespace App\Form;

use App\Entity\Helo;
use App\Entity\User;
use App\Repository\HeloRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchTyp extends AbstractType
{

    private static $title = [
        'User',
        'batona',
        'isslama',
        'balata',
    ];
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Helo|null $helo */
        $helo = $options['data'] ?? null;
        $service = $helo ? $helo->getService() : null;
        $builder->add('service', ChoiceType::class, [

                'choices' =>
                array_combine(self::$title,self::$title),
                'placeholder'=>"choose a service ..."

            ]);

 if ($service) {
     $builder->add('nom', EntityType::class, [
         'class' => User::class,
         'query_builder' => function (UserRepository $er) {
             return $er->createQueryBuilder('u')
                 ->orderBy('u.name', 'ASC');
         },
         'choice_label' => function(User $user) {
             return sprintf('(%d) %s', $user->getId(), $user->getPrename());
         },
         'placeholder'=>"choose car",
         'required'=>false

     ]);

 }
 elseif ($service==="Helo")
 {
     dd('hello');
 }

    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Helo::class,
        ]);
    }
}
