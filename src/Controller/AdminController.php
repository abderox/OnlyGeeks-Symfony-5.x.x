<?php

namespace App\Controller;

use App\Repository\PostCommentRepository;
use \Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]

class AdminController extends AbstractController
{
    #[Route('/articles/comments', name: 'admin')]
    public function index(PostCommentRepository $repo , PaginatorInterface $paginator,Request $request): Response
    {

        $q=$request->query->get('q');
        $query=$repo->findwithsearch($q);
       // $query=$repo->findBy([],['updatedAt'=>'DESC']);
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            8 );
        return $this->render('admin/comments.html.twig', [
            'comments' => $pagination
        ]);
    }
}
