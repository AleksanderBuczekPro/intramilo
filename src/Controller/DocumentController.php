<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Document;
use App\Form\DocumentType;
use App\Entity\SubCategory;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class DocumentController extends AbstractController
{
    /**
     * Permet d'importer un document dans une sous-catégorie spécifique
     * 
     * @Route("/doc/{slug}/{sub_slug}/document/new", name="document_create")
     * 
     * @ParamConverter("category",    options={"mapping": {"slug":   "slug"}})
     * @ParamConverter("subCategory", options={"mapping": {"sub_slug":   "slug"}})
     */
    public function create(Category $category, SubCategory $subCategory, Request $request, EntityManagerInterface $manager)
    {

        $document = new Document();

        $document->setSubCategory($subCategory);

        $form = $this->createForm(DocumentType::class, $document);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($document);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le document <strong>{$document->getTitle()}</strong> a bien été chargé !"

            );

            return $this->redirectToRoute('doc_show', ['slug' => $category->getSlug(), 'sub_slug' => $subCategory->getSlug()]);
        
        }

        return $this->render('documentation/document/create.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
            'subCategory' => $subCategory
        ]);
    }

    /**
     * Permet d'afficher les informations d'un document (Document)
     * 
     * @Route("/doc/{slug}/{sub_slug}/document/{id}", name="document_show")
     * 
     * @ParamConverter("category", options={"mapping": {"slug":   "slug"}})
     * @ParamConverter("subCategory", options={"mapping": {"sub_slug": "slug"}})
     * @ParamConverter("document", options={"mapping": {"id": "id"}})
     * 
     * @return Response
     */
    public function show(Category $category, SubCategory $subCategory, Document $document, EntityManagerInterface $manager)
    {

        return $this->render('documentation/document/show.html.twig', [
            'category' => $category,
            'subCategory' => $subCategory,
            'document' => $document
        ]);
    }

    /**
     * Permet d'incrémenter le compteur de vues pour un document (à l'ouverture du fichier)
     * 
     * @Route("/doc/views", name="document_views")
     *
     * @return void
     */
    public function view(Request $request, DocumentRepository $repo, EntityManagerInterface $manager){

        $id = $request->request->get('id');
        $document = $repo->findOneById($id);

        $views = $document->getViews();
        $views++;
        $document->setViews($views);

        $manager->persist($document);
        $manager->flush();

        return $this->render('documentation/document/show.html.twig', [
            'category' => $document->getSubCategory(),
            'subCategory' => $document->getSubCategory()->getCategory(),
            'document' => $document
        ]);
    }

    /**
     * Permet de modifier un document
     * 
     * @Route("/doc/{slug}/{sub_slug}/document/{id}/edit", name="document_edit")
     * 
     * @ParamConverter("category", options={"mapping": {"slug":   "slug"}})
     * @ParamConverter("subCategory", options={"mapping": {"sub_slug": "slug"}})
     * @ParamConverter("document", options={"mapping": {"id": "id"}})
     * 
     * @return Response
     */
    public function edit(Category $category, SubCategory $subCategory, Document $document, Request $request, EntityManagerInterface $manager)
    {

        $form = $this->createForm(DocumentType::class, $document);

        $form->handleRequest($request);

        dump($document);

        if($form->isSubmitted() && $form->isValid()){
            
            $manager->persist($document);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le fichier <strong>{$document->getTitle()}</strong> a bien été modifié !"

            );

            // Gestion des nouveaux slugs
            $slug = $document->getSubCategory()->getCategory()->getSlug();
            $subSlug = $document->getSubCategory()->getSlug();

            return $this->redirectToRoute('document_show', ['slug' => $slug, 'sub_slug' => $subSlug, 'id' => $document->getId()]);

        }

        return $this->render('documentation/document/edit.html.twig', [
            'form'=> $form->createView(),
            'category' => $category,
            'subCategory' => $subCategory,
            'document' => $document
        ]);
    }

    /**
     * Permet de mettre à la Une un document
     * 
     * @Route("/doc/document/{id}/front", name="document_front")
     *  
     *
     * @return Response
     */
    public function front(Document $document, EntityManagerInterface $manager){

        if($document->getFront() == '0'){

            $document->setFront('1');

        }else{

            $document->setFront('0');

        }

        $manager->persist($document);
        $manager->flush();

        // Gestion des nouveaux slugs
        $slug = $document->getSubCategory()->getCategory()->getSlug();
        $subSlug = $document->getSubCategory()->getSlug();

        return $this->redirectToRoute('document_show', ['slug' => $slug, 'sub_slug' => $subSlug, 'id' => $document->getId()]);

    }
 
    /**
     * Permet de supprimer un fichier (Document)
     *
     * @Route("/doc/{slug}/{sub_slug}/document/{id}/delete", name="document_delete")
     * 
     * @ParamConverter("subCategory", options={"mapping": {"sub_slug":   "slug"}})
     * @ParamConverter("sheet", options={"mapping": {"sheet_slug": "slug"}})
     * @ParamConverter("document", options={"mapping": {"id": "id"}})
     * 
     */
    public function delete(Document $document, EntityManagerInterface $manager)
    {
        $manager->remove($document);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le fichier <strong>{$document->getTitle()}</strong> a bien été supprimé !"

        );

         // Gestion des nouveaux slugs
         $slug = $document->getSubCategory()->getCategory()->getSlug();
         $subSlug = $document->getSubCategory()->getSlug();

         return $this->redirectToRoute('doc_show', ['slug' => $slug, 'sub_slug' => $subSlug]);


    }
}
