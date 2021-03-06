<?php

namespace App\Controller;

use DateTime;
use Swift_Mailer;
use App\Entity\User;
use App\Entity\Groupe;
use App\Service\Filter;
use App\Form\AccountType;
use App\Form\PictureType;
use App\Form\EmailResetType;
use App\Entity\PasswordUpdate;
use App\Form\AdminAccountType;
use App\Form\RegistrationType;
use App\Form\PasswordResetType;
use App\Form\PasswordUpdateType;
use App\Repository\UserRepository;
use App\Repository\SheetRepository;
use App\Repository\GroupeRepository;
use Symfony\Component\Form\FormError;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     *Permet d'afficher et de gérer le formulaire de connexion
     * 
     * @Route("/login", name="account_login")
     * 
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

       

        return $this->render('account/login.html.twig', [
            
            'hasError' => $error !== null,
            'username' => $username

        ]);
    }

    /**
     * Permet de se déconnecter
     * 
     * @Route("/logout", name="account_logout")
     *
     * @return void
     */
    public function logout(){
        // .. rien !

    }

    /**
     * Permet d'afficher le formulaire d'inscription
     * 
     * @Route("/register", name="account_register")
     *
     * @return Response
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder) {

        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);

            // $user->setPicture("https://media-exp1.licdn.com/dms/image/C5603AQHu7cPy83UvbA/profile-displayphoto-shrink_100_100/0?e=1585785600&v=beta&t=qLyCkqMYn87M4pG_DFxBhoxNtIbPnIJhn3VLAxBU_Sk");
            $user->setIntroduction("intro");
            // $user->setDescription("description");

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'utilisateur <strong>{$user->getFirstname()} {$user->getLastname()}</strong> a bien été créé !"

            );

            return $this->redirectToRoute('account_login');
        
        }

        return $this->render('account/registration.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * Permet d'afficher et de traiter le formulaire de modification de profil
     * 
     * @Route("/account/profile", name="account_profile")
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function edit(Request $request, EntityManagerInterface $manager) {

        $user = $this->getUser();
        
        $form = $this->createForm(AdminAccountType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

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
                "Les données du profil ont été modifiées avec succès !"
            );

        }
        
        return $this->render('account/edit.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * Permet de modifier ou d'ajouter une photo de profil
     * 
     * @Route("/account/profile/picture/edit", name="account_picture_edit")
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function editPicture(Request $request, EntityManagerInterface $manager) {

        $user = $this->getUser();
        
        $form = $this->createForm(PictureType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

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

                // Si une photo de profil existe déjà, on la supprime
                $oldFilename = $user->getPictureFilename();
                if($oldFilename){
                    
                    $path = $this->getParameter('pictures_directory').'/'.$oldFilename;

                    $filesystem = new Filesystem();
                    $filesystem->remove($path);                    

                }

                $user->setPictureFilename($newFilename);
                
            }
            
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "La photo de profil a été modifiée avec succès !"
            );
            

            return $this->redirectToRoute('account_index');

        }
        
        return $this->render('account/picture.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * Permet de supprimer la photo de profil
     * 
     * @Route("/account/profile/picture/delete/", name="account_picture_delete")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function deletePicture(EntityManagerInterface $manager)
    {
            $user = $this->getUser();

            $filename = $user->getPictureFilename();

            $path = $this->getParameter('pictures_directory').'/'.$filename;

            $filesystem = new Filesystem();
            $filesystem->remove($path);

            // $filesystem->remove(
            //     $this->getParameter('pictures_directory'),
            //     $filename);

            
            $user->setPictureFilename(null);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre photo de profil a bien été supprimée a bien été supprimé !"

            );

            return $this->redirectToRoute('account_index');
        
        
    }



    /**
     * Permet de modifier le mot de passe
     * 
     * @Route("/account/password-update", name="account_password")
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function updatePassword(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder) {

        $user = $this->getUser();

        $passwordUpdate = new PasswordUpdate();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){


            $validPassword = $encoder->isPasswordValid(
                $user,
                $passwordUpdate->getOldPassword() 
            );
            
            // 1. Vérifier que le oldPassword du formulaire soit le même que le password de l'user
            if(!$validPassword){
                
                // Gérer l'erreur
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel"));

            }else{

                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);

                $user->setHash($hash);
                

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "Votre mot de passe a bien été modifié !"
    
                );

                return $this->redirectToRoute('account_index');
            }

            

        
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * Permet de modifier ses informations personnelles
     * 
     * @Route("/account/infos", name="account_infos")
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function updateInfos(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder) {

        $user = $this->getUser();

        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){


            

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "Vos informations personnelles ont bien été modifiées !"
    
                );

                return $this->redirectToRoute('account_index');

        
        }

        return $this->render('account/infos.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * Fonction permettant d'initialiser son mot de passe (mot de passe oublié)
     * 
     * @Route("/account/reset-password", name="account_reset_password")
     *
     * @return void
     */
    public function resetPassword(UserRepository $userRepo, Request $request, EntityManagerInterface $manager, Swift_Mailer $mailer){

        $email = $request->request->get('email');

        $user = $userRepo->findOneByEmail($email);
        
        // Si le mail existe
        if ($user !== null) {

            // Token
            $token = uniqid();
            $user->setToken($token);
            $user->setPasswordRequestedAt(new DateTime());

            $manager->persist($user);
            $manager->flush();

            // Envoie du mail
            $message = (new \Swift_Message('Mot de passe oublié - Intramilo'))
            ->setFrom('intramilo.dijon@gmail.com')
            ->setTo($email)
            ->setBody(
                $this->renderView(
                    // emails/registration.html.twig
                    'emails/reset-password.html.twig',
                    ['user' => $user]
                ),
                'text/html'
            );

            $mailer->send($message);

            return $this->render('account/reset-password-email-confirm.html.twig');

            // return $this->render('account/login.html.twig', [ 'hasError' => null, 'username' => $email ]);

        }else{


            

            // $this->addFlash(
            //     'danger',
            //     "Cet email n'existe pas."

            // );
            
            // Ouverture de la page
            if ($email == null) {

                return $this->render('account/reset-password.html.twig',  [ 'no_email' => false ]);

            }else{
                // Mauvais mail
                return $this->render('account/reset-password.html.twig',  [ 'no_email' => true ]);

            }

            

        }
        // }

        return $this->render('account/reset-password.html.twig', array(
            // 'form' => $form->createView(),
        ));

    }

    /**
     * Permet d'initialiser le mot de passe
     * 
     * @Route("/account/reset-password-token", name="account_reset_password_token")
     *
     * @param UserRepository $userRepo
     * @param Request $request
     * @param UserPasswordEncoder $encoder
     * @param EntityManagerInterface $manager
     * @return void
     */
    public function resetPasswordToken(UserRepository $userRepo, Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager){

    
        $password = new PasswordUpdate;
        $form = $this->createForm(PasswordResetType::class, $password);
        $form->handleRequest($request);


        $token = $request->query->get('token');

        // S'il existe un jeton
        if ($token !== null) {

            $user = $userRepo->findOneByToken($token);

                // Si l'utilisateur existe
                if ($user !== null) {

                    $passwordRequestedAt = $user->getPasswordRequestedAt();

                    $now = new DateTime();
                    $interval = $now->getTimestamp() - $passwordRequestedAt->getTimestamp();

                    $daySeconds = 60 * 60 * 24; // 24h
                    $expired = $interval > $daySeconds ? true : $expired = false;


                    // Si le lien a expiré
                    if($expired){

                        return $this->render('account/reset-password-email-expired.html.twig');    
                    }


                    if($form->isSubmitted() && $form->isValid()){

                        $encoded = $encoder->encodePassword($user, $password->getNewPassword());
                        $user->setHash($encoded);

                        $manager->persist($user);
                        $manager->flush();

                        //add flash
                        
                        $this->addFlash(
                            'success',
                            "Mot de passe réinitialisé avec succès ! Connectez-vous avec votre nouveau mot de passe."

                        );

                        return $this->render('account/login.html.twig', [ 'hasError' => null, 'username' => $user->getEmail() ]);

                    }

                    // $pwd = $request->request->get('password');
                    // $pwd2 = $request->request->get('password2');

                    // if ($pwd == $pwd2 && $pwd != null) {

                        
                    // }

                    return $this->render('account/reset-password-token.html.twig', array( 
                        'form' => $form->createView(),
                        'user' => $user
                    ));       
                }
                // N'est plus valide
                return $this->render('account/reset-password-email-expired.html.twig');

        }
        

    }

    

    /**
     * Undocumented function
     *
     * @return void
     */
    public function picture(){


        return $this->render('user/index.html.twig', [
            'user' => $this->getUser()
        ]);


    }


    /**
     * Permet de faire la liste des réservations de l'utilisateur connecté
     * 
     * @Route("/account/bookings", name="account_bookings")
     *
     * @return Response
     */
    public function bookings(){

        return $this->render('account/bookings.html.twig');
    }


    /**
     * Permet de faire la liste des documentations de l'utilisateur connecté
     * 
     * @Route("/account/documents", name="account_documents")
     * 
     * @return Response
     */
    public function myDocuments(SheetRepository $sheetRepo, UserRepository $userRepo, Request $request, EntityManagerInterface $manager, Filter $filter)
    {
        $subCategories = $this->getUser()->getSubCategories();

        $files = $filter->getFiles($this->getUser(), $userRepo, $sheetRepo);

        $counter = count($files['filesToValidate']) + count($files['filesToCorrect']) + count($files['filesWellObsolete']) + count($files['filesObsolete']);

        return $this->render('account/dashboard.html.twig', [

            'filesToValidate' => $files['filesToValidate'],
            'filesToCorrect' => $files['filesToCorrect'],
            'filesUpToDate' => $files['filesUpToDate'],
            'filesWellObsolete' => $files['filesWellObsolete'],
            'filesObsolete' => $files['filesObsolete'],
            'counter' => $counter,
            'subCategories' => $subCategories
        ]);
    }

    /**
     * Permet de récupérer la liste des brouillons de l'utilisateur
     * 
     * @Route("/account/drafts", name="account_drafts")
     * 
     * @return Response
     */
    public function myDrafts(SheetRepository $sheetRepo, UserRepository $userRepo, Request $request, EntityManagerInterface $manager, Filter $filter)
    {
        $subCategories = $this->getUser()->getSubCategories();

        $files = $filter->getDrafts($this->getUser());

        return $this->render('account/drafts.html.twig', [

            'files' => $files
        ]);
    }

    /**
     * Permet de faire la liste des dossiers de l'utilisateur connecté
     * 
     * @Route("/account/folders", name="account_folders")
     * 
     * @return Response
     */
    public function myFolders(SheetRepository $sheetRepo, DocumentRepository $docRepo, Request $request, EntityManagerInterface $manager, Filter $filter)
    {
        $subCategories = $this->getUser()->getSubCategories();

        return $this->render('account/folders.html.twig', [

            'subCategories' => $subCategories
        ]);
    }

    /**
     * Permet de charger le compteur de notifications
     * 
     * @Route("/account/notifications", name="notifications_load")
     * 
     * @return Response
     */
    public function notifications(Filter $filter, UserRepository $userRepo, SheetRepository $sheetRepo)
    {
        // $files = $filter->getFiles($this->getUser());

        // Recherche des GROUPES dont l'utilisateur est responsable
        if($this->isGranted('ROLE_ADMIN')){

            $notifications = $filter->getAdminNotifications($this->getUser(), $userRepo, $sheetRepo);
            
        }else{

            $notifications = $filter->getNotifications($this->getUser(), $userRepo, $sheetRepo);

        }


        $counter = $notifications['counter'];

        // $counter = count($files['filesToValidate']) + count($files['filesToCorrect']) + count($files['filesWellObsolete']) + count($files['filesObsolete']);
        // $counter = $counter + count($adminFiles['filesToValidate']) + count($adminFiles['filesToCorrect']) + count($adminFiles['filesWellObsolete']) + count($adminFiles['filesObsolete']);

        // Si aucune notification
        if($counter == 0){

            $counter = null;

        }


        // Enregistrement dans le Session
        $session = new Session();

        // set session attributes
        $session->set('notification-counter', $counter);



        return $this->json(
            [
                'counter' => $counter,
                'notifications' => $notifications
            ]);
    }

    /**
     * Permet d'afficher le profil de l'utilisateur connecté
     *
     * @Route("/account", name="account_index")
     * @Route("/account/{id}", name="account_index_filter")
     * @IsGranted("ROLE_USER")
     * 
     * @ParamConverter("Groupe", options={"mapping": {"id": "id"}})
     * 
     * @return Response
     */
    public function myAccount(Request $request, SheetRepository $sheetRepo, UserRepository $userRepo, GroupeRepository $groupeRepo, Filter $filter, Groupe $groupe = null) {

        $files = $filter->getFiles($this->getUser(), $userRepo, $sheetRepo);

        $adminFiles = $filter->getAdminFiles($this->getUser(), $userRepo, $sheetRepo, $groupe);

        $drafts = $filter->getDrafts($this->getUser());

        $groupes = $groupeRepo->findBy(array(), array('title' => 'ASC'));


    
        return $this->render('user/index.html.twig', [

            'filesToValidate' => $files['filesToValidate'],
            'filesToCorrect' => $files['filesToCorrect'],
            'filesUpToDate' => $files['filesUpToDate'],
            'filesWellObsolete' => $files['filesWellObsolete'],
            'filesObsolete' => $files['filesObsolete'],
            'filesArchived' => $files['filesArchived'],

            'adminFilesToValidate' => $adminFiles['filesToValidate'],
            'adminFilesToCorrect' => $adminFiles['filesToCorrect'],
            'adminFilesUpToDate' => $adminFiles['filesUpToDate'],
            'adminFilesWellObsolete' => $adminFiles['filesWellObsolete'],
            'adminFilesObsolete' => $adminFiles['filesObsolete'],
            'drafts' => $drafts,
            'groupe' => $groupe,
            'groupes' => $groupes,
            'user' => $this->getUser()

        ]);

    }


    // /**
    //  * Permet de récupérer les notifications de l'utilisateur
    //  * 
    //  * @Route("/doc/notification", name="account_notification")
    //  *
    //  * @param Filter $filter
    //  * @return void
    //  */
    // public function notification(Filter $filter){

    //     $subCategories = $this->getUser()->getSubCategories();
    //     $files = $filter->getFiles($subCategories);

    //     $encoder = [new JsonEncoder()];
    //     $normalizer = [new ObjectNormalizer()];

    //     $serializer = new Serializer($normalizer, $encoder);

    //     $jsonContent = $serializer->normalize($files, 'json');
    //     $data = $serializer->normalize($user, null, [AbstractNormalizer::ATTRIBUTES => ['familyName', 'company' => ['name']]]);



    //     return $this->json([$jsonContent]);
    // }


   
}
