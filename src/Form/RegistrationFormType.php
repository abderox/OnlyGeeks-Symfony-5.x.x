<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',null,['label'=>'Last name',

                'constraints' => [
                    new NotBlank([
                        'message' => 'First name required',
                    ]),

                ]])
            ->add('prename',null,['label'=>'Firstname',

                'constraints' => [
                    new NotBlank([
                        'message' => 'First name required',
                    ]),

                ]])
            ->add('oGusername',null,['label'=>'Username',

                'constraints' => [
                    new NotBlank([
                        'message' => 'Username required',
                    ]),

            ]])
            ->add('email' ,EmailType::class,[

                'constraints' => [
                    new NotBlank([
                        'message' => 'Blank field.',
                    ]),
                    new Email([
                        'message' => 'valid email please .',
                    ]),
                ],
            ])

            ->add('agreeTerms', CheckboxType::class, ['label'=>'Agree terms ',
                'mapped' => false,  
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms if you want to proceed .',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label'=>'Password',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
