<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends EasyAdminController
{
    
    public function persistUserEntity(User $user)
    {
        // Gestion du mot de passe
        $encodedPassword = $this->encodePassword($user, $user->getHash());
        $user->setHash($encodedPassword);

        parent::persistEntity($user);

    }


    private function encodePassword($user, $password)
    {
        $passwordEncoderFactory = new EncoderFactory([
            User::class => new MessageDigestPasswordEncoder('sha512', true, 5000)
        ]);

        $encoder = $passwordEncoderFactory->getEncoder($user);

        return $encoder->encodePassword($password, $user->getSalt());
    }


}