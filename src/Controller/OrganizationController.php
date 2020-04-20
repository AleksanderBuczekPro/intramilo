<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Form\OrganizationType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\OrganizationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

            $manager->persist($organization);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'organisme <strong>{$organization->getName()}</strong> a bien été modifié !"

            );

            return $this->redirectToRoute('organizations_index');
        
        }

        return $this->render('organization/edit.html.twig', [
            'form' => $form->createView()
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
        $manager->remove($organization);
        $manager->flush();

        $this->addFlash(
            'success',
            "L'organisme <strong>{$organization->getName()}</strong> a bien été supprimé !"

        );

        return $this->redirectToRoute('organizations_index');



    }
}
