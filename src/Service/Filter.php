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


        $startDate = new DateTime();
        $endDate = new DateTime();

        dump($startDate);
        
        switch ($filter) {
            case 'upToDate':
                $endDate->modify('-3 months'); 
                break;
            // case 'toValidate':
            //     echo "i égal 1";
            //     break;
            // case 'toCorrect':
            //     echo "i égal 2";
            //     break;
            case 'wellObsolete':
                $startDate->modify('-5 months'); 
                $endDate->modify('-6 months'); 
                break;
            case 'obsolete':
                $startDate->modify('-6 months');
                $endDate = ""; 
                break;
        }

        $sheets = $this->getSheets($startDate, $endDate, $subCategories);
        $documents =  $this->getDocuments($startDate, $endDate, $subCategories);

        $files = array_merge_recursive($sheets, $documents);

        usort($files, function($a, $b){ 
            return strcasecmp($a->getTitle(), $b->getTitle());
        });

        return $files;

    }


    public function getSheets($startDate, $endDate, $subCategories){

        $sheets = [];

        foreach ($subCategories as $subCategory) {

            if($endDate != ""){

                $parameters = array(
                    'date_start'=> $startDate,
                    'date_end'=> $endDate,
                    'sub_category'=> $subCategory
                );

                $q = "AND s.updatedAt > :date_end";

            }else{

                $parameters = array(
                    'date_start'=> $startDate,
                    'sub_category'=> $subCategory
                );

                $q = "";
            }
            
            
            $sheet =  $this->manager->createQuery(
                'SELECT s
                FROM App\Entity\Sheet s
                WHERE s.updatedAt < :date_start '. $q .' AND s.subCategory = :sub_category
                '
            )
            ->setParameters($parameters)
            ->getResult();

            $sheets = array_merge_recursive($sheets, $sheet);

        }

        return $sheets;

    }


    public function getDocuments($startDate, $endDate, $subCategories){

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