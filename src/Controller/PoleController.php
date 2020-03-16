<?php

namespace App\Controller;

use App\Entity\Pole;
use App\Form\PoleType;
use App\Service\Color;
use App\Repository\PoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function create(Request $request, EntityManagerInterface $manager, Color $color)
    {
        $pole = new Pole();

        $form = $this->createForm(PoleType::class, $pole);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            // On sélectionne une couleur
            $pole->setColor($color->getPoleColor());

            $manager->persist($pole);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le pôle <strong>{$pole->getTitle()}</strong> a bien été créé !"

            );

            return $this->redirectToRoute('admin_poles_index');
        
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
            
            $manager->persist($pole);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le pôle <strong>{$pole->getTitle()}</strong> a bien été modifié !"

            );

            return $this->redirectToRoute('admin_poles_index');

        }

        return $this->render('admin/pole/edit.html.twig', [
            'form'=> $form->createView(),
            'category' => $pole
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

        return $this->redirectToRoute('admin_poles_index');


    }

}
