<?php

namespace App\Controller;

use App\Service\Docs;
use App\Service\Filter;
use App\Service\Search;
use App\Entity\Category;
use App\Form\SearchType;
use App\Entity\SubCategory;
use App\Service\Notification;
use App\Repository\PoleRepository;
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
     * @Route("/doc", name="doc_index")
     */
    public function index(PoleRepository $repo, Request $request)
    {

        // Gestion des catégories
        $poles = $repo->findAll();

        // Gestion de la recherche
        $query = $request->query->get('q');
        
        if(isset($query)){
            return $this->redirectToRoute('search_index', ['q' => $query]);
        }

        return $this->render('documentation/index.html.twig', [
            'poles' => $poles
        ]);
    }

    /**
     * Permet d'afficher le contenu d'une sous-catégorie (fiches et documents)
     * 
     * @Route("/doc/{slug}/{sub_slug}", name="doc_show")
     * 
     * @ParamConverter("subCategory", options={"mapping": {"sub_slug": "slug"}})
     * 
     * @return Response
     */
    public function show(Category $category, SubCategory $subCategory, SheetRepository $sheetRepo, DocumentRepository $docRepo, Docs $docs)
    {
        $sheets = $sheetRepo->findBySubCategory($subCategory);
        $documents = $docRepo->findBySubCategory($subCategory);

        $waitingStatus = ["TO_VALIDATE", "TO_CORRECT"]; 

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
