<?php

namespace App\Controller;

use DateTimeZone;
use App\Entity\Sheet;
use App\Service\Menu;
use App\Entity\Header;
use App\Entity\Section;
use App\Form\SheetType;
use App\Form\ToolsType;
use App\Entity\Category;
use App\Form\AuthorType;
use App\Form\CommentType;
use App\Entity\SubCategory;
use App\Entity\Interlocutor;
use App\Entity\Organization;
use App\Form\AttachmentType;
use App\Form\OrganizationType;
use PhpParser\Node\Stmt\Foreach_;
use App\Repository\SheetRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SubCategoryRepository;
use App\Repository\InterlocutorRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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

        dump($request->request->get('paragraphs'));

        // On remplace le texte par le texte formaté (sans couleurs)
        // $sheet->setContent($request->request->get('content'));

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
     * Fonction permettant de dupliquer une fiche (Brouillon et Envoyer à valider)
     */
    public function duplicate($sheet, $manager, $status){

        // On duplique la fiche
        $sheetDuplicated = clone $sheet;
        
        // Init
        $sheetDuplicated->setOrigin($sheet);
        $sheetDuplicated->setStatus($status);

        // On duplique toutes les relations
        
        // Paragraphs
        // $place = 1;
        foreach($sheet->getParagraphs() as $paragraph){

            $p = clone $paragraph;

            // $p->setPlace($place);
            $p->setSheet($sheetDuplicated);
            $manager->persist($p);

            // $place = $place + 1;
            
        }

        // A VERIFIER
        // Entêtes
        foreach($sheetDuplicated->getHeaders() as $header){

            $h = clone $header;

            $h->setSheet($sheetDuplicated);
            $manager->persist($h);

            foreach($header->getSections() as $section){

                $s = clone $section;

                $s->setHeader($h);
                $manager->persist($s);

            }
            
        }

        // Attachments
        foreach($sheetDuplicated->getAttachments() as $attachment){

            $a = clone $attachment;

            $a->setSheet($sheetDuplicated);
            $manager->persist($a);
            
        }




        $manager->refresh($sheet);

        $sheetDuplicated->setUpdatedAt(new \DateTime(null, new DateTimeZone('Europe/Paris')));
  
        $manager->persist($sheetDuplicated);
        $manager->flush();

        return $sheetDuplicated->getId();


    }

    /**
     * Permet d'ajouter une fiche dans une sous-catégorie spécifique
     * 
     * @Route("/documentation/{id}/sheet/new", name="sheet_create_sub")
     *
     * @Route("/documentation/sheet/new/model/{id_model}", name="sheet_create_model")
     * @Route("/documentation/sheet/new", name="sheet_create")
     * 
     * @ParamConverter("sheetFromModel", options={"mapping": {"id_model": "id"}})
     * 
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     * 
     */
    public function create(SubCategory $subCategory = null, Sheet $sheetFromModel = null, Request $request, EntityManagerInterface $manager, SubCategoryRepository $subRepo, InterlocutorRepository $repo) {
        
        
        $sheet = new Sheet();

        // Si c'est depuis un modèle
        if($sheetFromModel){
            
            $subCategory = $sheetFromModel->getSubCategory();
            $sheet->setSubCategory($subCategory);

            // On duplique les RELATIONS
            // Paragraphs
            foreach($sheetFromModel->getParagraphs() as $p){
                
                $paragraph = clone $p;
                $paragraph->setSheet($sheet);

                $sheet->addParagraph($paragraph);
                
            }
            
            // Headers
            foreach($sheetFromModel->getHeaders() as $h){
                
                // $sheet->addHeader($header);

                $header = new Header();
                $header->setTitle($h->getTitle());
                $header->setSheet($sheet);

                $sheet->addHeader($header);

                foreach($h->getSections() as $s){
 
                    // Création de la nouvelle section
                    $section = new Section();
                    $section->setTitle($s->getTitle());
                    $section->setContent($s->getContent());

                    $section->setHeader($header);

                    $header->addSection($section);
                }
                
            }


        
        }else{

            if($subCategory){
                $sheet->setSubCategory($subCategory);  
            }

        
        }

        // Ajout dynamique d'un organisme
        $organization = new Organization();
        $organizationForm = $this->createForm(OrganizationType::class, $organization);

        // $organizationForm->handleRequest($request);

        // if($organizationForm->isSubmitted() && $organizationForm->isValid()){

        //     $manager->persist($organization);
        //     $manager->flush();

        //     $this->addFlash(
        //         'success',
        //         "L'organisme <strong>{$organization->getName()}</strong> a bien été créé !"

        //     );

        // }

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

            // Paragraphs
            $place = 1;
            foreach($sheet->getParagraphs() as $p){

                $p->setPlace($place);
                $p->setSheet($sheet);
                $manager->persist($p);

                $place = $place + 1;
                
            }


            // Attachments
            foreach($sheet->getAttachments() as $attachment){

                $attachment->setSheet($sheet);
                $attachment->setAuthor($this->getUser());
                $attachment->setSubCategory($sheet->getSubCategory());

                $manager->persist($attachment);
                
            }

            // Headers
            foreach($sheet->getHeaders() as $header){



                $header->setSheet($sheet);
                $manager->persist($header);

                foreach($header->getSections() as $section){

                    $section->setHeader($header);
                    $manager->persist($section);

                }
                
            }
            
            // Init
            $sheet->setFront('0');
            $sheet->setViews(0);
            $sheet->setOrigin(null);
            $sheet->setAuthor($this->getUser());

            $sheet->setUpdatedAt(new \DateTime(null, new DateTimeZone('Europe/Paris')));
            
            
            // // Enregistrement en tant que brouillon
            if($form->get('saveDraft')->isClicked() || $form->get('saveDraftExit')->isClicked()){
                
                $sheet->setStatus("DRAFT");

            }else{

                // user
                if(!$this->isGranted("ROLE_ADMIN")){

                    $sheet->setStatus("TO_VALIDATE");
                    
                
                }else{ // admin

                    $sheet->setStatus(null);

                }

            }
            

            $pictureFile = $form->get('pic')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($pictureFile) {

                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pictureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $pictureFile->move(
                        $this->getParameter('pictures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents

                // Si une photo de profil existe déjà, on la supprime
                $oldFilename = $sheet->getPictureFilename();
                if($oldFilename){
                    
                    $path = $this->getParameter('pictures_directory').'/'.$oldFilename;

                    $filesystem = new Filesystem();
                    $filesystem->remove($path);                    

                }

                $sheet->setPictureFilename($newFilename);
                
            }

            $manager->persist($sheet);
            $manager->flush();
        


            // Redirection conditionnelle
            if(!$form->get('saveDraft')->isClicked()){

                $this->addFlash(
                    'success',
                    "La fiche <strong>{$sheet->getTitle()}</strong> a bien été créée !"
    
                );
            
                // Redirection fiche
                return $this->redirectToRoute('sheet_show', ['id' => $sheet->getId()]);

            }else{

                // Redirection formulaire
                return $this->redirectToRoute('sheet_edit', ['id' => $sheet->getId()]);

            }

        }

        return $this->render('documentation/sheet/create.html.twig', [
            'form' => $form->createView(),
            'orgForm' => $organizationForm->createView(),
            'sheet' => $sheet
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
    public function edit(Sheet $sheet, EntityManagerInterface $manager, Request $request, InterlocutorRepository $repo, SheetRepository $sheetRepo){

        $form = $this->createForm(SheetType::class, $sheet, array('user' => $this->getUser()));

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            // Récupération de toutes les variables POST
            $data = $request->request->all();
        

            $default = false;
            foreach($data as $key => $val) {

                // Si la clé contient 'interlocutor'
                if (strpos($key, 'default_pic') !== false) {
                    
                    $default = true;

                }

            }

            $pictureFile = $form->get('pic')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($pictureFile) {

                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pictureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $pictureFile->move(
                        $this->getParameter('pictures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents

                // Si une photo de profil existe déjà, on la supprime
                $oldFilename = $sheet->getPictureFilename();
                if($oldFilename){
                    
                    $path = $this->getParameter('pictures_directory').'/'.$oldFilename;

                    $filesystem = new Filesystem();
                    $filesystem->remove($path);                    

                }

                $sheet->setPictureFilename($newFilename);
                
            }

            // Image de fond par défaut
            if($default){

                $oldFilename = $sheet->getPictureFilename();
                if($oldFilename){
                    
                    $path = $this->getParameter('pictures_directory').'/'.$oldFilename;

                    $filesystem = new Filesystem();
                    $filesystem->remove($path);                    

                }

                $sheet->setPictureFilename(null);

            }

            // Paragraphs
            $place = 1;
            foreach($sheet->getParagraphs() as $p){

                $p->setPlace($place);
                $p->setSheet($sheet);
                $manager->persist($p);

                $place = $place + 1;
                
            }

            dump($sheet);
            // Attachments
            foreach($sheet->getAttachments() as $attachment){



                $attachment->setSheet($sheet);
                $attachment->setAuthor($this->getUser());
                $attachment->setSubCategory($sheet->getSubCategory());

                $manager->persist($attachment);
                
            }
    

            // Headers & Sections
            foreach($sheet->getHeaders() as $header){


                $header->setSheet($sheet);
                $manager->persist($header);

                foreach($header->getSections() as $section){

                    $section->setHeader($header);
                    $manager->persist($section);

                }
                
            }

            // Datetime
            $sheet->setUpdatedAt(new \DateTime(null, new DateTimeZone('Europe/Paris')));

            // Initialisation des interlocuteurs
            foreach($sheet->getInterlocutors() as $interlocutor){

                $sheet->removeInterlocutor($interlocutor);
            
            }

                // Interlocutors
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

            // CONDITIONS
            // Quelle action ?
            if($form->get('saveDraft')->isClicked() || $form->get('saveDraftExit')->isClicked()){

                $action = 'draft';

            }else{

                if($form->get('sendToCorrect')->isClicked()){
                    
                    $action = 'to_correct';


                }else{

                    $action = 'to_validate';

                }

            }

            // EN LIGNE
            if($sheet->getStatus() == null){
                
                // Brouillon
                if($action == "draft"){

                    

                    // DUPLICATA
                    $duplicate = $this->duplicate($sheet, $manager, 'DRAFT');
                    return $this->redirectToRoute('sheet_edit', ['id' => $duplicate]);


                }else{
                // Envoyer à valider
                    // Admin
                    if($this->isGranted("ROLE_ADMIN")){
                        

                    // User
                    }else{

                        // S'il y a déjà un brouillon enregistré
                        $draft = $sheetRepo->findOneByOrigin($sheet->getId());

                        dump($draft);
                        if($draft){
                            
                            $draft->setOrigin(null);
                            $manager->remove($draft);
                            $manager->flush();

                        }

                        // DUPLICATA
                        $duplicate = $this->duplicate($sheet, $manager, 'TO_VALIDATE');
                        return $this->redirectToRoute('sheet_show', ['id' => $duplicate]);


                    }

                }

            }

             // A CORRIGER
             if($sheet->getStatus() == "TO_CORRECT"){

                // Brouillon (bouton désactivé)
                if($action == "draft"){

                    $sheet->setStatus('DRAFT');


                }else{
                // Envoyer à valider
                    // Admin
                    if($this->isGranted("ROLE_ADMIN")){
                        
                        // On valide la fiche
                        $sheet->setStatus(null);

                    // User
                    }else{
                        
                        // On garde le status TO_VALIDATE
                        $sheet->setStatus("TO_VALIDATE");
                    }

                }

            }


            // EN ATTENTE DE VALIDATION / A VALIDER
            if($sheet->getStatus() == "TO_VALIDATE"){

                // Brouillon (bouton désactivé)
                if($action == "draft"){

                    $sheet->setStatus('DRAFT');

                }else{
                             
                    // Admin
                    if($this->isGranted("ROLE_ADMIN")){
                        
                        // A corriger
                        if($action == "to_correct"){

                            $sheet->setStatus("TO_CORRECT");

                        }else{

                            // Envoyer à valider
                            // On valide la fiche
                            
                            $sheet->setStatus(null);

                        }
                       

                        
                    // User
                    }else{
                        
                        // On garde le status TO_VALIDATE

                    }

                }

            }

           
            // BROUILLON
            if($sheet->getStatus() == "DRAFT"){

                // Brouillon
                if($action == "draft"){

                    // On garde le status DRAFT

                }else{
                // Envoyer à valider
                    // Admin
                    if($this->isGranted("ROLE_ADMIN")){
                        


                        // On supprime l'ancienne fiche
                        $oldSheet = $sheetRepo->findOneById($sheet->getOrigin());

                        dump($oldSheet);
                        if($oldSheet){
                            
                            $oldSheet->setOrigin(null);
                            $manager->remove($oldSheet);


                        }

                        // On valide la fiche
                        $sheet->setStatus(null);
                        $sheet->setOrigin(null);

                        $manager->flush();


                    // User
                    }else{
                        
                        // On garde le status TO_VALIDATE
                        $sheet->setStatus("TO_VALIDATE");

                    }

                }

            }


            $manager->persist($sheet);
            $manager->flush();


            // // Si c'est une fiche "En cours de validation" que l'on modifie
            // if($sheet->getStatus() == "TO_VALIDATE"){

            //     $sheet->setUpdatedAt(new \DateTime(null, new DateTimeZone('Europe/Paris')));

            //     $manager->persist($sheet);
            //     $manager->flush();

            // }else{
            //     // Sinon c'est une fiche à corriger
            //     if($sheet->getStatus() == "TO_CORRECT"){
                    
            //         $sheet->setStatus("TO_VALIDATE");

            //         // $sheet->setUpdatedAt(new \DateTime(null, new DateTimeZone('Europe/Paris')));
            //         $manager->persist($sheet);
            //         $manager->flush();

            //     }else{


            //         if($form->get('saveDraft')->isClicked() || $form->get('saveDraftExit')->isClicked()){
                
            //             $sheet->setStatus("DRAFT");
        
            //         }else{
        
            //             // user
            //             if(!$this->isGranted("ROLE_ADMIN")){
            //                 $sheet->setStatus("TO_VALIDATE");
                        
            //             }else{ // admin
        
            //                 $sheet->setStatus(null);
        
            //             }
        
            //         }

            //         // Sinon c'est une fiche qui vient d'être modifiée

            //         // Si c'est l'admin, on valide directement
            //         if($this->isGranted("ROLE_ADMIN") && $sheet->getStatus() != 'DRAFT'){

            //             $manager->persist($sheet);
            //             $manager->flush();


            //         }else{

            //             dump($sheet->getStatus());
                        
            //             // DUPLICATION
            //             if($sheet->getStatus() != 'DRAFT'){

            //                 $this->duplicate($sheet, $manager);
                        
            //             }


            //         }
                    
            //         $manager->flush();

            //     }
               

            // }

            // dump($sheet->getStatus());

             // Redirection conditionnelle
            //  if($action != 'draft'){
            if(!$form->get('saveDraft')->isClicked()){
                $this->addFlash(
                    'success',
                    "La fiche <strong>{$sheet->getTitle()}</strong> a bien été modifiée !"
    
                );
            
                // Gestion des nouveaux slugs
                return $this->redirectToRoute('sheet_show', ['id' => $sheet->getId()]);

            }else{
                
                $id = $sheet->getId();

                // Gestion des nouveaux slugs
                return $this->redirectToRoute('sheet_edit', ['id' => $id]);

            }

        }

        $subCategory = $sheet->getSubcategory();
        $category = $sheet->getSubCategory()->getCategory();

        $draft = $sheetRepo->findOneByOrigin($sheet->getId());

        return $this->render('documentation/sheet/edit.html.twig', [
            'form'=> $form->createView(),
            'category' => $category,
            'subCategory' => $subCategory,
            'sheet' => $sheet,
            'draft' => $draft
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

        // True
        if($sheet->getFront() == '0'){

            $sheet->setFront('1');
            $sheet->setPublishedAt(new \DateTime(null, new DateTimeZone('Europe/Paris')));
            $sheet->setFrontAuthor($this->getUser());
        
        // False
        }else{

            $sheet->setFront('0');
            $sheet->setPublishedAt(null);
            $sheet->setFrontAuthor(null);

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
        // Si c'est une fiche "En cours de validation" ou Brouillon
        if($sheet->getStatus() == "TO_VALIDATE" || $sheet->getStatus() == "DRAFT")
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

         return $this->redirectToRoute('account_index');


    }

    /**
     * Permet de changer l'auteur d'une fiche
     *
     * @Route("/documentation/sheet/{id}/author", name="sheet_change_author")
     * 
     * @IsGranted("ROLE_ADMIN")
     * 
     */
    public function author(Sheet $sheet, EntityManagerInterface $manager, SheetRepository $sheetRepo, Request $request)
    {
        $form = $this->createForm(AuthorType::class, $sheet);       

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($sheet);
            $manager->flush();

            $this->addFlash(
                'success',
                "Auteur de la fiche changé avec succès !"

            );

            return $this->redirectToRoute('sheet_show', ['id' => $sheet->getId()]);

        }
        
        return $this->render('documentation/sheet/author.html.twig', [

            'form'=> $form->createView(),
            'sheet' => $sheet

        ]);

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
