<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\SubCategory;
use App\Repository\CategoryRepository;
use App\Repository\SheetRepository;
use App\Repository\SubCategoryRepository;
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
    public function index(CategoryRepository $repo)
    {
        $categories = $repo->findAll();

        return $this->render('documentation/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * Permet d'afficher le contenu d'une sous-catégorie
     * 
     * @Route("/doc/{slug}/{sub_slug}", name="doc_show")
     * 
     * @ParamConverter("subCategory", options={"mapping": {"sub_slug": "slug"}})
     * 
     * @return Response
     */
    public function show(Category $category, SubCategory $subCategory, SheetRepository $repo)
    {
        $sheets = $repo->findBySubCategory($subCategory);

        return $this->render('documentation/show.html.twig', [
            'category' => $category,
            'subCategory' => $subCategory,
            'sheets' => $sheets
        ]);
    }
}
