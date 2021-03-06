<?php

namespace App\Controller;

use App\Repository\PoleRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDocumentationController extends AbstractController
{
     /**
     * Permet la gestion des pôles, catégories et sous catégories.
     * 
     * @Route("/admin/documentation", name="admin_documentation_index")
     */
    public function index(PoleRepository $repo)
    {
        $poles = $repo->findBy(array(), array('place' => 'ASC'));

        return $this->render('admin/documentation/index.html.twig', [
            'poles' => $poles
    
        ]);
    }
}
