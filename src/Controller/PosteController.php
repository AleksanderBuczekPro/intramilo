<?php

namespace App\Controller;

use App\Entity\Poste;
use App\Form\PosteType;
use App\Repository\PosteRepository;
use App\Service\Pagination;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PosteController extends AbstractController
{
    /**
     * Permet d'afficher la liste de tous les postes
     * 
     * @Route("/admin/postes", name="admin_postes_index")
     */
    public function index(PosteRepository $repo)
    {
        $postes = $repo->findAll();
        
        return $this->render('admin/poste/index.html.twig', [
            'postes' => $postes
        ]);
    }

    /**
     * Permet de créer un poste
     * 
     * @Route("/admin/poste/create", name="admin_poste_create")
     *
     * @return Response
     * 
     */
    public function create(Request $request, EntityManagerInterface $manager) {

        $poste = new Poste();

        $form = $this->createForm(PosteType::class, $poste);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($poste);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'antenne <strong>{$poste->getTitle()}</strong> a bien été créée !"

            );

            return $this->redirectToRoute('admin_postes_index');
        
        }

        return $this->render('admin/poste/create.html.twig', [
            'form' => $form->createView()
        ]);

    }


    /**
     * Permet de modifer les informations d'un poste
     * 
     * @Route("/admin/poste/{id}/edit", name="admin_poste_edit")
     *
     * @return Response
     */
    public function edit(Request $request, EntityManagerInterface $manager, Poste $poste) {

        $form = $this->createForm(PosteType::class, $poste);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($poste);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le poste <strong>{$poste->getTitle()}</strong> a bien été modifié !"

            );

            return $this->redirectToRoute('admin_postes_index');
        
        }

        return $this->render('admin/poste/edit.html.twig', [
            'form' => $form->createView(),
            'poste' => $poste
        ]);

    }

    /**
     * Permet de supprimer un poste
     *
     * @Route("/admin/poste/{id}/delete", name="admin_poste_delete")
     * 
     */
    public function delete(Poste $poste, EntityManagerInterface $manager)
    {
        $manager->remove($poste);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le poste <strong>{$poste->getTitle()}</strong> a bien été supprimé !"

        );

        return $this->redirectToRoute('admin_postes_index');



    }
}
