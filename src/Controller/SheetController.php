<?php

namespace App\Controller;

use DateTimeZone;
use App\Entity\Sheet;
use App\Service\Menu;
use App\Form\SheetType;
use App\Form\ToolsType;
use App\Entity\Category;
use App\Form\CommentType;
use App\Entity\SubCategory;
use App\Entity\Interlocutor;
use App\Form\AttachmentType;
use PhpParser\Node\Stmt\Foreach_;
use App\Repository\SheetRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SubCategoryRepository;
use App\Repository\InterlocutorRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class SheetController extends AbstractController
{

    /**
     * Permet de valider une fiche "En cours de validation"
     * 
     * @Route("/documentation/validate", name="sheet_validate")
     *  
     * 
     *
     * @return void
     */
    public function validate(EntityManagerInterface $manager, Request $request, SheetRepository $repo){

        dump($request);

        $id = $request->request->get('id');
        $sheet = $repo->findOneById($id);

        // dump($id);

        // return $this->json(['data' => $request]);

        dump($request->request->get('content'));

        // On remplace le texte par le texte formaté (sans couleurs)
        $sheet->setContent($request->request->get('content'));

        // On récupère l'ancienne fiche
        $oldSheet = $sheet->getOrigin();

        dump($oldSheet);

        // On supprime l'ancienne fiche
        if($oldSheet != null){

            $sheet->setViews($oldSheet->getViews());
            $manager->remove($oldSheet);

        }

        // On initilaise les paramètres
        $sheet->setOrigin(null);
        $sheet->setStatus(null);

        $manager->persist($sheet);
        $manager->flush();
        
        
        $subCategory = $sheet->getSubCategory();
        $category = $subCategory->getCategory();

        // Gestion des slugs
        $slug = $sheet->getSubCategory()->getCategory()->getSlug();
        $subSlug = $sheet->getSubCategory()->getSlug();

        return $this->redirectToRoute('doc_show', ['slug' => $category->getSlug(), 'sub_slug' => $subCategory->getSlug()]);

    }

    /**
     * Permet d'ajouter une fiche dans une sous-catégorie spécifique
     * 
     * @Route("/documentation/{id}/sheet/new", name="sheet_create_sub")
     * @Route("/documentation/sheet/new", name="sheet_create")
     * 
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     * 
     */
    public function create(SubCategory $subCategory = null, Request $request, EntityManagerInterface $manager, SubCategoryRepository $subRepo, InterlocutorRepository $repo) {

        $sheet = new Sheet();

        if($subCategory){
            $sheet->setSubCategory($subCategory);  
        }
        

        $form = $this->createForm(SheetType::class, $sheet, array('user' => $this->getUser()));

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            // Récupération de toutes les variables POST
            $data = $request->request->all();
            
            // Pour chaque variable POST
            foreach($data as $key => $val) {

                // Si la clé contient 'interlocutor'
                if (strpos($key, 'interlocutor') !== false) {
                    
                    // On ajoute l'interlocuteur
                    $sheet->addInterlocutor($repo->findOneById($val));

                }

            }

            foreach($sheet->getHeaders() as $header){



                $header->setSheet($sheet);
                $manager->persist($header);

                foreach($header->getSections() as $section){

                    $section->setHeader($header);
                    $manager->persist($section);

                }
                
            }
            
            $sheet->setFront('0');

            if(!$this->isGranted("ROLE_ADMIN")){
                $sheet->setStatus("TO_VALIDATE");
            }           


            $sheet->setViews(0);
            $sheet->setAuthor($this->getUser());

            $sheet->setUpdatedAt(new \DateTime(null, new DateTimeZone('Europe/Paris')));

            $manager->persist($sheet);
            $manager->flush();

            $this->addFlash(
                'success',
                "La fiche <strong>{$sheet->getTitle()}</strong> a bien été créée !"

            );

            // Gestion des nouveaux slugs
            return $this->redirectToRoute('sheet_show', ['id' => $sheet->getId()]);

        
        }

        return $this->render('documentation/sheet/create.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * Permet de modifier une fiche afin qu'elle soit validée par un responsable
     * 
     * @Route("/documentation/{id}/sheet/edit", name="sheet_edit")
     * 
     * @IsGranted("ROLE_USER")
     *
     * @return void
     */
    public function edit(Sheet $sheet, EntityManagerInterface $manager, Request $request, InterlocutorRepository $repo){

        $form = $this->createForm(SheetType::class, $sheet, array('user' => $this->getUser()));       

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            // Initialisation des interlocuteurs
            foreach($sheet->getInterlocutors() as $interlocutor){

                $sheet->removeInterlocutor($interlocutor);
            
            }

            // Récupération de toutes les variables POST
            $data = $request->request->all();

            dump($data);
            
            // Pour chaque variable POST
            foreach($data as $key => $val) {

                // Si la clé contient 'interlocutor'
                if (strpos($key, 'interlocutor') !== false) {
                    
                    // On ajoute l'interlocuteur
                    $sheet->addInterlocutor($repo->findOneById($val));

                }

            }


            // Si c'est une fiche "En cours de validation" que l'on modifie
            if($sheet->getStatus() == "TO_VALIDATE"){

                foreach($sheet->getHeaders() as $header){


                    $header->setSheet($sheet);
                    $manager->persist($header);
    
                    foreach($header->getSections() as $section){
    
                        $section->setHeader($header);
                        $manager->persist($section);
    
                    }
                    
                }
                
                $sheet->setUpdatedAt(new \DateTime(null, new DateTimeZone('Europe/Paris')));

                $manager->persist($sheet);
                $manager->flush();

            }else{
                // Sinon c'est une fiche à corriger
                if($sheet->getStatus() == "TO_CORRECT"){

                    foreach($sheet->getHeaders() as $header){

                        $header->setSheet($sheet);
                        $manager->persist($header);
        
                        foreach($header->getSections() as $section){
        
                            $section->setHeader($header);
                            $manager->persist($section);
        
                        }
                        
                    }
                    
                    $sheet->setStatus("TO_VALIDATE");

                    $sheet->setUpdatedAt(new \DateTime(null, new DateTimeZone('Europe/Paris')));
                    $manager->persist($sheet);
                    $manager->flush();

                }else{

                    // Sinon c'est une fiche qui vient d'être modifiée

                    // Si c'est l'admin, on valide directement
                    if($this->isGranted("ROLE_ADMIN")){

                        $sheet->setUpdatedAt(new \DateTime(null, new DateTimeZone('Europe/Paris')));

                        foreach($sheet->getHeaders() as $header){

                            $header->setSheet($sheet);
                            $manager->persist($header);
            
                            foreach($header->getSections() as $section){
            
                                $section->setHeader($header);
                                $manager->persist($section);
            
                            }
                            
                        }

                        $manager->persist($sheet);
                        $manager->flush();


                    }else{

                        // On duplique la fiche
                        $sheetToValidate = clone $sheet;
                        
                        $sheetToValidate->setOrigin($sheet);
                        $sheetToValidate->setStatus('TO_VALIDATE');

                        // On duplique les entêtes
                        foreach($sheetToValidate->getHeaders() as $header){

                            $header->setSheet($sheetToValidate);
                            $manager->persist($header);
            
                            foreach($header->getSections() as $section){
            
                                $section->setHeader($header);
                                $manager->persist($section);
            
                            }
                            
                        }

                        $manager->refresh($sheet);

                        $sheetToValidate->setUpdatedAt(new \DateTime(null, new DateTimeZone('Europe/Paris')));
                        $manager->persist($sheetToValidate);

                    }
                    
                    $manager->flush();

                }

                

            }

            return $this->redirectToRoute('sheet_show', ['id' => $sheet->getId()]);

        }

        $subCategory = $sheet->getSubcategory();
        $category = $sheet->getSubCategory()->getCategory();

        return $this->render('documentation/sheet/edit.html.twig', [
            'form'=> $form->createView(),
            'category' => $category,
            'subCategory' => $subCategory,
            'sheet' => $sheet
        ]);

    }

    



     /**
     * Permet de mettre à la Une une fiche
     * 
     * @Route("/documentation/sheet/{id}/front", name="sheet_front")
     *  
     *
     * @return Response
     */
    public function front(Sheet $sheet, EntityManagerInterface $manager){

        if($sheet->getFront() == '0'){

            $sheet->setFront('1');

        }else{

            $sheet->setFront('0');

        }

        $manager->persist($sheet);
        $manager->flush();

        return $this->redirectToRoute('sheet_show', ['id' => $sheet->getId()]);

    }


    /**
     * Permet d'afficher le contenu d'une fiche (Sheet)
     * 
     * @Route("/documentation/sheet/{id}", name="sheet_show")
     * 
     * 
     * @return Response
     */
    public function show(Sheet $sheet, Request $request, EntityManagerInterface $manager, Menu $menu)
    {

        // Si la fiche est "En cours de validation"
        if($sheet->getStatus() == "TO_VALIDATE"){

            $form = $this->createForm(CommentType::class, $sheet);        

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){

                    $sheet->setStatus("TO_CORRECT");
                    $manager->persist($sheet);
                    $manager->flush();

                return $this->redirectToRoute('sheet_show', ['id' => $sheet->getId()]);


            }

            return $this->render('documentation/sheet/show.html.twig', [
                'category' => $sheet->getSubCategory()->getCategory(),
                'subCategory' => $sheet->getSubCategory(),
                'sheet' => $sheet,
                'form' => $form->createView()

            ]);

        }


        // Si la fiche n'est pas en cours de validation ou à corriger
        if($sheet->getStatus() === null){

            $views = $sheet->getViews();
            $views++;
            $sheet->setViews($views);

            $manager->persist($sheet);
            $manager->flush();

        }
            
        return $this->render('documentation/sheet/show.html.twig', [
            'category' => $sheet->getSubCategory()->getCategory(),
            'subCategory' => $sheet->getSubCategory(),
            'sheet' => $sheet
        ]);
    }

    /**
     * Permet d'afficher une seule annonce
     *
     * @Route("/documentation/sheet/{id}/delete", name="sheet_delete")
     * 
     * @IsGranted("ROLE_USER")
     * 
     */
    public function delete(Sheet $sheet, EntityManagerInterface $manager, SheetRepository $sheetRepo)
    {
        // Si c'est une fiche "En cours de validation"
        if($sheet->getStatus() == "TO_VALIDATE")
        {
            // Suppression de la fiche "En cours de validation"
            $sheet->setOrigin(null);
            $manager->remove($sheet);
            $manager->flush();

        }
        else{

            // Sinon c'est une fiche

            // Recherche d'une fiche "En cours de validation" associée à la fiche que l'on souhaite 
            $sheetToValidate = $sheetRepo->findOneByOrigin($sheet);

            // S'il existe une fiche "En cours de validation"
            if($sheetToValidate){

                // Suppression de la fiche "En cours de validation"
                $sheetToValidate->setOrigin(null);
                $manager->remove($sheetToValidate);
                $manager->flush();

            }

            // Ensuite, on supprime la fiche en question
            $manager->remove($sheet);
            $manager->flush();


        }
        

        $this->addFlash(
            'success',
            "La fiche <strong>{$sheet->getTitle()}</strong> a bien été supprimée !"

        );

        //  // Gestion des nouveaux slugs
         $slug = $sheet->getSubCategory()->getCategory()->getSlug();
         $subSlug = $sheet->getSubCategory()->getSlug();

         return $this->redirectToRoute('doc_show', ['slug' => $slug, 'sub_slug' => $subSlug]);


    }

    /**
     * Permet la gestion des outils d'une fiche
     * 
     * @Route("/documentation/{slug}/{sub_slug}/{sheet_slug}/sheet/tools/edit", name="sheet_tools")
     * 
     * @ParamConverter("subCategory", options={"mapping": {"sub_slug":   "slug"}})
     * @ParamConverter("sheet", options={"mapping": {"sheet_slug": "slug"}})
     * 
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function editTools(Category $category, SubCategory $subCategory, Sheet $sheet, Request $request, EntityManagerInterface $manager){

        $form = $this->createForm(ToolsType::class, $sheet);

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){
            
            foreach($sheet->getAttachments() as $attachment){

                $attachment->setSheet($sheet);
                $manager->persist($attachment);
                
            }
            
            $manager->persist($sheet);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les outils de la fiche <strong>{$sheet->getTitle()}</strong> ont bien été modifiés !"

            );

            // Gestion des nouveaux slugs
            $slug = $sheet->getSubCategory()->getCategory()->getSlug();
            $subSlug = $sheet->getSubCategory()->getSlug();

            // return $this->redirectToRoute('sheet_show', ['slug' => $slug, 'sub_slug' => $subSlug, 'sheet_slug' => $sheet->getSlug()]);

        }

        return $this->render('documentation/sheet/tools.html.twig', [
            'form'=> $form->createView(),
            'category' => $category,
            'subCategory' => $subCategory,
            'sheet' => $sheet
        ]);

    }

   

    
}
