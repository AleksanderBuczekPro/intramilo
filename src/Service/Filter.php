<?php

namespace App\Service;

use App\Repository\SheetRepository;
use DateTime;
use Doctrine\Common\Persistence\ObjectManager;

class Filter{

    private $manager;

    public function __construct(ObjectManager $manager){
        $this->manager = $manager;

    }

    public function getResults($filter, $subCategories){

        // switch ($filter) {
        //     case 'upToDate':
        //         $endDate->modify('-3 months'); 
        //         break;
        //     case 'toValidate':
        //         break;
        //     case 'toCorrect':
        //         break;
        //     case 'wellObsolete':
        //         $startDate->modify('-5 months'); 
        //         $endDate->modify('-6 months'); 
        //         break;
        //     case 'obsolete':
        //         $startDate->modify('-6 months');
        //         $endDate = ""; 
        //         break;
        // }

        $sheets = $this->getSheets($subCategories, $filter);
        $documents =  $this->getDocuments($subCategories, $filter);

        $files = array_merge_recursive($sheets, $documents);

        usort($files, function($a, $b){ 
            return strcasecmp($a->getTitle(), $b->getTitle());
        });

        return $files;

    }

    public function getSheets($subCategories, $filter){

        

        $sheets = [];



        foreach ($subCategories as $subCategory) {

            $startDate = new DateTime();
            $endDate = new DateTime();

            switch ($filter) {

                // Fiches à jour
                case 'upToDate':
                    
                    $endDate->modify('-3 months');

                    
                    $parameters = array(
                        'date_start'=> $startDate,
                        'date_end'=> $endDate,
                        'sub_category'=> $subCategory
                    );

                    $sheet =  $this->manager->createQuery(
                        'SELECT s
                        FROM App\Entity\Sheet s
                        WHERE s.updatedAt < :date_start AND s.updatedAt > :date_end AND s.subCategory = :sub_category AND s.status IS NULL
                        '
                    )
                    ->setParameters($parameters)
                    ->getResult();
        
                    $sheets = array_merge_recursive($sheets, $sheet);
                    break;

                // Fiches En cours de validation
                case 'toValidate':
                    $parameters = array(
                        'etat'=> "TO_VALIDATE",
                        'sub_category'=> $subCategory
                    );
    
                    $sheet =  $this->manager->createQuery(
                        'SELECT s
                        FROM App\Entity\Sheet s
                        WHERE s.status = :etat AND s.subCategory = :sub_category
                        '
                    )
                    ->setParameters($parameters)
                    ->getResult();
        
                    $sheets = array_merge_recursive($sheets, $sheet);

    
                    break;

                // Fiches à corrriger
                case 'toCorrect':
                    $parameters = array(
                    'etat'=> "TO_CORRECT",
                    'sub_category'=> $subCategory
                );

                $sheet =  $this->manager->createQuery(
                    'SELECT s
                    FROM App\Entity\Sheet s
                    WHERE s.status = :etat AND s.subCategory = :sub_category
                    '
                )
                ->setParameters($parameters)
                ->getResult();
    
                $sheets = array_merge_recursive($sheets, $sheet);
                    break;
                
                // Fiches bientôt obsolètes
                case "wellObsolete":
                    $startDate->modify('-5 months'); 
                    $endDate->modify('-6 months'); 

                    $parameters = array(
                        'date_start'=> $startDate,
                        'date_end' => $endDate,
                        'sub_category'=> $subCategory
                    );
    
                    $q = "";

                    $sheet =  $this->manager->createQuery(
                        'SELECT s
                        FROM App\Entity\Sheet s
                        WHERE s.updatedAt < :date_start AND s.updatedAt > :date_end '. $q .' AND s.subCategory = :sub_category AND s.status IS NULL
                        '
                    )
                    ->setParameters($parameters)
                    ->getResult();
        
                    $sheets = array_merge_recursive($sheets, $sheet);
                    break;

                // Fiches obsolètes
                case "obsolete":
                    $startDate->modify('-6 months');
                    $endDate = "";

                    // Obsolète
                    $parameters = array(
                        'date_start'=> $startDate,
                        'sub_category'=> $subCategory
                    );
    
                    $q = "";

                    $sheet =  $this->manager->createQuery(
                        'SELECT s
                        FROM App\Entity\Sheet s
                        WHERE s.updatedAt < :date_start '. $q .' AND s.subCategory = :sub_category AND s.status IS NULL
                        '
                    )
                    ->setParameters($parameters)
                    ->getResult();
        
                    $sheets = array_merge_recursive($sheets, $sheet);
                    break;
            }
           

        }


        return $sheets;

    }


    public function getDocuments($subCategories, $filter){

        $startDate = new DateTime();
        $endDate = new DateTime();

        $documents = [];

        foreach ($subCategories as $subCategory) {

            if($endDate != ""){

                $parameters = array(
                    'date_start'=> $startDate,
                    'date_end'=> $endDate,
                    'sub_category'=> $subCategory
                );

                $q = "AND d.updatedAt > :date_end";

            }else{

                $parameters = array(
                    'date_start'=> $startDate,
                    'sub_category'=> $subCategory
                );

                $q = "";
            }
            
            
            $document =  $this->manager->createQuery(
                'SELECT d
                FROM App\Entity\Document d
                WHERE d.updatedAt < :date_start '. $q .' AND d.subCategory = :sub_category
                '
            )
            ->setParameters($parameters)
            ->getResult();

            $documents = array_merge_recursive($documents, $document);

        }

        return $documents;

    }


}