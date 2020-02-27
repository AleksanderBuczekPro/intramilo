<?php

namespace App\Controller;

use App\Entity\Groupe;
use App\Form\GroupeType;
use App\Service\Pagination;
use App\Repository\GroupeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GroupeController extends AbstractController
{
    /**
     * Permet d'afficher la liste de tous les groupes
     * 
     * @Route("/admin/groupes/{page<\d+>?1}", name="admin_groupes_index")
     */
    public function index(GroupeRepository $repo, $page, Pagination $pagination)
    {
        $pagination ->setEntityClass(Groupe::class)
                    ->setPage($page);

        return $this->render('admin/groupe/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet de créer un groupe
     * 
     * @Route("/admin/groupe/create", name="admin_groupe_create")
     *
     * @return Response
     * 
     */
    public function create(Request $request, EntityManagerInterface $manager) {

        $groupe = new Groupe();

        $form = $this->createForm(GroupeType::class, $groupe);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($groupe);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'antenne <strong>{$groupe->getTitle()}</strong> a bien été créée !"

            );

            return $this->redirectToRoute('admin_groupes_index');
        
        }

        return $this->render('admin/groupe/create.html.twig', [
            'form' => $form->createView()
        ]);

    }


    /**
     * Permet de modifer les informations d'un groupe
     * 
     * @Route("/admin/groupe/{id}/edit", name="admin_groupe_edit")
     *
     * @return Response
     */
    public function edit(Request $request, EntityManagerInterface $manager, Groupe $groupe) {

        $form = $this->createForm(GroupeType::class, $groupe);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($groupe);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le groupe <strong>{$groupe->getTitle()}</strong> a bien été modifié !"

            );

            return $this->redirectToRoute('admin_groupes_index');
        
        }

        return $this->render('admin/groupe/edit.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * Permet de supprimer un groupe
     *
     * @Route("/admin/groupe/{id}/delete", name="admin_groupe_delete")
     * 
     */
    public function delete(Groupe $groupe, EntityManagerInterface $manager)
    {
        $manager->remove($groupe);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le groupe <strong>{$groupe->getTitle()}</strong> a bien été supprimé !"

        );

        return $this->redirectToRoute('admin_groupes_index');



    }
}
