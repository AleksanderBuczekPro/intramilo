<?php

namespace App\Controller;

use App\Entity\Website;
use App\Form\WebsiteType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WebsiteController extends AbstractController
{
    /**
     * Permet la gestion des sites web préférés de chaque utilisateur
     * 
     * @Route("/website", name="website_index")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function index(Request $request, ObjectManager $manager, UserRepository $userRepo)
    {

        $website = new Website;

        $form = $this->createForm(WebsiteType::class, $website);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $website->setAuthor($this->getUser());

            $manager->persist($website);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le site <strong>{$website->getTitle()}</strong> a bien été ajouté !"

            );

            return $this->redirectToRoute('website_index');
        
        }

        return $this->render('website/index.html.twig', [
            'form' => $form->createView(),
            'websites' => $this->getUser()->getWebsites()
        ]);
    }

    /**
     * Permet de modifier le nom et l'url d'un site web favori
     * 
     * @Route("/website/update/{id}", name="website_update")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function update(Website $website, Request $request, ObjectManager $manager, UserRepository $userRepo)
    {

        $form = $this->createForm(WebsiteType::class, $website);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $website->setAuthor($this->getUser());

            $manager->persist($website);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le site <strong>{$website->getTitle()}</strong> a bien été modifié !"

            );

            return $this->redirectToRoute('website_index');
        
        }

        return $this->render('website/update.html.twig', [
            'form' => $form->createView(),
            'websites' => $this->getUser()->getWebsites()
        ]);
    }

    /**
     * Permet de supprimer un site web favori
     * 
     * @Route("/website/delete/{id}", name="website_delete")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function delete(Website $website, ObjectManager $manager)
    {

            $manager->remove($website);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le site <strong>{$website->getTitle()}</strong> a bien été supprimé !"

            );

            return $this->redirectToRoute('website_index');
        
        
    }
}
