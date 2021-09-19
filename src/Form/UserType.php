<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    private static $array =[
      'Male'=>0,
      'Female'=>1,
      'Other'=>2
    ];
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
               ->add('name',null,['label'=>'Lastname'])
               ->add('prename',null,['label'=>'Firstname'])
            ->add('oGusername',null,['label'=>'Username'])
            ->add('gender',ChoiceType::class, [
                'choices' => self::$array
                    ,
                'placeholder'=>"--Sex--"])
               ->add('email' ,EmailType::class,[

                   'constraints' => [
                       new Email([
                           'message' => 'valid email please .',
                       ]),
                   ],
               ])
            ->add('profile',ProfileType::class)

           ;

    }
    public function getName()
    {
        return 'user';
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
