<?php

namespace App\Controller;

use App\Entity\Sheet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FavoritesController extends AbstractController
{
    /**
     * @Route("/favorites/{id}/add", name="favorites_add")
     */
    public function add(Sheet $sheet, EntityManagerInterface $manager)
    {
        
        $user = $this->getUser();
        $user->addFavorite($sheet);

        $manager->persist($sheet);
        $manager->flush();

        $this->addFlash(
            'success',
            "La fiche <strong>{$sheet->getTitle()}</strong> a été ajoutée en favori !"

        );
    
        // Gestion des nouveaux slugs
        return $this->redirectToRoute('sheet_show', ['id' => $sheet->getId()]);

    }

        /**
     * @Route("/favorites/{id}/remove", name="favorites_remove")
     */
    public function remove(Sheet $sheet, EntityManagerInterface $manager)
    {
        
        $user = $this->getUser();
        $user->removeFavorite($sheet);

        $manager->persist($sheet);
        $manager->flush();

        $this->addFlash(
            'success',
            "La fiche <strong>{$sheet->getTitle()}</strong> n'est plus en favori"

        );
    
        // Gestion des nouveaux slugs
        return $this->redirectToRoute('sheet_show', ['id' => $sheet->getId()]);

    }
}
