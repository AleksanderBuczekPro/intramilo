<?php

namespace App\Controller;

use App\Entity\Interlocutor;
use App\Entity\Organization;
use App\Form\InterlocutorType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\OrganizationRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InterlocutorController extends AbstractController
{

    /**
     * Permet de créer un interlocuteur
     * 
     * @Route("/interlocutor/{id}/create", name="interlocutor_create")
     *
     * @return Response
     * 
     */
    public function create(Request $request, EntityManagerInterface $manager, Organization $organization) {

        $interlocutor = new Interlocutor();

        $interlocutor->setOrganization($organization);

        $form = $this->createForm(InterlocutorType::class, $interlocutor);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($interlocutor);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'interlocuteur <strong>{$interlocutor->getFullName()}</strong> a bien été créé !"

            );

            return $this->redirectToRoute('organizations_index');
        
        }

        return $this->render('interlocutor/create.html.twig', [
            'form' => $form->createView(),
            'organization' => $organization
        ]);

    }

    /**
     * Permet de modifer les informations d'un interlocuteur
     * 
     * @Route("/interlocutor/{id}/edit", name="interlocutor_edit")
     * 
     *
     * @return Response
     */
    public function edit(Request $request, EntityManagerInterface $manager, Interlocutor $interlocutor) {

        $form = $this->createForm(InterlocutorType::class, $interlocutor);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($interlocutor);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'interlocuteur <strong>{$interlocutor->getFullName()}</strong> a bien été modifié !"

            );

            return $this->redirectToRoute('organizations_index');
        
        }

        return $this->render('interlocutor/edit.html.twig', [
            'form' => $form->createView(),
            'organization' => $interlocutor->getOrganization()
        ]);

    }

    /**
     * Permet de récupérer la liste des inetrlocuteurs pour un organisme
     * 
     * @Route("/interlocutor/load", name="interlocutor_load")
     * 
     *
     * @return Response
     */
    public function load(Request $request, EntityManagerInterface $manager, OrganizationRepository $repo) {

        // Récupération de l'organisme
        $id = $request->request->get('id');
        $organization = $repo->findOneById($id);
        
        // Récupération des interlocuteurs
        $interlocutors = $organization->getInterlocutors();
        

        // Conversion des objets en JSON avec un Serializer
        $encoder = [new JsonEncoder()];

        // Handling Circular References
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];

        $normalizer = [new ObjectNormalizer(null, null, null, null, null, null, $defaultContext)];

        $serializer = new Serializer($normalizer, $encoder);

        $jsonContent = $serializer->serialize($interlocutors, 'json');

        
        // On retourne du JSON
        return $this->json($jsonContent);

    }

    
    /**
     * Permet de supprimer un interlocuteur
     *
     * @Route("interlocutor/{id}/delete", name="interlocutor_delete")
     * 
     */
    public function delete(Interlocutor $interlocutor, EntityManagerInterface $manager)
    {
        $manager->remove($interlocutor);
        $manager->flush();

        $this->addFlash(
            'success',
            "L'interlocuteur <strong>{$interlocutor->getFullName()}</strong> a bien été supprimé !"

        );

        return $this->redirectToRoute('organizations_index');



    }
}
