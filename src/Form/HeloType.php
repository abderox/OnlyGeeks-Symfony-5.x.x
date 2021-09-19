<?php

namespace App\Form;

use App\Entity\Helo;
use App\Entity\User;
use App\Repository\HeloRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Vich\UploaderBundle\Form\Type\VichImageType;

class HeloType extends AbstractType
{
    private static $title = [
       'names',
        'star',
        'interstellar_space'
    ];
 public function __construct(UserRepository $userrepo , EntityManagerInterface $em)
 {
     $this->userrepo=$userrepo;
     $this->em=$em;
 }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

             /** @var Helo|null $helo */
//        $helo = $options['data'] ?? null;
//        $service =  $helo->getService() ;
        $builder->add('service', ChoiceType::class, [

            'choices' =>
                array_combine(self::$title,self::$title),
            'placeholder'=>"--choose a service--",
            'label'=>' ',


        ])
            ->add('title',TextType::class ,['attr'=>['class'=>'form-control','autofocus'=>'true'],'help'=>'Please be specified when you choose a title !'])
            ->add('content',TextareaType::class ,['attr'=>['class'=>'form-control mt-3','autofocus'=>'true'],'required' => true,'label'=>'Description'])
            ->add('age', TelType::class , ['attr'=>['class'=>'form-control','autofocus'=>'true'],'label'=>'Lucky Number','help'=>'This number could change your life ? it could make you win !'])
//            ->add('submit',SubmitType::class,['label'=>'create'])
            ->add('imageFile', VichImageType::class, [
                'attr'=>['style'=>'max-width:400px'],
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'delete image',
                'download_uri' => false,
                'imagine_pattern' => 'my_thumb_filter',
                'label'=>'Poster',
                'help'=>'If you are writing about a book , a movie or any topic that needs visual clarification , it could be useful if you mention it !'

            ])
            ->add('tags',TagType::class);

//        ;
//        if ($service) {
//
//            $builder->add('noma', EntityType::class, [
//                'class' => User::class,
//                'query_builder' => function (UserRepository $er) {
//                    return $er->createQueryBuilder('u')
//                        ->orderBy('u.name', 'ASC');
//                },
//                'choice_label' => function(User $user) {
//                    return sprintf('(%d) %s', $user->getId(), $user->getPrename());
//                },
//                'placeholder'=>"choose car",
//                'required'=>false
//
//            ]);
//
//        }

//
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                /** @var Helo|null $data */
                $data = $event->getData()??'star';
                if (!$data) {
                    return;
                }
                $this->setupSpecificLocationNameField(
                    $event->getForm(),
                    $data->getService()??'star');
            }


        );
        $builder->get('service')->addEventListener(
            FormEvents::POST_SUBMIT,
            function(FormEvent $event) {
                $form = $event->getForm();

                $this->setupSpecificLocationNameField(
                    $form->getParent(),
                    $form->getData()
                );
            }
        );


//        else
//        {
//            dd('nope');
//        }

    }
    private function setupSpecificLocationNameField(FormInterface $form,?string $service)
    {

        if (null === $service) {
            $form->remove('noma');
            return;
        }

        $choices = $this->getLocationNameChoices($service);

        if (null === $choices) {

            $form->remove('noma');
            return;
        }

        $form->add('noma', ChoiceType::class, [
            'placeholder' => 'Luxury cars , Palace , Trips ? ',
            'choices' => $choices,
            'label'=>' ',
            'required' => false,
        ]);


//
//        $form->add('noma', EntityType::class, [
//                'class' => User::class,
//                'query_builder' => function (UserRepository $er) {
//                    return $er->createQueryBuilder('u')
//                        ->orderBy('u.name', 'ASC');
//                },
//                'choice_label' => function(User $user) {
//                    return sprintf('(%d) %s', $user->getId(), $user->getPrename());
//                },
//                'placeholder'=>"choose car",
//                'required'=>false
//
//            ]);





    }
    private function getLocationNameChoices(string $service)
    {
//        $repoNeighborhood = $this->em->getRepository('App:User');
//
//        $planets = $repoNeighborhood->createQueryBuilder("q")
//            ->select('q.name')
//            ->getQuery()
//            ->getResult();7
        $query = $this->em->createQuery('SELECT u.name FROM App\Entity\User u order by u.name ASC  ');
        $planets =   array_filter(array_column($query->getResult(),'name'),'strlen');


        $stars = [
            'Polaris',
            'Sirius',
            'Alpha Centauari A',
            'Alpha Centauari B',
            'Betelgeuse',
            'Rigel',
            'Other'
        ];
        $locationNameChoices = [
            'names' => array_combine($planets, $planets),
            'star' => array_combine($stars, $stars),
            'interstellar_space' => null,
        ];
        return $locationNameChoices[$service];
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Helo::class,
        ]);
    }

}
