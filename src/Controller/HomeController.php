<?php

namespace App\Controller;

use App\Service\Docs;
use App\Repository\UserRepository;
use App\Repository\CategoryRepository;
use App\Repository\ParametersRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class HomeController extends Controller{

    // /**
    //  * @Route("/hello/{prenom}/age/{age}", name="hello")
    //  * @Route("/hello", name="hello_base")
    //  * @Route("/hello/{prenom}", name="hello_prenom")
    //  * 
    //  * Montre la page qui dit bonjour
    //  *
    //  * @return void
    //  */
    // public function hello($prenom = "anonyme", $age = 0){
    //     return $this->render(
    //         'hello.html.twig',
    //         [
    //             'prenom' => $prenom,
    //             'age' => $age

    //         ]
    //     );

    // }

    /**
     * @Security("is_granted('ROLE_USER')")
     * 
     * @Route("/", name="homepage")
     *
     */
    public function home(UserRepository $userRepo, Docs $docs, Request $request, CategoryRepository $catRepo, ParametersRepository $paramRepo){

        $parameters = $paramRepo->find(1);

        $fronts = $docs->getFrontDocs();
        $files = $docs->getLastDocs();

        // Gestion de la recherche
        // $query = $request->query->get('q');

        // if(isset($query)){
        //     return $this->redirectToRoute('search_index', ['q' => $query]);
        // }

        return $this->render(
            'home.html.twig',
            [ 
                'fronts' => $fronts,
                'files' => $files,
                'websites' => $this->getUser()->getWebsites(),
                'parameters' => $parameters
            
            ]

        );

    }


    
}




?>