<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Service\Pagination;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminUserController extends AbstractController
{
    /**
     * Permet d'afficher la liste de tous les utilisateurs
     * 
     * @Route("/admin/users/{page<\d+>?1}", name="admin_users_index")
     */
    public function index(UserRepository $repo, $page, Pagination $pagination)
    {
        $pagination ->setEntityClass(User::class)
                    ->setPage($page);

        return $this->render('admin/user/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet d'afficher le formulaire d'inscription d'un utilisateur
     * 
     * @Route("/admin/user/register", name="admin_user_register")
     *
     * @return Response
     */
    public function register(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder) {

        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            // Gestion du mot de passe
            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);

            
            // Gestion du statut administrateur
            // $admin = $request->request->get('isAdmin');
            // if(isset($admin)){

            //     $user->addUserRole('ROLE_ADMIN');

            // }else{

                
            //     $administrateur = "";

            // }

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'utilisateur <strong>{$user->getFirstname()} {$user->getLastname()}</strong> a bien été créé !"

            );

            return $this->redirectToRoute('admin_users_index');
        
        }

        return $this->render('admin/user/registration.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * Permet de modifer les informations d'un utilisateur
     * 
     * @Route("/admin/user/{id}/edit", name="admin_user_edit")
     *
     * @return Response
     */
    public function edit(Request $request, ObjectManager $manager, User $user) {

        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'utilisateur <strong>{$user->getFirstname()} {$user->getLastname()}</strong> a bien été modifié !"

            );

            return $this->redirectToRoute('admin_users_index');
        
        }

        return $this->render('admin/user/edit.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * Permet de supprimer un utilisateur
     *
     * @Route("/admin/user/{id}/delete", name="admin_user_delete")
     * 
     */
    public function delete(User $user, ObjectManager $manager)
    {
        $manager->remove($user);
        $manager->flush();

        $this->addFlash(
            'success',
            "L'utilisateur a bien été supprimé !"

        );

        return $this->redirectToRoute('admin_users_index');



    }
}
