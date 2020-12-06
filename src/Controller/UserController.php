<?php

namespace App\Controller;


use App\Entity\User;
use App\Service\Filter;
use App\Repository\UserRepository;
use App\Repository\SheetRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * @Route("/user/{slug}", name="user_show")
     */
    public function index(User $user, SheetRepository $sheetRepo, UserRepository $userRepo, Filter $filter)
    {
        


        if($user == $this->getUser()){

            return $this->redirectToRoute('account_index');

        }

        $files = $filter->getFiles($user, $userRepo, $sheetRepo);
    
        return $this->render('user/index.html.twig', [

            'filesToValidate' => $files['filesToValidate'],
            'filesToCorrect' => $files['filesToCorrect'],
            'filesUpToDate' => $files['filesUpToDate'],
            'filesWellObsolete' => $files['filesWellObsolete'],
            'filesObsolete' => $files['filesObsolete'],

            'user' => $user

        ]);
    }

    /**
     * @Route("/users", name="users_show")
     */
    public function show(UserRepository $repo)
    {
        
        $users = $repo->findBy(array(), array('lastName' => 'ASC'));


        return $this->render('user/show.html.twig', [
            'users' => $users,
        ]);
    }

}
