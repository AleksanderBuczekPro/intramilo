<?php

namespace App\Controller;

use App\Service\Search;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    /**
     * Résultat de la recherche dans Documentation
     * 
     * @Route("/search", name="search_index")
     * 
     */
    public function index(Request $request, Search $search)
    {

        // Gestion de la recherche POST (formulaire)
        // $query = $request->request->get('q');

        // Gestion de la recherche GET
        $query = $request->query->get('q');
        $sort = $request->query->get('sort');

        $users = null;
        $antennes = null;
        $files = null;
        
        if($query){

            $users = $search->getUsers($query);
            $antennes = $search->getAntennes($query);
            $files = $search->getSheets($query, $sort);
            
        }

        
        return $this->render('documentation/search.html.twig', [
            'query' => $query,
            'files' => $files,
            'users' => $users,
            'antennes' => $antennes
        ]);            

    }
}
