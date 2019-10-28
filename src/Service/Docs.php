<?php

namespace App\Service;

use App\Repository\DocumentRepository;
use Doctrine\Common\Persistence\ObjectManager;

class Docs{

    private $manager;

    public function __construct(ObjectManager $manager){
        $this->manager = $manager;

    }

    public function getDocs($subcategory_id){

        return $this->manager->createQuery(
            'SELECT d FROM App\Entity\Document d
            JOIN App\Entity\SubCategory s
            WHERE d.subCategory = :sub_category_id           
            '
        )
        ->setParameter('sub_category_id', $subcategory_id)
        ->getResult();

        // return $this->manager->createQuery(
        //     'SELECT *
        //     (SELECT title, slug, organization, updatedAt, content, null as size, null as mimetype, null as original_name
        //     FROM App\Entity\Sheet s

        //     UNION ALL
            
        //     SELECT title, null as slug, organization, updatedAt, null as content, size, mime_type, original_name
        //     FROM App\Entity\Document d)
        //     Doc
        //     ORDER BY title'
        // )
        // ->getResult();


    }

    public function getSheets($subcategory_id){

        return $this->manager->createQuery(
            'SELECT sh FROM App\Entity\Sheet sh
            JOIN App\Entity\SubCategory s
            WHERE sh.subCategory = :sub_category_id           
            '
        )
        ->setParameter('sub_category_id', $subcategory_id)
        ->getResult();
    
    }

    public function getAll($subcategory_id){

        return $this->manager->createQuery(
            'SELECT sh.title FROM App\Entity\Sheet sh

            UNION ALL

            SELECT d.title FROM App\Entity\Document d
            '
        )
        ->setParameter('sub_category_id', $subcategory_id)
        ->getResult();
    
    }




}