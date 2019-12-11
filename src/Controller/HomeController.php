<?php

namespace App\Controller;

use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller{

    /**
     * @Route("/hello/{prenom}/age/{age}", name="hello")
     * @Route("/hello", name="hello_base")
     * @Route("/hello/{prenom}", name="hello_prenom")
     * 
     * Montre la page qui dit bonjour
     *
     * @return void
     */
    public function hello($prenom = "anonyme", $age = 0){
        return $this->render(
            'hello.html.twig',
            [
                'prenom' => $prenom,
                'age' => $age

            ]
        );

    }

    /**
     * @Security("is_granted('ROLE_USER')")
     * 
     * @Route("/", name="homepage")
     *
     */
    public function home(AdRepository $adRepo, UserRepository $userRepo){

        

        return $this->render(
            'home.html.twig',
            [ 
                'ads' => $adRepo->findBestAds(3),
                'users' => $userRepo->findBestUsers(2)
            
            ]

        );

    }

    
}




?>