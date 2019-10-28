<?php

namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;

class Search{

    private $manager;

    public function __construct(ObjectManager $manager){
        $this->manager = $manager;

    }

    public function getResults($query){

        $sheets = $this->getSheets($query);
        $documents =  $this->getDocuments($query);

        $files = array_merge_recursive($sheets, $documents);

        usort($files, function($a, $b){ 
            return strcasecmp($a->getTitle(), $b->getTitle());
        });

        return $files;

    }


    public function getSheets($query){

        $parameters = array(
            'title_query'=> '%'.$query.'%',
            'organization_query'=> '%'.$query.'%',
            'content_query'=> '%'.$query.'%'
        );
        
        return $this->manager->createQuery(
            'SELECT s
            FROM App\Entity\Sheet s
            WHERE lower(s.title) LIKE :title_query
            OR lower(s.organization) LIKE :organization_query
            OR lower(s.content) LIKE :content_query
            '
        )
        ->setParameters($parameters)
        ->getResult();

    }


    public function getDocuments($query){

        $parameters = array(
            'title_query'=> '%'.$query.'%',
            'organization_query'=> '%'.$query.'%'
        );
        
        return $this->manager->createQuery(
            'SELECT d
            FROM App\Entity\Document d
            WHERE lower(d.title) LIKE :title_query
            OR lower(d.organization) LIKE :organization_query
            '
        )
        ->setParameters($parameters)
        ->getResult();

    }


}