<?php

namespace App\Controller;

use App\Entity\Parameters;
use App\Form\ParametersType;
use App\Repository\ParametersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AdminParametersController extends AbstractController
{
    /**
     * @Route("/admin/parameters", name="admin_parameters_index")
     */
    public function index(Request $request, EntityManagerInterface $manager, ParametersRepository $repo)
    {
        $parameters = $repo->find(1);

        if(!$parameters){

            $parameters = new Parameters;

        }

        $form = $this->createForm(ParametersType::class, $parameters);

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
                $oldFilename = $parameters->getLogoFilename();
                if($oldFilename){
                    
                    $path = $this->getParameter('pictures_directory').'/'.$oldFilename;

                    $filesystem = new Filesystem();
                    $filesystem->remove($path);                    

                }

                $parameters->setLogoFilename($newFilename);
            }

            $manager->persist($parameters);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les parametres ont été mis à jour avec succés !"

            );

            return $this->redirectToRoute('admin_parameters_index');
        
        }

        return $this->render('admin/parameters/index.html.twig', [
            'form' => $form->createView(),
            'parameters' => $parameters
        ]);
    }
}
