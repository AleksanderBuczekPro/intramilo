<?php

namespace App\Controller;

use App\Entity\Pole;
use App\Form\PoleType;
use App\Service\Color;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PoleController extends AbstractController
{
    /**
     * @Route("/admin/pole/add", name="admin_pole_create")
     */
    public function create(Request $request, ObjectManager $manager, Color $color)
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

            return $this->redirectToRoute('admin_category_index');
        
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
    public function editCategory(Pole $pole, Request $request, ObjectManager $manager)
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

            return $this->redirectToRoute('admin_category_index');

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
    public function deleteCategory(Pole $pole, ObjectManager $manager)
    {

        $manager->remove($pole);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le pole <strong>{$pole->getTitle()}</strong> a bien été supprimé !"

        );

        return $this->redirectToRoute('admin_category_index');


    }

}
