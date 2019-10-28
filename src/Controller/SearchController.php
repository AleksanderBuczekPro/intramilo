<?php

namespace App\Controller;

use App\Service\Search;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    /**
     * Résultat de la recherche dans Documentation
     * 
     * @Route("/search", name="search_index")
     */
    public function index(ObjectManager $manager, Request $request, Search $search)
    {

        $query = $request->query->get('q');

        $files = $search->getResults($query);

        

        return $this->render('documentation/search.html.twig', [
            'query' => $query,
            'files' => $files
        ]);
    }
}
