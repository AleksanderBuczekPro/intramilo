<?php

namespace App\Controller;

use App\Service\Docs;
use App\Entity\Category;
use App\Form\SearchType;
use App\Entity\SubCategory;
use App\Repository\SheetRepository;
use App\Repository\CategoryRepository;
use App\Repository\DocumentRepository;
use App\Repository\PoleRepository;
use App\Repository\SubCategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
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
    public function index(PoleRepository $repo, Request $request, ObjectManager $manager)
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

        $files = array_merge_recursive($sheets, $documents);

        usort($files, function($a, $b){ 
            return strcasecmp($a->getTitle(), $b->getTitle());
        });

        return $this->render('documentation/show.html.twig', [
            'category' => $category,
            'subCategory' => $subCategory,
            'files' => $files
        ]);
    }

}
