<?php

namespace App\Controller;
use App\Entity\FriendShip;
use App\Entity\Helo;
use App\Entity\PostComment;
use App\Entity\PostDislikes;
use App\Entity\PostLike;
use App\Entity\Profile;
use App\Entity\Reference;
use App\Entity\Tag;
use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\HeloType;
use App\Form\ProfileType;
use App\Form\SearchTyp;
use App\Form\TagType;
use App\Form\UpdatePasswordFormType;
use App\Form\UserProfileType;
use App\Form\UserType;
use App\Repository\FriendShipRepository;
use App\Repository\HeloRepository;
use App\Repository\PostCommentRepository;
use App\Repository\PostDislikesRepository;
use App\Repository\PostLikeRepository;
use App\Repository\ProfileRepository;
use App\Repository\ReferenceRepository;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Service\Uploader;
use Aws\S3\S3Client;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectManager;
use Gedmo\ReferenceIntegrity\Mapping\Validator;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Message;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use const http\Client\Curl\PROXY_HTTP;

class onlygeeksEssentials extends AbstractController
{
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }
//    /**
//     * @Route("/", name="app_homepage")
//     */
//    public function homepage()
//    {
//        return $this->render('User_content//homepage.html.twig');
//    }

//    /**
//     * @Route("/onlygeeks/show/{nam<[^0-9]+>}",name="app_question_show")
//     */
//    public function show($nam)
//    {
//        $answers = [
//          'make money not friends',
//          'hello from agadir ',
//          'what are you doing',
//
//        ];
//
//        return $this->render('User_content//show.html.twig', ['User_content/'=>ucwords(str_replace('_',' ',$nam)),'answers'=>$answers]);
//        // Annotations are simple to read
//
//    }



    /**
     * @Route("/onlygeeks/create" , methods={"POST","get"} , name="app_create")
     * @Security("is_granted('ROLE_USER') && user.isVerified() ")
     */
    //     * @Security("is_granted('ROLE_USER') && user.isVerified() éé helo.getUser()===user")
    public function create(Request $request, EntityManagerInterface $em )
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');


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
//        if(!$this->getUser()->isVerified())
//        {
//            $this->addFlash('error','You need to verify your account first !');
//            return $this->redirectToRoute('app_show');
//        }
        $helo = new Helo();
        $user=new User();



         $form = $this->createForm(HeloType::class,$helo);

        $form->handleRequest($request); //remplace $request->request->all()
        if($form->isSubmitted() && $form->isValid())
        {
            //dump($form->getData());
            $data=$form->getData(); // $form->get('title')->getDta(); //$form['']->getData();
            $helo->setUser($this->getUser());
            $helo->setIsPublished(true);
            $em->persist($helo);
            $em->flush();
            $this->addFlash('success','Pin created successfully!');
            return $this->redirectToRoute('app_my_posts');
        }

        return $this->render('User_content/create.html.twig', ['maform'=>$form->createView(),'helo'=>$helo]);
        //

    }

    /**
     * @Route("/onlygeeks",name="app_show")
     */
    public function showart(HeloRepository $repo,TagRepository $tagRepository,PaginatorInterface $paginator,Request  $request)
    {
       // return $this->redirect('https://www.google.com/');
        $tags=$tagRepository->findpopulartags(true);
        $alltags=$tagRepository->findAll();
        $q=$request->query->get('q');
        $query=$repo->findwithsearch($q);
//        $query=$repo->findBy([],['updatedAt'=>'DESC']);
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            8 /*limit per page*/
        );

        return $this->render('User_content/homepage.html.twig', ['helos' => $pagination,'tags'=>$tags,'alltags'=>$alltags ]);

    }
    /**
     * @Route("/",name="app_splash")
     */
    public function showsplash(HeloRepository $repo,TagRepository $tagRepository,PaginatorInterface $paginator,Request  $request)
    {


        return $this->render('User_content/splash.html.twig');

    }

    /**
     * @Route("/tags/{nam}",name="app_filter-tags")
     */
    public function searchwithtags(HeloRepository $repo,$nam,TagRepository $tagRepository,PaginatorInterface $paginator,Request  $request)
    {

        $tags=$tagRepository->findpopulartags(true);
        $alltags=$tagRepository->findAll();
        $tagtitle=$tagRepository->findOneBy(['slug'=>$nam]);
        $q=$request->query->get('q');
        $query=$repo->findwithtags($nam,$q);
//        $query=$repo->findBy([],['updatedAt'=>'DESC']);
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            8 /*limit per page*/
        );

        return $this->render('User_content/searchwithtags.html.twig', ['helos' => $pagination,'tags'=>$tags,'alltags'=>$alltags,'tagtitle'=>$tagtitle,'keyword'=>$q ]);

    }

    /**
     * @Route("/onlygeeks/shows/{id<\d+>}",priority="10",name="app_show_pin")
     * @Security("is_granted('PIN_VIEW',helo)")
     */
    public function showa(Helo $helo,HeloRepository $repo , $id)
    {
        if(!$this->getUser())
        {
            $this->addFlash('error','login required');
            return $this->redirectToRoute('app_show');
        }

        if($helo)
        {
            return $this->render('User_content/show.html.twig', ['helo' => $helo]);
        }
       else
       {
          // $error= ['message'=>"cet élément avec ce numero : $id  n'existe pas ", 'number'=>404];
           $yoyo= $this->createNotFoundException("cet élément avec ce numero : $id  n'existe pas ");
           //dd($yoyo);
           return $this->render('User_content/error.html.twig', ['error' => $yoyo]);
       }

    }

    /**
     * @Route("/onlygeeks/errors" , name="app_error")
     */
    public function error()
    {
        return $this->render('error404.html.twig');
    }
    /**
     * @Route("/onlygeeks/shows/{id<\d+>}/edit" , name="app_edit" , methods={"POST","GET"})
     * @Security("is_granted('PIN_EDIT',helo)")
     */
    public function edit(Helo $helo ,$id ,Request $request ,EntityManagerInterface $em) : Response
    {

//        if(!$this->getUser())
//        {
//            $this->addFlash('error','login first');
//            return $this->redirectToRoute('app_show');
//        }
//        if(!$this->getUser()->isVerified())
//        {
//            $this->addFlash('error','You need to verify your account first !');
//            return $this->redirectToRoute('app_show');
//        }
//
//
//        if($this->getUser()!=$helo->getUser())
//        {
//            $this->addFlash('error','You are not authorized !');
//            return $this->redirectToRoute('app_show');
//        }



//        $form = $this->createFormBuilder($helo)
//            ->add('title',TextType::class ,['attr'=>['class'=>'form-control','autofocus'=>'true']])
//            ->add('age', NumberType::class , ['attr'=>['class'=>'form-control','autofocus'=>'true'],'help' => 'You need help , email me .'])
//            ->add('submit',SubmitType::class,['label'=>'update'])
//            ->getForm()
//        ;
        $form = $this->createForm(HeloType::class,$helo );
        $form->handleRequest($request); //remplace $request->request->all()
        if($form->isSubmitted() && $form->isValid())
        {
            //dump($form->getData());
            $data=$form->getData(); // $form->get('title')->getDta(); //$form['']->getDtat();
//            $em->persist($helo);
            $em->flush();
            $this->addFlash('success','Post updated successfully!');

           return $this->redirectToRoute('app_my_posts');

        }

        return $this->render('User_content/edit.html.twig', ['helo'=>$helo,'maform'=>$form->createView()]);
    }
    /**
     * @Route("/onlygeeks/shows/{id<\d+>}/delete" , name="app_delete" , methods={"DELETE","POST"})
     * @Security("is_granted('PIN_EDIT',helo)")
     */
    public function delete(Helo $helo,UserRepository $user  ,Request $request ,EntityManagerInterface $em) : Response
    {

//        if($this->getUser()!=$helo->getUser())
//        {
//            $this->addFlash('error','You are not authorized !');
//            return $this->redirectToRoute('app_show');
//        }
        $em->remove($helo);
        $em->flush();
        $this->addFlash('info','Pin deleted successfully!');
        $us=$user->findOneBy(['email'=>$this->getUser()->getUsername()]);
        return $this->redirectToRoute('app_show_profile',['id'=>$us->getId()]);
    }
    /**
     * @Route("/onlygeeks/search" , name="app_search" )
     */
    public function search(Request $request ) : Response
    {

        $helo = new Helo();
        $helo->setService($request->query->get('service'));
        $form = $this->createForm(HeloType::class,$helo);
        if (!$form->has('noma')) {
            return new Response(null, 204);
        }


        return $this->render('User_content/service.html.twig', ['form'=>$form->createView()]);
    }

    /**
     * @param Helo $helo
     * @param PostLikeRepository $likepost
     * @param EntityManagerInterface $manager
     * @return Response
     * @Security("is_granted('ROLE_USER') && user.isVerified() ")
     * @Route("/onlygeeks/show/{id<\d+>}/like" , name="app_pin_like" )
     */
    public function like(Helo $helo,PostLikeRepository $likepost,$id,EntityManagerInterface $manager) :Response
    {
        if($helo->isLikedBy($this->getUser()))
        {
            $like= $likepost->findOneBy(['pin'=>$helo,'user'=>$this->getUser()]);


                $manager->remove($like);


            $manager->flush();
            return  $this->json([
               'code'=>200,
               'message'=>"You liked a post" ,
                'likes'=>$likepost->count(['pin'=>$helo])
            ],200);
        }
        else if($helo->isDisLikedBy($this->getUser()))
        {
            return  $this->json([
                'code'=>403,
                'message'=>"You liked a post" ,
                'likes'=>$likepost->count(['pin'=>$helo])
            ],200);
        }
        $like = new PostLike();
        $like->setUser($this->getUser())
              ->setPin($helo);
        $manager->persist($like);
        $manager->flush();
        return  $this->json([
            'code'=>200,
            'message'=>"natural" ,
            'likes'=>$likepost->count(['pin'=>$helo])
        ],200);
    }
    /**
     * @Route("/onlygeeks/show/{id<\d+>}/comment" , name="app_pin_comment" )
     */
    public function comment(Helo $helo,PostCommentRepository $commmentpost,$id,EntityManagerInterface $manager,Request $request) :Response
    {
//
        $like = new PostComment();


        $like->setUser($this->getUser())
            ->setPin($helo)
            ->setComment($request->request->get('comment'))
        ->setIsDeleted(false);
        $manager->persist($like);
        $manager->flush();
//        foreach ($commmentpost->findAll() as $k )
//        {
//            $list[]=$k->getComment();
//        }
        $json= $this->json([
            'code'=>200,
            'message'=>"response" ,
            'comments'=>$like->getComment(),
            'createdAt'=>$like->getCreatedAt(),
            'id_user'=>$like->getUser()->getId(),
            'fullname'=>$like->getUser()->getFullName(),
            "count_comments"=>$commmentpost->count(["pin"=>$helo]),
            "countNonedeletedcomments"=>count($like->getPin()->getNoNdeletedPostComments()),
            "countDeletedcomments"=>count($like->getPin()->getdeletedPostComments())
        ],200);
         return $json;
    }
    /**
     * @param Helo $helo
     * @param EntityManagerInterface $manager
     * @return Response
     * @Security("is_granted('ROLE_USER') && user.isVerified() ")
     * @Route("/onlygeeks/show/{id<\d+>}/dislike" , name="app_pin_dislike" )
     */
    public function dislike(Helo $helo,PostDislikesRepository $likepost,$id,EntityManagerInterface $manager) :Response
    {
        if($helo->isDisLikedBy($this->getUser()))
        {
            $like= $likepost->findOneBy(['pin'=>$helo,'user'=>$this->getUser()]);

            $manager->remove($like);


            $manager->flush();
            return  $this->json([
                'code'=>200,
                'message'=>"You disliked a post" ,
                'dislikes'=>$likepost->count(['pin'=>$helo])
            ],200);
        }
        else if($helo->isLikedBy($this->getUser()))
        {
            return  $this->json([
                'code'=>403,
                'message'=>"unlike first a post" ,
                'dislikes'=>$likepost->count(['pin'=>$helo])
            ],200);
        }
        $like = new PostDislikes();
        $like->setUser($this->getUser())
            ->setPin($helo);
        $manager->persist($like);
        $manager->flush();
        return  $this->json([
            'code'=>200,
            'message'=>"natural" ,
            'dislikes'=>$likepost->count(['pin'=>$helo])
        ],200);
    }
    /**
     * @Route("/account", name="app_account")
     * @Security("is_granted('ROLE_USER') && user.isVerified() ")
     */
    public function index(LoggerInterface $logger)
    {
        $logger->debug('Checking account page for '.$this->getUser()->getEmail());
        return $this->render('account/index.html.twig', [

        ]);
    }
    /**
     * @Security("is_granted('ROLE_USER')  ")
     * @Route("/onlygeeks/profile/{id<\d+>}",name="app_show_profile")
     */
    public function profile(UserRepository $repo , HeloRepository $repoh , $id ,PaginatorInterface $paginator,Request $request)
    {
        $p=0;
        $c=0;
        $posts=[];
        $user = $repo->find($id);
        if($user)
        {
        $helo=$repoh->findAll();

        foreach ($helo as $j)
            {
              if($j->getUser()->getId()==$user->getId())
              {

                  $posts[]=$j;
              }
            }
        if($posts){
        foreach ($posts as $k)
        {

            $p += count($k->getLikes());
            $c +=count($k->getPostComments());
        }

        }
        }
        $userinfo=['user'=>$user,'like'=>$p,'comment'=>$c];
        $query=$repoh->findBy(['user'=>$user],['updatedAt'=>'DESC']);

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            8 /*limit per page*/
        );



        return $this->render('account/index.html.twig', ['user'=>$userinfo,'helos'=>$pagination]);




    }

/**
 * @param Helo $helo
 * @param PostComment $postComment
 * @param $id
 * @param EntityManagerInterface $manager
 * @return Response
 * @Security("is_granted('ROLE_USER') && user.isVerified()")
 * @Route("/onlygeeks/show/{id<\d+>}/comment/delete/{cm<\d+>}" , name="app_delete_comment" )
 */
public function deletecomment(Helo $helo,PostCommentRepository $postComment,$id,$cm,EntityManagerInterface $manager) :Response
{

            $comment = $postComment->findOneBy(['id' => $cm, 'pin' => $helo, 'user' => $this->getUser()]);


        if($comment){
            if($this->isGranted("ROLE_ADMIN")) {
                  $manager->remove($comment);
            }
            else{
                $comment->setIsDeleted(True);
            }
        $manager->flush();

        return  $this->json([
            'code'=>200,
            'message'=>"You deleted a a comment" ,
            'id_comment'=>$id,
            'count'=>$postComment->count(['pin'=>$helo])
        ],200);
        }
        else {
            return  $this->json([
                'code'=>404,
                'message'=>"not found comment" ,
                'id_post'=>$id,
                'id_comment'=>$cm,
                'count'=>"????"
            ],404);
        }


}

    /**
     *  @param EntityManagerInterface $manager
     * @Route("/onlygeeks/profile/{id<\d+>}/follow" , name="app_profile_follow" )
     */
    public function follow(UserRepository $person,FriendShipRepository $friend,$id,EntityManagerInterface $manager,Request $request) :Response
    {

            $pr=$person->find($id);

            $like= $friend->findOneBy(['user'=>$this->getUser(),'friend'=>$pr]);
             if($like)
             {
                 $manager->remove($like);
                 $manager->flush();
                 return  $this->json([
                     'code'=>200,
                     'message'=>"You unfollowed him" ,
                     'People_i_follow'=>$friend->count(['user'=>$this->getUser()]),
                     'friendship'=>$friend->count(['friend'=>$pr,'user'=>$this->getUser()])
                 ],200);
             }
             else{
                $friend_class=new FriendShip();
                if($this->getUser()!=$pr) {
                    $friend_class->setUser($this->getUser());
                    $friend_class->setFriend($pr);
                    $manager->persist($friend_class);
                    $manager->flush();
                    return  $this->json([
                        'code'=>200,
                        'message'=>"You followed him" ,
                        'People_i_follow'=>$friend->count(['user'=>$this->getUser()]),
                        'friendship'=>$friend->count(['friend'=>$pr,'user'=>$this->getUser()])
                    ],200);
                }
                else{
                    return  $this->json([
                        'code'=>500,
                        'message'=>"You can't follow your self" ,
                    ],500);
                }
                  }


    }

    /**
     * @Security("is_granted('ROLE_USER') && user.isVerified() ")
     * @Route("/account/edit" , name="app_edit_profile" , methods={"POST","GET"})
     */
    public function edit_account(UserRepository $user_verif ,ProfileRepository $pr,Request $request ,EntityManagerInterface $em) : Response
    {
        $pr_v=$pr->findOneBy(['user'=>$this->getUser()]);
        $us=$user_verif->findOneBy(['email'=>$this->getUser()->getUsername()]);
        $t=$this->getUser()->getUsername();
        $profile =new Profile();
        if(is_null($pr_v))
        {

            $profile->setUser($this->getUser());
            $em->persist($profile);
            $em->flush();

        }
        $form1=$this->createForm(ProfileType::class,$pr_v);
        $form1->handleRequest($request);
        $form = $this->createForm(UserType::class,$this->getUser());
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {

            if($form->get('email')->getData()!=$t)
            {
                $this->addFlash('info','Please note that You will be asked to verify your account after email update !');
               $us->setIsVerified(false);
                $em->flush();

                $this->emailVerifier->sendEmailConfirmation('app_verify_email', $this->getUser(),
                    (new TemplatedEmail())
                        ->from(new Address('abdelhadi12mouzafir@gmail.com', 'Pinterest customer service'))
                        ->to($this->getUser()->getUsername())
                        ->subject('Please Confirm your Email')
                        ->htmlTemplate('registration/confirmation_email.html.twig')
                );

                $this->addFlash('info','Kindly check your mail box , email verification required !');
                return $this->redirectToRoute('app_logout');
            }

            $em->flush();
            $this->addFlash('success','Profile edited successfully!');


        }

        return $this->render('account/account.html.twig', ['maform'=>$form->createView()]);
    }

    /**
     * @Route("/onlygeeks/shows/{id}/references",name="upload_reference",methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function uploadreferences(Helo $helo,Request $request,Uploader $uploader, EntityManagerInterface $manager, ValidatorInterface $validator) : Response
    {
        /**
         * @var UploadedFile $uploaded
         */
        $uploaded=$request->files->get('reference');
        $violations=$validator->validate(
          $uploaded,

          [
              new NotNull(['message'=>"Kindly select a file to upload !"]),
              new File([
              'maxSize'=>'12M',
              'mimeTypes' => [
                  'image/*',
                  'application/pdf',
                  'application/msword',
                  'application/vnd.ms-excel',
                  'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                  'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                  'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                  'text/plain'
              ]

          ])
          ]
        );
        if($violations->count()>0)
        {
//            /**@var  ConstraintViolation $violation*/
//            $violation=$violations[0];
//           $this->addFlash('error',$violation->getMessage());
            return $this->json($violations,400);
        }
        else if($uploaded) {
            $filename = $uploader->uploadArticleReference($uploaded);
            $reference = new Reference($helo);
            $reference->setFilename($filename);
            $reference->setOriginalFilename($uploaded->getClientOriginalName() ?? $filename);
            $reference->setMimetype($uploaded->getMimeType() ?? 'application/octet-stream');
            $manager->persist($reference);
            $manager->flush();
        }
//        return $this->redirectToRoute('app_edit',['id'=>$helo->getId()]);
        return $this->json($reference ,201 ,[],[
            'groups'=>['main']
        ]);
    }
    /**
     * @Route ("/onlygeeks/shows/{id<\d+>}/references",name="app_list_refernces")
     */
    public function getAllreferences(Helo $helo)
    {
    return $this->json($helo->getReference(),200,[],[
        'groups'=>['main']
    ]);
    }
    /**
     * @Route("/onlygeeks/shows/references/{id<\d+>}/download",name="download_file")
     */
    public function downloadfiles(Reference $reference, Uploader $uploader,S3Client $s3Client) : Response
    {

//       $this->denyAccessUnlessGranted('FILE_DOWNLOAD',$reference->getPin());

        $dispo=HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $reference->getOriginalFilename()
        );
        $cmd = $s3Client->getCommand('GetObject', [
            'Bucket' => 'onlygeeks',
            'Key' =>$reference->getFilePath(),
            'ResponseContentType'=>$reference->getMimetype(),
            'ResponseContentDisposition'=>$dispo
        ]);

        $request = $s3Client->createPresignedRequest($cmd, '+30 minutes');
        $presignedUrl = (string)$request->getUri();
        if($reference->getPin()->getUser()->AmIfollowing($this->getUser()) || $this->getUser()==$reference->getPin()->getUser())
        {
            return new RedirectResponse($presignedUrl);

        }
        else
        {
            return $this->json([
                'code'=>401,
                'message'=>'You should follow first '
            ],200);
        }

//        $response = new StreamedResponse(function () use ($reference,$uploader)
//        {
//            $outputstream=fopen('php://output','wb');
//            $filestram=$uploader->readStream($reference->getFilePath(),false);
//            stream_copy_to_stream($filestram,$outputstream);
//        });
//        $response->headers->set('Content-Type',$reference->getMimetype());
//        $dispo=HeaderUtils::makeDisposition(
//            HeaderUtils::DISPOSITION_ATTACHMENT,
//            $reference->getOriginalFilename()
//        );
//        $response->headers->set('Content-Disposition',$dispo);
//       return $response;
    }
    /**
     * @Route("/onlygeeks/shows/references/{id}", name="app_reference_delete", methods={"DELETE"})
     */
    public function deleteArticleReference(ReferenceRepository $reference,$id, Uploader $uploader, EntityManagerInterface $entityManager)
    {
        $repo=$reference->find($id);
        $helo = $repo->getPin();
        $this->denyAccessUnlessGranted('PIN_EDIT', $helo);

        $entityManager->remove($repo);
        $entityManager->flush();

        $uploader->deleteFile($repo->getFilePath(),false);

        return new Response(null, 204);
    }
    /**
     * @Route("/onlygeeks/shows/myPosts",name="app_my_posts")
     * @IsGranted("ROLE_USER")
     */
    public function list(HeloRepository $repo,PaginatorInterface $paginator,Request $request) : Response
    {
        $query=$repo->findBy(['user'=>$this->getUser()],['updatedAt'=>'DESC']);
        $helos = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            6 /*limit per page*/
        );
       return $this->render('User_content/showmyposts.html.twig',['helos'=>$helos]);
    }
    /**
     * @Route("/onlygeeks/shows/myPosts/{id<\d+>}/publish",name="app_publish")
     * @IsGranted("ROLE_USER")
     */
    public function publish_unpublished(Helo $helo,HeloRepository $heloRepository,EntityManagerInterface $manager) : Response
    {
        $helopublished=$heloRepository->find($helo->getId());
        if($helopublished->getIsPublished())
        {
            $helo->setIsPublished(false);
            $helo->setPublishedAt(new \DateTimeImmutable(('now')));
            $manager->flush();
        }
        else{
            $helo->setIsPublished(true);
            $manager->flush();
        }

        return $this->redirectToRoute('app_my_posts');
    }


    /**
     * @Route("/account/privacy/",name="app_update_pass")
     * @IsGranted("ROLE_USER")
     */
    public function change_user_password(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {


        $user = $this->getUser();
        $form = $this->createForm(UpdatePasswordFormType::class);
        $form->handleRequest($request);
     //   $checkPass = $passwordEncoder->isPasswordValid($user, $form->get('oldPassword')->getData());


            if ($form->isSubmitted() && $form->isValid()) {
                $checkPass = $passwordEncoder->isPasswordValid($user, $form->get('oldPassword')->getData());

                $encodedPassword = $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                );
                if($checkPass)
                {
                    $user->setPassword($encodedPassword);
                    $this->getDoctrine()->getManager()->flush();
                    $this->addFlash('success','Password changed successfully ');
                    return $this->redirectToRoute('app_logout');
                }
                else{
                    $this->addFlash('error','wrong credentials , kindly re-enter your password');
                }
            }
            return $this->render('reset_password/update.html.twig', [
                'resetForm' => $form->createView(),
            ]);
        }





}