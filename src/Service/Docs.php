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


    }

    public function getLastDocs(){

        $limit = 5;

        $sheets = $this->manager->createQuery(
            'SELECT s FROM App\Entity\Sheet s
            WHERE s.status IS NULL
            ORDER BY s.updatedAt DESC
            '
        )
        ->setMaxResults($limit)
        ->getResult();

        dump($sheets);

        return $sheets;

    }

    public function getFrontDocs(){

        $limit = 5;

        $sheets = $this->manager->createQuery(
            'SELECT s FROM App\Entity\Sheet s
            WHERE s.front = 1
            ORDER BY s.updatedAt DESC
            '
        )
        ->setMaxResults($limit)
        ->getResult();

        dump($sheets);

        return $sheets;

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