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
        

        // Init
        $drafts = '';

        $adminFiles['filesToValidate'] = '';
        $adminFiles['filesToCorrect'] = '';
        $adminFiles['filesUpToDate'] = '';
        $adminFiles['filesWellObsolete'] = '';
        $adminFiles['filesObsolete'] = '';

        if($user == $this->getUser()){

            $adminFiles = $filter->getAdminFiles($this->getUser(), $userRepo, $sheetRepo);
            $drafts = $filter->getDrafts($this->getUser());

            $user = $this->getUser();


        }

        $files = $filter->getFiles($user, $userRepo, $sheetRepo);
    
        return $this->render('user/index.html.twig', [

            'filesToValidate' => $files['filesToValidate'],
            'filesToCorrect' => $files['filesToCorrect'],
            'filesUpToDate' => $files['filesUpToDate'],
            'filesWellObsolete' => $files['filesWellObsolete'],
            'filesObsolete' => $files['filesObsolete'],

            'adminFilesToValidate' => $adminFiles['filesToValidate'],
            'adminFilesToCorrect' => $adminFiles['filesToCorrect'],
            'adminFilesUpToDate' => $adminFiles['filesUpToDate'],
            'adminFilesWellObsolete' => $adminFiles['filesWellObsolete'],
            'adminFilesObsolete' => $adminFiles['filesObsolete'],

            'drafts' => $drafts,
            'user' => $user

        ]);
    }

    /**
     * @Route("/users", name="users_show")
     */
    public function show(UserRepository $repo)
    {
        
        $users = $repo->findBy(array(), array('lastName' => 'ASC'));

        dump($users);

        return $this->render('user/show.html.twig', [
            'users' => $users,
        ]);
    }

}
