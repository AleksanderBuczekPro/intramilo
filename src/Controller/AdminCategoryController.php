<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Entity\SubCategory;
use App\Form\SubCategoryType;
use App\Repository\PoleRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCategoryController extends AbstractController
{
    /**
     * Gestion des catégories et des sous-catégories
     * 
     * @Route("/admin/category", name="admin_category_index")
     */
    public function index(PoleRepository $repo)
    {

        $poles = $repo->findAll();

        return $this->render('admin/category/index.html.twig', [
            'poles' => $poles,
        ]);
    }

    /**
     * Permet d'ajouter une catégorie
     * 
     * @Route("/admin/category/new", name="admin_category_create")
     *
     * @return Response
     */
    public function createCategory(Request $request, ObjectManager $manager) {

        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($category);
            $manager->flush();

            $this->addFlash(
                'success',
                "La catégorie <strong>{$category->getTitle()}</strong> a bien été créée !"

            );

            return $this->redirectToRoute('admin_category_index');
        
        }

        return $this->render('admin/category/create.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * Permet d'ajouter une sous-catégorie
     * 
     * @Route("/admin/sub-category/new", name="admin_sub_category_create")
     *
     * @return Response
     */
    public function createSubCategory(Request $request, ObjectManager $manager) {

        $subCategory = new SubCategory();

        $form = $this->createForm(SubCategoryType::class, $subCategory);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($subCategory);
            $manager->flush();

            $this->addFlash(
                'success',
                "La sous-catégorie <strong>{$subCategory->getTitle()}</strong> a bien été ajoutée !"

            );

            return $this->redirectToRoute('admin_category_index');
        
        }

        return $this->render('admin/category/create_sub.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * Permet de modifier une catégorie
     * 
     * @Route("/admin/category/{id}/edit", name="admin_category_edit")
     */
    public function editCategory(Category $category, Request $request, ObjectManager $manager)
    {

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            $manager->persist($category);
            $manager->flush();

            $this->addFlash(
                'success',
                "La catégorie <strong>{$category->getTitle()}</strong> a bien été modifiée !"

            );

            return $this->redirectToRoute('admin_category_index');

        }

        return $this->render('admin/category/edit.html.twig', [
            'form'=> $form->createView(),
            'category' => $category
        ]);
    }

    /**
     * Permet de modifier une catégorie
     * 
     * @Route("/admin/sub-category/{id}/edit", name="admin_sub_category_edit")
     */
    public function editSubCategory(SubCategory $subCategory, Request $request, ObjectManager $manager)
    {

        $form = $this->createForm(SubCategoryType::class, $subCategory);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            $manager->persist($subCategory);
            $manager->flush();

            $this->addFlash(
                'success',
                "La sous-catégorie <strong>{$subCategory->getTitle()}</strong> a bien été modifiée !"

            );

            return $this->redirectToRoute('admin_category_index');

        }

        return $this->render('admin/category/edit_sub.html.twig', [
            'form'=> $form->createView(),
            'subCategory' => $subCategory
        ]);
    }

    /**
     * Permet de supprimer une catégorie
     * 
     * @Route("/admin/category/{id}/delete", name="admin_category_delete")
     */
    public function deleteCategory(Category $Category, ObjectManager $manager)
    {

        $manager->remove($Category);
        $manager->flush();

        $this->addFlash(
            'success',
            "La categorie <strong>{$Category->getTitle()}</strong> a bien été supprimée !"

        );

        return $this->redirectToRoute('admin_category_index');


    }


    /**
     * Permet de supprimer une sous-catégorie
     * 
     * @Route("/admin/sub-category/{id}/delete", name="admin_sub_category_delete")
     */
    public function deleteSubCategory(SubCategory $subCategory, ObjectManager $manager)
    {

        $manager->remove($subCategory);
        $manager->flush();

        $this->addFlash(
            'success',
            "La sous-categorie <strong>{$subCategory->getTitle()}</strong> a bien été supprimée !"

        );

        return $this->redirectToRoute('admin_category_index');


    }


}
