<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Sheet;
use App\Form\AuthorType;
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
     * Permet de réaffecter la documentation d'un utilisateur
     *
     * @Route("/admin/user/{id}/reaffect", name="admin_user_reaffect")
     * 
     */
    public function reaffect(User $user, EntityManagerInterface $manager, Request $request, UserRepository $repo)
    {
        $sheet = new Sheet();

        $form = $this->createForm(AuthorType::class, $sheet);       

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $newAuthor = $sheet->getAuthor();

            foreach($user->getSheets() as $s){

                $s->setAuthor($newAuthor);
                $manager->persist($s);
            }

            foreach($user->getDocuments() as $d){

                $d->setAuthor($newAuthor);
                $manager->persist($d);
            }

            foreach($user->getSubCategories() as $sub){
                
                $isAlready = false;
                
                foreach($sub->getAuthors() as $a){

                    // Si le nouvel auteur a déjà l'autorisation dans la nouvelle sous-catégorie
                    if($a == $newAuthor){

                        $isAlready = true;

                    }

                    // Si l'ancien auteur a les droits dans une sous-catégorie, on l'enlève
                    if($a == $user){

                        $sub->removeAuthor($user);

                        $manager->persist($sub);

                    }
                    
                }
                
                // S'il n'y est pas
                if(!$isAlready){

                    $sub->addAuthor($newAuthor);
                    $manager->persist($sub);

                }
            }


            $manager->flush();

            $this->addFlash(
                'success',
                "La documentation de <strong>" . $user->getFullName() . "</strong> a été réaffectée à <strong>" . $newAuthor->getFullName() . "</strong> avec succès !"

            );

            return $this->redirectToRoute('admin_users_index');

        }

        
        return $this->render('admin/user/reaffect.html.twig', [

            // 'authors' => $authors,
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
        $size = sizeof($user->getSheets()) + sizeof($user->getDocuments());

        dump($size);

        if($size == 0){

            $manager->remove($user);
            $manager->flush();
    
            $this->addFlash(
                'success',
                "L'utilisateur a bien été supprimé !"
    
            );    


        }else{

            $this->addFlash(
                'danger',
                "L'utilisateur ne peut être supprimé. Des fiches ou des documents lui appartiennent.
                <br> Veuillez réaffecter sa documentation."
    
            ); 
            
        }



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
