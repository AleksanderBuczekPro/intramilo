<?php

namespace App\Controller;

use App\Entity\Sheet;
use App\Form\SheetType;
use App\Entity\Category;
use App\Entity\SubCategory;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class SheetController extends AbstractController
{

    /**
     * Permet d'ajouter une fiche dans une sous-catégorie spécifique
     * 
     * @Route("/doc/{slug}/{sub_slug}/sheet/new", name="sheet_create")
     * 
     * @ParamConverter("category",    options={"mapping": {"slug":   "slug"}})
     * @ParamConverter("subCategory", options={"mapping": {"sub_slug":   "slug"}})
     *
     * @return Response
     * 
     */
    public function create(Category $category, SubCategory $subCategory, Request $request, ObjectManager $manager) {

        $sheet = new Sheet();

        $sheet->setSubCategory($subCategory);

        $form = $this->createForm(SheetType::class, $sheet);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($sheet);
            $manager->flush();

            $this->addFlash(
                'success',
                "La fiche <strong>{$sheet->getTitle()}</strong> a bien été créée !"

            );

            return $this->redirectToRoute('doc_show', ['slug' => $category->getSlug(), 'sub_slug' => $subCategory->getSlug()]);
        
        }

        return $this->render('documentation/sheet/create.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
            'subCategory' => $subCategory
        ]);

    }

    /**
     * Permet de modifier une fiche
     * 
     * @Route("/doc/{slug}/{sub_slug}/{sheet_slug}/sheet/edit", name="sheet_edit")
     * 
     * @ParamConverter("subCategory", options={"mapping": {"sub_slug":   "slug"}})
     * @ParamConverter("sheet", options={"mapping": {"sheet_slug": "slug"}})
     * 
     * @return Response
     */
    public function edit(Category $category, SubCategory $subCategory, Sheet $sheet, Request $request, ObjectManager $manager)
    {

        $form = $this->createForm(SheetType::class, $sheet);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            $manager->persist($sheet);
            $manager->flush();

            $this->addFlash(
                'success',
                "La fiche <strong>{$sheet->getTitle()}</strong> a bien été modifiée !"

            );

            // Gestion des nouveaux slugs
            $slug = $sheet->getSubCategory()->getCategory()->getSlug();
            $subSlug = $sheet->getSubCategory()->getSlug();

            return $this->redirectToRoute('sheet_show', ['slug' => $slug, 'sub_slug' => $subSlug, 'sheet_slug' => $sheet->getSlug()]);

        }

        return $this->render('documentation/sheet/edit.html.twig', [
            'form'=> $form->createView(),
            'category' => $category,
            'subCategory' => $subCategory,
            'sheet' => $sheet
        ]);
    }


    /**
     * Permet d'afficher le contenu d'une fiche (Sheet)
     * 
     * @Route("/doc/{slug}/{sub_slug}/{sheet_slug}", name="sheet_show")
     * 
     * @ParamConverter("subCategory", options={"mapping": {"sub_slug":   "slug"}})
     * @ParamConverter("sheet", options={"mapping": {"sheet_slug": "slug"}})
     * 
     * @return Response
     */
    public function show(Category $category, SubCategory $subCategory, Sheet $sheet)
    {

        return $this->render('documentation/sheet/show.html.twig', [
            'category' => $category,
            'subCategory' => $subCategory,
            'sheet' => $sheet
        ]);
    }

    /**
     * Permet d'afficher une seule annonce
     *
     * @Route("/doc/{slug}/{sub_slug}/{sheet_slug}/delete", name="sheet_delete")
     * 
     * @ParamConverter("subCategory", options={"mapping": {"sub_slug":   "slug"}})
     * @ParamConverter("sheet", options={"mapping": {"sheet_slug": "slug"}})
     * 
     */
    public function delete(Sheet $sheet, ObjectManager $manager)
    {
        $manager->remove($sheet);
        $manager->flush();

        $this->addFlash(
            'success',
            "La fiche <strong>{$sheet->getTitle()}</strong> a bien été supprimée !"

        );

         // Gestion des nouveaux slugs
         $slug = $sheet->getSubCategory()->getCategory()->getSlug();
         $subSlug = $sheet->getSubCategory()->getSlug();

         return $this->redirectToRoute('doc_show', ['slug' => $slug, 'sub_slug' => $subSlug]);


    }


    
}
