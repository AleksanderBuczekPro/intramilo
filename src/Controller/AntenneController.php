<?php

namespace App\Controller;

use App\Entity\Antenne;
use App\Form\AntenneType;
use App\Service\Pagination;
use App\Repository\AntenneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AntenneController extends AbstractController
{
    /**
     * Permet d'afficher la liste de toutes les antennes
     * 
     * @Route("/admin/antennes/{page<\d+>?1}", name="admin_antennes_index")
     */
    public function index(AntenneRepository $repo)
    {
        $antennes = $repo->findBy(array(), array('title' => 'ASC'));

        return $this->render('admin/antenne/index.html.twig', [
            'antennes' => $antennes
        ]);
    }

    /**
     * Permet de créer une antenne
     * 
     * @Route("/admin/antenne/create", name="admin_antenne_create")
     *
     * @return Response
     * 
     */
    public function create(Request $request, EntityManagerInterface $manager) {

        $antenne = new Antenne();

        $form = $this->createForm(AntenneType::class, $antenne);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($antenne);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'antenne <strong>{$antenne->getTitle()}</strong> a bien été créée !"

            );

            return $this->redirectToRoute('admin_antennes_index');
        
        }

        return $this->render('admin/antenne/create.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * Permet de modifer les informations d'une antenne
     * 
     * @Route("/admin/antenne/{id}/edit", name="admin_antenne_edit")
     *
     * @return Response
     */
    public function edit(Request $request, EntityManagerInterface $manager, Antenne $antenne) {

        dump($antenne);

        $form = $this->createForm(AntenneType::class, $antenne);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($antenne);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'antenne <strong>{$antenne->getTitle()}</strong> a bien été modifiée !"

            );

            return $this->redirectToRoute('admin_antennes_index');
        
        }

        return $this->render('admin/antenne/edit.html.twig', [
            'form' => $form->createView(),
            'antenne' => $antenne
        ]);

    }

    /**
     * Permet de supprimer une antenne
     *
     * @Route("/admin/antenne/{id}/delete", name="admin_antenne_delete")
     * 
     */
    public function delete(Antenne $antenne, EntityManagerInterface $manager)
    {
        $manager->remove($antenne);
        $manager->flush();

        $this->addFlash(
            'success',
            "L'antenne <strong>{$antenne->getTitle()}</strong> a bien été supprimée !"

        );

        return $this->redirectToRoute('admin_antennes_index');



    }


    /**
     * Permet d'afficher la liste des antennes
     * 
     * @Route("/antennes", name="antennes_show")
     *
     * @return void
     */
    public function show(AntenneRepository $repo){

        $antennes = $repo->findBy(array(), array('title' => 'ASC'));

        return $this->render('antenne/show.html.twig', [
            'antennes' => $antennes
        ]);


    }

}
