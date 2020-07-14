<?php

namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * @Route("/user/{slug}", name="user_show")
     */
    public function index(User $user)
    {
        return $this->render('user/index.html.twig', [
            'user' => $user,
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
