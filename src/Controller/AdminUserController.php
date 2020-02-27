<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Pagination;
use App\Form\AdminAccountType;
use App\Form\RegistrationType;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
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
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder) {

        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            // Gestion du mot de passe
            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);

            $user->setPicture("https://media-exp1.licdn.com/dms/image/C5603AQHu7cPy83UvbA/profile-displayphoto-shrink_100_100/0?e=1585785600&v=beta&t=qLyCkqMYn87M4pG_DFxBhoxNtIbPnIJhn3VLAxBU_Sk");
            $user->setDescription("description"); 
            
            // Gestion du statut administrateur
            // $admin = $request->request->get('isAdmin');
            // if(isset($admin)){

            //     $user->addUserRole('ROLE_ADMIN');

            // }else{

                
            //     $administrateur = "";

            // }

            $pictureFile = $form->get('pic')->getData();

            // this condition is needed because the 'brochure' field is not required

            // so the PDF file must be processed only when a file is uploaded
            if ($pictureFile) {

                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pictureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $pictureFile->move(
                        $this->getParameter('pictures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $user->setPictureFilename($newFilename);
            }

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

            // Gestion de la photo de profil

            $pictureFile = $form->get('pic')->getData();

            // this condition is needed because the 'brochure' field is not required

            // so the PDF file must be processed only when a file is uploaded
            if ($pictureFile) {

                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pictureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $pictureFile->move(
                        $this->getParameter('pictures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $user->setPictureFilename($newFilename);
            }

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
