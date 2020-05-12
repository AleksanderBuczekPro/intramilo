<?php

namespace App\Controller;

use App\Entity\Pole;
use App\Service\Docs;
use App\Service\Stats;
use App\Service\Filter;
use App\Service\Search;
use App\Entity\Category;
use App\Form\SearchType;
use App\Entity\SubCategory;
use App\Service\Notification;
use App\Repository\PoleRepository;
use App\Repository\UserRepository;
use App\Repository\SheetRepository;
use App\Repository\CategoryRepository;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SubCategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class DocumentationController extends AbstractController
{
    /**
     * Permet d'afficher la page d'accueil de la documentation
     * 
     * @Route("/documentation", name="doc_index")
     */
    public function index(PoleRepository $repo, CategoryRepository $catRepo, UserRepository $userRepo, Request $request, Stats $stats)
    {
        
        // Gestion de la recherche
        $query = $request->query->get('q');

        if(isset($query)){
            return $this->redirectToRoute('search_index', ['q' => $query]);
        }
        
                
        // Gestion des catégories
        $poles = $repo->findBy(array(), array('title' => 'ASC'));

        $authors = $userRepo->findAll();

        $categories = $catRepo->findBy(array(), array('title' => 'ASC'));

        return $this->render('documentation/index2.html.twig', [
            'poles' => $poles,
            'categories' => $categories,
            'popular' => $stats->getPopularSheets(),
            'authors' => $authors
        ]);
    }

    /**
     * Permet d'afficher le pôle
     * 
     * @Route("/documentation/pole/{id}", name="doc_pole")
     */
    public function pole(Pole $pole, PoleRepository $repo, Docs $docs)
    {
        $poles = $repo->findAll();

        $fronts = $docs->getFrontDocs();

        $contributors = $docs->getLastContributors();

        return $this->render('documentation/pole.html.twig', [
            'pole' => $pole,
            'poles' => $poles,
            'fronts' => $fronts
        ]);
    }

    /**
     * Permet d'afficher le contenu d'une sous-catégorie (fiches et documents)
     * 
     * @Route("/documentation/category/{slug}/{sub_slug}", name="doc_show")
     * 
     * @ParamConverter("subCategory", options={"mapping": {"sub_slug": "slug"}})
     * 
     * @return Response
     */
    public function show(Category $category, SubCategory $subCategory, SheetRepository $sheetRepo, DocumentRepository $docRepo, Docs $docs)
    {
        $sheets = $sheetRepo->findBySubCategory($subCategory);
        $documents = $docRepo->findBySubCategory($subCategory);

        dump($sheets);

        $waitingStatus = ["TO_VALIDATE", "TO_CORRECT", "DRAFT"]; 

        $sheetsOnline = [];
        $sheetsWaiting = [];

        // Tri des fiches
        foreach($sheets as $sheet){

            // En attente
            if(in_array($sheet->getStatus(), $waitingStatus)) 
            { 
                // On affiche uniquement les documents de l'utilisateur connecté
                if ($sheet->getAuthor() == $this->getUser()){
                    $sheetsWaiting[] = $sheet;
                }
            }
            // En ligne
            else
            { 
                $sheetsOnline[] = $sheet;
            } 
        }

        $documentsOnline = [];
        $documentsWaiting = [];

        // Tri des documents En ligne / En attente
        foreach($documents as $document){

            // En attente
            if(in_array($document->getStatus(), $waitingStatus)) 
            {
                // On affiche uniquement les documents de l'utilisateur connecté
                if ($document->getAuthor() == $this->getUser()){
                    $documentsWaiting[] = $document;
                }
            }
            // En ligne
            else
            { 
                $documentsOnline[] = $document;
            } 
        }

        

        $filesOnline = array_merge_recursive($sheetsOnline, $documentsOnline);
        $filesWaiting = array_merge_recursive($sheetsWaiting, $documentsWaiting);

        usort($filesOnline, function($a, $b){ 
            return strcasecmp($a->getTitle(), $b->getTitle());
        });

        usort($filesWaiting, function($a, $b){ 
            return strcasecmp($a->getTitle(), $b->getTitle());
        });

        return $this->render('documentation/show.html.twig', [
            'category' => $category,
            'subCategory' => $subCategory,
            'filesOnline' => $filesOnline,
            'filesWaiting' => $filesWaiting
        ]);
    }

}
