<?php

namespace App\Service;

use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;

class Docs{

    private $manager;

    public function __construct(EntityManagerInterface $manager){
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

        $limit = 3;

        $sheets = $this->manager->createQuery(
            'SELECT s FROM App\Entity\Sheet s
            WHERE s.status IS NULL
            ORDER BY s.updatedAt DESC
            '
        )
        ->setMaxResults($limit)
        ->getResult();

        $documents = $this->manager->createQuery(
            'SELECT d FROM App\Entity\Document d
            WHERE d.status IS NULL
            ORDER BY d.updatedAt DESC
            '
        )
        ->setMaxResults($limit)
        ->getResult();

        $files = array_merge_recursive($sheets, $documents);

        usort($files, function($a, $b){ 
            // return strcasecmp($a->getTitle(), $b->getTitle());
            return strtotime($b->getUpdatedAt()->format('Y-m-d H:i:s')) - strtotime($a->getUpdatedAt()->format('Y-m-d H:i:s'));
        });

        dump($files);

        return $files;

    }

    public function getFrontDocs(){

        // $limit = 3;

        $sheets = $this->manager->createQuery(
            'SELECT s FROM App\Entity\Sheet s
            WHERE s.front = 1
            ORDER BY s.updatedAt DESC
            '
        )
        // ->setMaxResults($limit)
        ->getResult();

        $documents = $this->manager->createQuery(
            'SELECT d FROM App\Entity\Document d
            WHERE d.front = 1
            ORDER BY d.updatedAt DESC
            '
        )
        // ->setMaxResults($limit)
        ->getResult();

        $files = array_merge_recursive($sheets, $documents);

        usort($files, function($a, $b){ 
            return strcasecmp($a->getTitle(), $b->getTitle());
        });

        return $files;

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