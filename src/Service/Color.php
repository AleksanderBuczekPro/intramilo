<?php

namespace App\Service;

use App\Repository\PoleRepository;
use Doctrine\ORM\EntityManagerInterface;

class Color{

    private $manager;

    public function __construct(EntityManagerInterface $manager){
        $this->manager = $manager;

    }

    public function getPoleColor(){

        // Couleurs du Pôle (cf. Shards Dashboard https://designrevision.com/demo/shards-dashboards/components-blog-posts.html#)

        // Turquoise
        #00b8d8
        $turquoise = '#00b8d8';

        // Violet
        #3f20e7
        $purple = '#3f20e7';

        // Jaune
        #ffb400
        $yellow = '#ffb400';

        // Vert fluo
        #1adba2
        $green = '#1adba2';

        // Rouge rose
        #ff4169
        $red = '#ff4169';
        
        // Noir
        #0a0c0d
        $black = '#0a0c0d';

        $colors = array($turquoise, $purple, $yellow, $green, $red, $black);

        $nbPoles =  $this->manager->createQuery(
            'SELECT COUNT(p) FROM App\Entity\Pole p          
            '
        )
        ->getResult();


        // Taille du tableau de couleurs MODULO index

        $index =  $nbPoles[0][1] % count($colors);

        $color_hex = $colors[$index];

        return $color_hex;


    }



}