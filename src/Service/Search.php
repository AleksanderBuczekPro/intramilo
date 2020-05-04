<?php

namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;

class Search{

    private $manager;

    public function __construct(EntityManagerInterface $manager){
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
            // 'content_query'=> '%'.$query.'%'
        );

        $qb = $this->manager->createQueryBuilder();

        $qb->select('s')
            ->from('App\Entity\Sheet', 's')
            ->innerJoin('App\Entity\Organization', 'o', 'WITH', 'o.id = s.organization')
            ->where('s.status IS NULL')
            ->andWhere('s.title LIKE :title_query')
            ->orWhere('o.name LIKE :organization_query')
            // ->orWhere('s.content LIKE :content_query')
            
            ->setParameters($parameters)
            ->orderBy('s.title', 'ASC');


        $query = $qb->getQuery();

        return $query->execute();
        
    }


    public function getDocuments($query){

        $parameters = array(
            'title_query'=> '%'.$query.'%',
            'organization_query'=> '%'.$query.'%'
        );

        $qb = $this->manager->createQueryBuilder();
        
        $qb->select('d')
            ->from('App\Entity\Document', 'd')
            ->innerJoin('App\Entity\Organization', 'o', 'WITH', 'o.id = d.organization')
            ->where('d.status IS NULL')
            ->andWhere('d.title LIKE :title_query')
            ->orWhere('o.name LIKE :organization_query')
            ->setParameters($parameters)
            ->orderBy('d.title', 'ASC');

        $query = $qb->getQuery();

        return $query->execute();
    }


}