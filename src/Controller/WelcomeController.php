<?php

namespace App\Controller;
use App\Entity\Helo;
use App\Repository\HeloRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
class WelcomeController extends AbstractController
{

    /**
     * @Route("/questions/show/{nam[a-z]}",priority="0",name="app_question_show")
     */
    public function show($nam)
    {
        $answers = [
          'make money not friends',
          'hello from agadir ',
          'what are you doing',

        ];

        return $this->render('question/show.html.twig', ['question'=>ucwords(str_replace('_',' ',$nam)),'answers'=>$answers]);
        // Annotations are simple to read

    }

    /**
     * @Route("/eee", name="app_homepage")
     */
    public function homepage()
    {
        return $this->render('question/homepage.html.twig');
    }

    /**
     * @Route("/comments/{id}/vote/{direction}")
     */
    public function commentvote($id, $direction){
        //js api endpoint
        // id to query database
        if($direction ==='up')
        {
            $currentvote=rand(7,100);}
            else{
                $currentvote=rand(0,5);
        }
        return new JsonResponse(['votes'=>$currentvote]);
            // return $this->>json();
    }

    /**
     * @Route("/questions/create" , methods={"POST","get"} , name="app_create")
     */
    public function create(Request $request, EntityManagerInterface $em)
    {
       /* if($request->isMethod('post'))
        {
            $data=$request->request->all();
            if($this->isCsrfTokenValid('toid',$data['_token'])) {
                $helo = new Helo();
                $helo->setTitle($data['title']);
                $helo->setAge($data['age']);
                $em->persist($helo);
                $em->flush();
            }
            return $this->redirectToRoute('app_show');



        }*/
        $helo = new Helo();
         $form = $this->createFormBuilder($helo)
             ->add('title',TextType::class ,['attr'=>['class'=>'form-control','autofocus'=>'true'],'data'=>'abdel'])
             ->add('age', NumberType::class , ['attr'=>['class'=>'form-control','autofocus'=>'true'],'data'=>50 ,'help' => 'The ZIP/Postal code for your credit card\'s billing address.'])
             ->add('submit',SubmitType::class,['label'=>'create'])
             ->getForm()
             ;
        $form->handleRequest($request); //remplace $request->request->all()
        if($form->isSubmitted() && $form->isValid())
        {
            //dump($form->getData());
            $data=$form->getData(); // $form->get('title')->getDta(); //$form['']->getDtat();
            $em->persist($helo);
            $em->flush();
            return $this->redirectToRoute('app_show_pin',['id'=>$helo->getId()]);
        }

        return $this->render('question/create.html.twig', ['maform'=>$form->createView()]);
        //

    }

    /**
     * @Route("/",name="app_show")
     */
    public function showart(HeloRepository $repo)
    {
       // return $this->redirect('https://www.google.com/');
        return $this->render('question/showart.html.twig', ['helos' => $repo->findBy([],['updatedAt'=>'DESC'])]);

    }


    /**
     * @Route("/questions/shows/{id<\d+>}",priority="10",name="app_show_pin")
     */
    public function showa(HeloRepository $repo , $id)
    {
        $helos = $repo->find($id);
        if($helos)
        {
            return $this->render('question/showpin.html.twig', ['helos' => $helos]);
        }
       else
       {
          // $error= ['message'=>"cet élément avec ce numero : $id  n'existe pas ", 'number'=>404];
           $yoyo= $this->createNotFoundException("cet élément avec ce numero : $id  n'existe pas ");
           //dd($yoyo);
           return $this->render('question/error.html.twig', ['error' => $yoyo]);
       }

    }

    /**
     * @Route("/questions/errors" , name="app_error")
     */
    public function error()
    {
        return $this->render('error.html.twig');
    }
    /**
     * @Route("/questions/shows/{id<\d+>}/edit" , name="app_edit" , methods={"GET","POST"})
     */
    public function edit(HeloRepository $helos ,$id ,Request $request ,EntityManagerInterface $em) : Response
    {

        $helo = $helos->find($id);


        $form = $this->createFormBuilder($helo)
            ->add('title',TextType::class ,['attr'=>['class'=>'form-control','autofocus'=>'true']])
            ->add('age', NumberType::class , ['attr'=>['class'=>'form-control','autofocus'=>'true'],'help' => 'You need help , email me .'])
            ->add('submit',SubmitType::class,['label'=>'update'])
            ->getForm()
        ;
        $form->handleRequest($request); //remplace $request->request->all()
        if($form->isSubmitted() && $form->isValid())
        {
            //dump($form->getData());
            $data=$form->getData(); // $form->get('title')->getDta(); //$form['']->getDtat();
            $em->persist($helo);
            $em->flush();
            return $this->redirectToRoute('app_show_pin',['id'=>$helo->getId()]);
        }

        return $this->render('question/edit.html.twig', ['helo'=>$helo,'maform'=>$form->createView()]);
    }

}