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
     */
    public function index(Request $request, Search $search)
    {

        // Gestion de la recherche
        $query = $request->query->get('q');

        $files = $search->getResults($query);


        return $this->render('documentation/search.html.twig', [
            'query' => $query,
            'files' => $files
        ]);            

    }
}
