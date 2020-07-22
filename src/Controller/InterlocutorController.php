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
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;

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

            return $this->redirectToRoute('organizations_index', [ 'id' => $interlocutor->getOrganization()->getId() ]);
        
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

            return $this->redirectToRoute('organizations_index' , [ 'id' => $interlocutor->getOrganization()->getId() ]);
        
        }

        return $this->render('interlocutor/edit.html.twig', [
            'form' => $form->createView(),
            'organization' => $interlocutor->getOrganization(),
            'interlocutor' => $interlocutor
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

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new PropertyNormalizer($classMetadataFactory);
        $serializer = new Serializer([$normalizer]);

        // $inter_data = $serializer->normalize($interlocutors, null, ['groups' => ['default']]);
        $data = $serializer->normalize($interlocutors, null, [AbstractNormalizer::ATTRIBUTES => ['id', 'firstName', 'lastName', 'function', 'phoneNumber', 'email','sheets' => ['id']]]);

        
        // On retourne du JSON
        return $this->json($data);

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

        return $this->redirectToRoute('organizations_index', [ 'id' => $interlocutor->getOrganization()->getId() ]);



    }
}
