<?php

namespace App\Controller;


use App\Repository\UserRepository;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Routing\Annotation\Route;
USE Lcobucci\JWT\Token\Builder ;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Configuration;

class ChatIndexController extends AbstractController
{

    /**
     * @Route("/Chat", name="index")
     */
    public function index(UserRepository $repository)
    {
        $config = Configuration::forSymmetricSigner(new Sha256(), InMemory::plainText('Abdelhadi'));
        $username = $repository->findOneBy(['id'=>1450]);
        $token = $config->builder()
            ->permittedFor(sprintf("/%s", $username->getOGusername()))
            ->getToken($config->signer(), $config->signingKey());




        $response =  $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);


        $response->headers->setCookie(
            new Cookie(
                'mercureAuthorization',
                $token->toString(),
                (new \DateTime())
                    ->add(new \DateInterval('PT2H')),
                '/.well-known/mercure',
                null,
                false,
                true,
                false,
                'strict'
            )
        );

        return $response;
    }
}
