<?php

namespace App\Controller;

use App\Entity\Pole;
use App\Form\PoleType;
use App\Service\Color;
use App\Repository\PoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class PoleController extends AbstractController
{

    /**
     * Permet d'afficher la liste de tous les poles
     * 
     * @Route("/admin/poles", name="admin_poles_index")
     */
    public function index(PoleRepository $repo)
    {
        $poles =$repo->findAll();

        return $this->render('admin/pole/index.html.twig', [
            'poles' => $poles
        ]);
    }

    /**
     * @Route("/admin/pole/add", name="admin_pole_create")
     */
    public function create(Request $request, EntityManagerInterface $manager, Color $color, PoleRepository $repo)
    {
        $pole = new Pole();

        $form = $this->createForm(PoleType::class, $pole);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

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
                $oldFilename = $pole->getPictureFilename();
                if($oldFilename){
                    
                    $path = $this->getParameter('pictures_directory').'/'.$oldFilename;

                    $filesystem = new Filesystem();
                    $filesystem->remove($path);                    

                }

                $pole->setPictureFilename($newFilename);
                
            }

            // On sélectionne une couleur
            $pole->setColor($color->getPoleColor());
            $pole->setPlace(count($repo->findAll()));

            $manager->persist($pole);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le pôle <strong>{$pole->getTitle()}</strong> a bien été créé !"

            );

            return $this->redirectToRoute('admin_documentation_index');
        
        }

        return $this->render('admin/pole/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier un pôle
     * 
     * @Route("/admin/pole/{id}/edit", name="admin_pole_edit")
     */
    public function edit(Pole $pole, Request $request, EntityManagerInterface $manager)
    {

        $form = $this->createForm(PoleType::class, $pole);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

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
                $oldFilename = $pole->getPictureFilename();
                if($oldFilename){
                    
                    $path = $this->getParameter('pictures_directory').'/'.$oldFilename;

                    $filesystem = new Filesystem();
                    $filesystem->remove($path);                    

                }

                $pole->setPictureFilename($newFilename);
                
            }
            
            $manager->persist($pole);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le pôle <strong>{$pole->getTitle()}</strong> a bien été modifié !"

            );

            return $this->redirectToRoute('admin_documentation_index');

        }

        return $this->render('admin/pole/edit.html.twig', [
            'form'=> $form->createView(),
            'pole' => $pole
        ]);
    }

    /**
     * Permet de supprimer un pôle
     * 
     * @Route("/admin/pole/{id}/delete", name="admin_pole_delete")
     */
    public function delete(Pole $pole, EntityManagerInterface $manager)
    {

        $manager->remove($pole);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le pôle <strong>{$pole->getTitle()}</strong> a bien été supprimé !"

        );

        return $this->redirectToRoute('admin_documentation_index');


    }

     /**
     * Permet de trier l'ordre des Pôles
     * 
     * @Route("/admin/pole/sort", name="admin_pole_sort")
     */
    public function sort(PoleRepository $repo, Request $request)
    {

        // Interlocutors
        // Récupération de toutes les variables POST
        $test = $request->request->get('test');

        dump($test);

        if($request->request->get('test')){
            dump("ok");
        }
        // Pour chaque variable POST
        // foreach($data as $key => $val) {

        //     // Si la clé contient 'interlocutor'
        //     if (strpos($key, 'interlocutor') !== false) {
                
        //         // On ajoute l'interlocuteur
        //         $sheet->addInterlocutor($repo->findOneById($val));

        //     }

        // }
        // $manager->remove($pole);
        // $manager->flush();

        // $this->addFlash(
        //     'success',
        //     "Le pôle <strong>{$pole->getTitle()}</strong> a bien été supprimé !"

        // );

        // return $this->redirectToRoute('admin_documentation_sort');

        $poles = $repo->findAll();

        return $this->render('admin/pole/sort.html.twig', [
            'poles' => $poles
        ]);


    }

}
