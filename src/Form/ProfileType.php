<?php

namespace App\Form;

use App\Entity\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints;

class ProfileType extends AbstractType
{
    private static $title = [
        'Single',
        'in a Relationship',
        'Married',
        'Divorced just kidding',
        'Other'
    ];
    private static $private_info = [
        'everyone'=>0,
        'only me'=>1,
        'only fans'=>2
    ];
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imageFile', VichImageType::class, [
                'attr'=>['style'=>'max-width:100px'],
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'delete image',
                'download_uri' => false,
                'imagine_pattern' => 'my_profile_filter',
                'label'=>'Avatar',



            ])
            ->add('birthday', DateType::class, [
                'widget' => 'single_text',
                'label'=>'Date of birth',
                'attr' => ['class' => 'js-datepicker'],
                'constraints' => [
                    new Constraints\Callback(function($object, ExecutionContextInterface $context) {
                        $start = new \DateTime('now');
                        $stop = $object;


                            if ($start->format('Y') - $stop->format('Y') <14 ) {

                                $context
                                    ->buildViolation('Not a valid date of birth ')
                                    ->addViolation();
                            }

                    }),
                    ]

            ])

            ->add('about',TextareaType::class,['help'=>'max letters 100','attr'=>['rows'=>7],

            ])
            ->add('relationship',ChoiceType::class, [

        'choices' =>
            array_combine(self::$title,self::$title),
        'placeholder'=>"--Family state--"])
            ->add('privateInfo',ChoiceType::class, ['label'=>'Personal info privacy','help'=>' Select who could see your personal info such as email ...',

                'choices' =>
                    self::$private_info,
                'placeholder'=>"--Privacy--"])

        ;
    }
    public function getName()
    {
        return 'profile';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Profile::class,
        ]);
    }
}
