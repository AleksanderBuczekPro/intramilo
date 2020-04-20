<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Pagination;
use App\Form\AdminAccountType;
use App\Form\RegistrationType;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Repository\GroupeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminUserController extends AbstractController
{
    /**
     * Permet d'afficher la liste de tous les utilisateurs
     * 
     * @Route("/admin/users", name="admin_users_index")
     */
    public function index(GroupeRepository $repo)
    {
        $groupes = $repo->findBy(array(), array('title' => 'ASC'));

        return $this->render('admin/user/index.html.twig', [
            'groupes' => $groupes
    
        ]);
    }

    /**
     * Permet d'afficher le formulaire d'inscription d'un utilisateur
     * 
     * @Route("/admin/user/register", name="admin_user_register")
     *
     * @return Response
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder) {

        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            // Gestion du mot de passe
            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);

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
    public function edit(Request $request, EntityManagerInterface $manager, User $user, RoleRepository $repo) {

        $form = $this->createForm(AdminAccountType::class, $user);

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
            'form' => $form->createView(),
            'user' => $user
        ]);

    }

    /**
     * Permet de supprimer un utilisateur
     *
     * @Route("/admin/user/{id}/delete", name="admin_user_delete")
     * 
     */
    public function delete(User $user, EntityManagerInterface $manager)
    {
        $manager->remove($user);
        $manager->flush();

        $this->addFlash(
            'success',
            "L'utilisateur a bien été supprimé !"

        );

        return $this->redirectToRoute('admin_users_index');



    }

    /**
     * Permet de d'assigner le role administrateur à un utlisateur
     *
     * @Route("/admin/user/admin/add", name="admin_user_admin_add")
     * 
     */
    public function admin(EntityManagerInterface $manager, RoleRepository $roleRepo, UserRepository $userRepo, Request $request)
    {
        // Solution temporaire : récupération du role admin
        $adminRole = $roleRepo->findOneByTitle("ROLE_ADMIN");

        // Récupération du User
        $id = $request->request->get('id');
        $user = $userRepo->findOneById($id);


        $user->addUserRole($adminRole);


        $manager->persist($user);
        $manager->flush();

        return $this->redirectToRoute('admin_users_index');



    }

    /**
     * Permet de d'assigner le role administrateur à un utlisateur
     *
     * @Route("/admin/user/admin/remove", name="admin_user_admin_remove")
     * 
     */
    public function user(EntityManagerInterface $manager, RoleRepository $roleRepo, UserRepository $userRepo, Request $request)
    {
        // Solution temporaire : récupération du role admin
        $adminRole = $roleRepo->findOneByTitle("ROLE_ADMIN");
        
        // Récupération du User
        $id = $request->request->get('id');
        $user = $userRepo->findOneById($id);

        $user->removeUserRole($adminRole);

        $manager->persist($user);
        $manager->flush();

        return $this->redirectToRoute('admin_users_index');



    }

}
