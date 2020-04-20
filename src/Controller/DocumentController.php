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
     * @Route("/documentation/{id}/document/new", name="document_create_sub")
     * @Route("/documentation/document/new", name="document_create")
     * 
     */
    public function create(SubCategory $subCategory = null, Request $request, EntityManagerInterface $manager)
    {

        $document = new Document();

        if($subCategory){
            $document->setSubCategory($subCategory);  
        }
        

        $form = $this->createForm(DocumentType::class, $document, array('user' => $this->getUser()));

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $document->setViews(0);
            $document->setAuthor($this->getUser());
            $manager->persist($document);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le document <strong>{$document->getTitle()}</strong> a bien été chargé !"

            );

            // Gestion des nouveaux slugs
            $slug = $document->getSubCategory()->getCategory()->getSlug();
            $subSlug = $document->getSubCategory()->getSlug();

            return $this->redirectToRoute('doc_show', ['slug' => $slug, 'sub_slug' => $subSlug]);
        
        }

        return $this->render('documentation/document/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher les informations d'un document (Document)
     * 
     * @Route("/doc/document/{id}", name="document_show")
     * 
     * @return Response
     */
    public function show(Document $document)
    {

        return $this->render('documentation/document/show.html.twig', [
            'category' => $document->getSubCategory()->getCategory(),
            'subCategory' => $document->getSubCategory(),
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
     * @Route("/doc/document/{id}/edit", name="document_edit")
     * 
     * 
     * @return Response
     */
    public function edit(Document $document, Request $request, EntityManagerInterface $manager)
    {
      

        $form = $this->createForm(DocumentType::class, $document, array('user' => $this->getUser()));

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

            return $this->redirectToRoute('doc_show', ['slug' => $slug, 'sub_slug' => $subSlug]);

        }

        return $this->render('documentation/document/edit.html.twig', [
            'form'=> $form->createView(),
            'category' => $document->getSubCategory(),
            'subCategory' => $document->getSubCategory()->getCategory(),
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

        return $this->redirectToRoute('document_show', ['id' => $document->getId()]);

    }
 
    /**
     * Permet de supprimer un fichier (Document)
     *
     * @Route("/doc/document/{id}/delete", name="document_delete")
     * 
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
