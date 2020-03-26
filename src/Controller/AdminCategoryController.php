<?php

namespace App\Controller;

use App\Entity\Pole;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Entity\SubCategory;
use App\Form\SubCategoryType;
use App\Repository\PoleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class AdminCategoryController extends AbstractController
{
    /**
     * Gestion des catégories et des sous-catégories
     * 
     * @Route("/admin/category/{id}", name="admin_category_index")
     */
    public function index(CategoryRepository $repo, Request $request, Category $category)
    {
        // $id = $request->query->get('id');
        // $category = $repo->findOneById($id);
        
        // $poles = $repo->findAll();

        return $this->render('admin/category/index.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * Permet d'ajouter une catégorie
     * 
     * @Route("/admin/pole/{id}/category/new", name="admin_category_create")
     * 
     * @ParamConverter("pole", options={"mapping": {"id": "id"}})
     *
     * @return Response
     */
    public function createCategory(Request $request, EntityManagerInterface $manager, Pole $pole) {

        $category = new Category();
        $category->setPole($pole);

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($category);
            $manager->flush();

            $this->addFlash(
                'success',
                "La catégorie <strong>{$category->getTitle()}</strong> a bien été créée !"

            );

            return $this->redirectToRoute('admin_documentation_index');
        
        }

        return $this->render('admin/category/create.html.twig', [
            'pole' => $pole,
            'form' => $form->createView()
        ]);

    }

    /**
     * Permet d'ajouter une sous-catégorie
     * 
     * @Route("/admin/category/{id}/sub-category/new", name="admin_sub_category_create")
     * 
     * @ParamConverter("category", options={"mapping": {"id": "id"}})
     *
     * @return Response
     */
    public function createSubCategory(Request $request, EntityManagerInterface $manager, Category $category) {

        $subCategory = new SubCategory();

        $subCategory->setCategory($category);

        $form = $this->createForm(SubCategoryType::class, $subCategory);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($subCategory);
            $manager->flush();

            $this->addFlash(
                'success',
                "La sous-catégorie <strong>{$subCategory->getTitle()}</strong> a bien été ajoutée !"

            );

            return $this->redirectToRoute('admin_category_index', [
                'id' => $category->getId()
            ]);
        
        }

        return $this->render('admin/category/create_sub.html.twig', [
            'pole' => $category->getPole(),
            'category' => $category,
            'form' => $form->createView()
        ]);

    }

    /**
     * Permet de modifier une catégorie
     * 
     * @Route("/admin/category/{id}/edit", name="admin_category_edit")
     */
    public function editCategory(Category $category, Request $request, EntityManagerInterface $manager)
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

            return $this->redirectToRoute('admin_category_index', [
                'id' => $category->getId()
            ]);

        }

        return $this->render('admin/category/edit.html.twig', [
            'form'=> $form->createView(),
            'category' => $category
        ]);
    }

    /**
     * Permet de modifier une sous-catégorie
     * 
     * @Route("/admin/sub-category/{id}/edit", name="admin_sub_category_edit")
     */
    public function editSubCategory(SubCategory $subCategory, Request $request, EntityManagerInterface $manager)
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

            return $this->redirectToRoute('admin_category_index', [
                'id' => $subCategory->getCategory()->getId()
            ]);
        }

        return $this->render('admin/category/edit_sub.html.twig', [
            'form'=> $form->createView(),
            'pole' => $subCategory->getCategory()->getPole(),
            'category' => $subCategory->getCategory(),
            'subCategory' => $subCategory
        ]);
    }

    /**
     * Permet de supprimer une catégorie
     * 
     * @Route("/admin/category/{id}/delete", name="admin_category_delete")
     */
    public function deleteCategory(Category $category, EntityManagerInterface $manager)
    {

        $subCategories = $category->getSubCategories();

        if(count($subCategories) > 0){

            $this->addFlash(
                'danger',
                "La catégorie <strong>{$category->getTitle()}</strong> ne peut être suprimée car elle contient des sous-catégories !"
    
            );
            
            return $this->redirectToRoute('admin_category_index', [
                'id' => $category->getId()
            ]);

        }

        $manager->remove($category);
        $manager->flush();

        $this->addFlash(
            'success',
            "La categorie <strong>{$category->getTitle()}</strong> a bien été supprimée !"

        );

        return $this->redirectToRoute('admin_documentation_index');


    }


    /**
     * Permet de supprimer une sous-catégorie
     * 
     * @Route("/admin/sub-category/{id}/delete", name="admin_sub_category_delete")
     */
    public function deleteSubCategory(SubCategory $subCategory, EntityManagerInterface $manager)
    {

        $sheets = $subCategory->getSheets();
        $documents = $subCategory->getDocuments();

        $counter = count($sheets) + count($documents);

        if($counter > 0){

            $this->addFlash(
                'danger',
                "La sous-categorie <strong>{$subCategory->getTitle()}</strong> ne peut être supprimée car elle contient des fiches ou des documents !"
    
            );
            
            return $this->redirectToRoute('admin_category_index', [
                'id' => $subCategory->getCategory()->getId()
            ]);

        }

        $manager->remove($subCategory);
        $manager->flush();

        $this->addFlash(
            'success',
            "La sous-categorie <strong>{$subCategory->getTitle()}</strong> a bien été supprimée !"

        );

        return $this->redirectToRoute('admin_category_index', [
            'id' => $subCategory->getCategory()->getId()
        ]);


    }


}
