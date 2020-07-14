<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Form\OrganizationType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\OrganizationRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class OrganizationController extends AbstractController
{
    /**
     * Affiche la liste des organismes
     * 
     * @Route("/organizations", name="organizations_index")
     */
    public function index(OrganizationRepository $repo)
    {

        // $organizations = $repo->findAll();
        $organizations = $repo->findBy(array(), array('name' => 'asc'));
        

        return $this->render('organization/index.html.twig', [
            'organizations' => $organizations,
        ]);
    }

    /**
     * Permet de créer un organisme
     * 
     * @Route("/organization/create", name="organization_create")
     *
     * @return Response
     * 
     */
    public function create(Request $request, EntityManagerInterface $manager) {

        $organization = new Organization();

        $form = $this->createForm(OrganizationType::class, $organization);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $pictureFile = $form->get('logo')->getData();

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
                $oldFilename = $organization->getLogoFilename();
                if($oldFilename){
                    
                    $path = $this->getParameter('pictures_directory').'/'.$oldFilename;

                    $filesystem = new Filesystem();
                    $filesystem->remove($path);                    

                }

                $organization->setLogoFilename($newFilename);
                
            }

            $manager->persist($organization);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'organisme <strong>{$organization->getName()}</strong> a bien été créé !"

            );

            return $this->redirectToRoute('organizations_index');
        
        }

        return $this->render('organization/create.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * Permet de créer un organisme depuis une fiche
     * 
     * @Route("/organization/create/from-sheet", name="organization_create_from_sheet")
     *
     * @return Response
     * 
     */
    public function createFromSheet(Request $request, EntityManagerInterface $manager) {

        $organization = new Organization();

        $form = $this->createForm(OrganizationType::class, $organization);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $pictureFile = $form->get('logo')->getData();

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
                $oldFilename = $organization->getLogoFilename();
                if($oldFilename){
                    
                    $path = $this->getParameter('pictures_directory').'/'.$oldFilename;

                    $filesystem = new Filesystem();
                    $filesystem->remove($path);                    

                }

                $organization->setLogoFilename($newFilename);
                
            }

            $manager->persist($organization);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'organisme <strong>{$organization->getName()}</strong> a bien été créé !"

            );
        
        }

        return $this->render('organization/create.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * Permet de modifer les informations d'un organisme
     * 
     * @Route("/organization/{id}/edit", name="organization_edit")
     *
     * @return Response
     */
    public function edit(Request $request, EntityManagerInterface $manager, Organization $organization) {

        $form = $this->createForm(OrganizationType::class, $organization);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $pictureFile = $form->get('logo')->getData();

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
                $oldFilename = $organization->getLogoFilename();
                if($oldFilename){
                    
                    $path = $this->getParameter('pictures_directory').'/'.$oldFilename;

                    $filesystem = new Filesystem();
                    $filesystem->remove($path);                    

                }

                $organization->setLogoFilename($newFilename);
                
            }

            $manager->persist($organization);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'organisme <strong>{$organization->getName()}</strong> a bien été modifié !"

            );

            return $this->redirectToRoute('organizations_index');
        
        }

        return $this->render('organization/edit.html.twig', [
            'form' => $form->createView(),
            'organization' => $organization
        ]);

    }

    
    /**
     * Permet de supprimer un organisme
     *
     * @Route("organization/{id}/delete", name="organization_delete")
     * 
     */
    public function delete(Organization $organization, EntityManagerInterface $manager)
    {
        $size = sizeof($organization->getSheets()) + sizeof($organization->getDocuments());
        
        if($size == 0){

            $manager->remove($organization);
            $manager->flush();
    
            $this->addFlash(
                'success',
                "L'organisme <strong>{$organization->getName()}</strong> a bien été supprimé !"
    
            );

        }else{

            $this->addFlash(
                'danger',
                "L'organisme n'a pas pu être supprimé car des fiches et / ou des documents lui sont associés"
    
            );


        }

       

        return $this->redirectToRoute('organizations_index');



    }
}
