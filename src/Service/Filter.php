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

    public function getFiles($subCategories){

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

        $sheets = $this->getSheets($subCategories);
        $documents =  $this->getDocuments($subCategories);
        
        $filesUpToDate = array_merge_recursive($sheets['sheetsUpToDate'], $documents['documentsUpToDate']);
        $filesWellObsolete = array_merge_recursive($sheets['sheetsWellObsolete'], $documents['documentsWellObsolete']);
        $filesObsolete = array_merge_recursive($sheets['sheetsObsolete'], $documents['documentsObsolete']);

        usort($filesUpToDate, function($a, $b){ 
            return strcasecmp($a->getTitle(), $b->getTitle());
        });

        usort($filesWellObsolete, function($a, $b){ 
            return strcasecmp($a->getTitle(), $b->getTitle());
        });


        usort($filesObsolete, function($a, $b){ 
            return strcasecmp($a->getTitle(), $b->getTitle());
        });




        return array(
            'filesToValidate' => $sheets['sheetsToValidate'],
            'filesToCorrect' => $sheets['sheetsToCorrect'],
            'filesUpToDate' => $filesUpToDate,
            'filesWellObsolete' => $filesWellObsolete,
            'filesObsolete' => $filesObsolete
        );

    }

    public function getSheets($subCategories){

        $sheetsUpToDate = [];
        $sheetsToValidate = [];
        $sheetsToCorrect = [];
        $sheetsObsolete = [];
        $sheetsWellObsolete = [];

        // Pour chaque sous-catégorie
        foreach ($subCategories as $subCategory) {
            
            $parameters = array(
                'sub_category'=> $subCategory
            );
        
            // En cours de validation / A corriger
            $sheet = $this->manager->createQuery(
                                    "SELECT s
                                    FROM App\Entity\Sheet s
                                    WHERE s.status = 'TO_VALIDATE' AND s.subCategory = :sub_category
                                    ORDER BY s.updatedAt DESC
                                    "
                                )
                                ->setParameters($parameters)
                                ->getResult();
            
            
            $sheetsToValidate = array_merge_recursive($sheetsToValidate, $sheet);
            

            $sheet = $this->manager->createQuery(
                                    "SELECT s
                                    FROM App\Entity\Sheet s
                                    WHERE s.status = 'TO_CORRECT' AND s.subCategory = :sub_category
                                    ORDER BY s.updatedAt DESC
                                    "
                                )
                                ->setParameters($parameters)
                                ->getResult();
            
            $sheetsToCorrect = array_merge_recursive($sheetsToCorrect, $sheet);
                            

            // A jour / Bientôt obsolète / Obsolète

            // A jour

            $startDate = new DateTime();
            $endDate = new DateTime();

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

            $sheetsUpToDate = array_merge_recursive($sheetsUpToDate, $sheet);

            // Bientôt obsolètes
            $startDate = new DateTime();
            $endDate = new DateTime();

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

            $sheetsWellObsolete = array_merge_recursive($sheetsWellObsolete, $sheet);


            // Obsolète
            $startDate = new DateTime();
            $endDate = new DateTime();

            $startDate->modify('-6 months');
            $endDate = "";

            
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

            $sheetsObsolete = array_merge_recursive($sheetsObsolete, $sheet);
   
        
        }


        return array(
            'sheetsToValidate' => $sheetsToValidate,
            'sheetsToCorrect' => $sheetsToCorrect,
            'sheetsUpToDate' => $sheetsUpToDate,
            'sheetsWellObsolete' => $sheetsWellObsolete,
            'sheetsObsolete' => $sheetsObsolete
        );

    }

    public function getDocuments($subCategories){

        $documentsUpToDate = [];
        $documentsObsolete = [];
        $documentsWellObsolete = [];

        // Pour chaque sous-catégorie
        foreach ($subCategories as $subCategory) {
                                 

            // A jour / Bientôt obsolète / Obsolète

            // A jour

            $startDate = new DateTime();
            $endDate = new DateTime();

            $endDate->modify('-3 months');

            $parameters = array(
                'date_start'=> $startDate,
                'date_end'=> $endDate,
                'sub_category'=> $subCategory
            );

            $document =  $this->manager->createQuery(
                'SELECT d
                FROM App\Entity\Document d
                WHERE d.updatedAt < :date_start AND d.updatedAt > :date_end AND d.subCategory = :sub_category AND d.status IS NULL
                '
            )
            ->setParameters($parameters)
            ->getResult();

            $documentsUpToDate = array_merge_recursive($documentsUpToDate, $document);

            // Bientôt obsolètes
            $startDate = new DateTime();
            $endDate = new DateTime();

            $startDate->modify('-5 months'); 
            $endDate->modify('-6 months'); 

            $parameters = array(
                'date_start'=> $startDate,
                'date_end' => $endDate,
                'sub_category'=> $subCategory
            );

            $q = "";

            $document =  $this->manager->createQuery(
                'SELECT d
                FROM App\Entity\Document d
                WHERE d.updatedAt < :date_start AND d.updatedAt > :date_end '. $q .' AND d.subCategory = :sub_category AND d.status IS NULL
                '
            )
            ->setParameters($parameters)
            ->getResult();

            $documentsWellObsolete = array_merge_recursive($documentsWellObsolete, $document);


            // Obsolète
            $startDate = new DateTime();
            $endDate = new DateTime();

            $startDate->modify('-6 months');
            $endDate = "";

            
            $parameters = array(
                'date_start'=> $startDate,
                'sub_category'=> $subCategory
            );

            $q = "";

            $document =  $this->manager->createQuery(
                'SELECT d
                FROM App\Entity\Document d
                WHERE d.updatedAt < :date_start '. $q .' AND d.subCategory = :sub_category AND d.status IS NULL
                '
            )
            ->setParameters($parameters)
            ->getResult();

            $documentsObsolete = array_merge_recursive($documentsObsolete, $document);
   
        
        }

        return array(
            'documentsUpToDate' => $documentsUpToDate,
            'documentsWellObsolete' => $documentsWellObsolete,
            'documentsObsolete' => $documentsObsolete
        );

    }


}