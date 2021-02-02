<?php

namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;

class Search{

    private $manager;

    public function __construct(EntityManagerInterface $manager){
        $this->manager = $manager;

    }

    public function getFiles($query, $sort){

        $sheets = $this->getSheets($query);
        $documents =  $this->getDocuments($query);

        $files = array_merge_recursive($sheets, $documents);

        if($sort == "popular" || $sort == null){
            usort($files, function($a, $b){ 
                return strnatcmp($b->getViews(), $a->getViews());
            });
        }

        if($sort == "recent"){
            usort($files, function($a, $b){ 
                return strtotime($b->getUpdatedAt()->format('Y-m-d H:i:s')) - strtotime($a->getUpdatedAt()->format('Y-m-d H:i:s'));
                
            });
        }

        if($sort == "ancient"){
            usort($files, function($a, $b){ 
                return strtotime($a->getUpdatedAt()->format('Y-m-d H:i:s')) - strtotime($b->getUpdatedAt()->format('Y-m-d H:i:s'));
            });
        }

        


        return $files;

    }


   
    
    public function getSheets($query){

        $query = trim($query);

        $parameters = array(
            'title_query'=> '%'.$query.'%',
            'subtitle_query'=> '%'.$query.'%',
            'name_query'=> '%'.$query.'%',
            'organization_query'=> '%'.$query.'%',
            'attachment_query'=> '%'.$query.'%',
            'introduction_query'=> '%'.$query.'%',
            'header_query'=> '%'.$query.'%',
            'header_title_query'=> '%'.$query.'%',
            'header_content_query'=> '%'.$query.'%',
            'paragraph_title_query'=> '%'.$query.'%',
            'paragraph_content_query'=> '%'.$query.'%'
        );

        return $this->manager->createQuery(
            "SELECT DISTINCT s
            FROM
                App\Entity\Sheet s

            LEFT JOIN App\Entity\User u         WITH u.id = s.author
            LEFT JOIN App\Entity\Organization o WITH o.id = s.organization
            LEFT JOIN App\Entity\Attachment a   WITH a.sheet = s.id
            LEFT JOIN App\Entity\Header h       WITH h.sheet = s.id
            LEFT JOIN App\Entity\Section se     WITH se.header = h.id
            LEFT JOIN App\Entity\Paragraph p    WITH p.sheet = s.id


            WHERE (
                s.status IS NULL AND s.archivedAt IS NULL
            ) 
            AND (
                   s.title        LIKE :title_query
                OR s.subtitle     LIKE :subtitle_query
                OR CONCAT(u.firstName, ' ', u.lastName) LIKE :name_query
                OR o.name         LIKE :organization_query
                OR a.title        LIKE :attachment_query
                OR s.introduction LIKE :introduction_query
                OR h.title        LIKE :header_query
                OR se.title       LIKE :header_title_query
                OR se.content     LIKE :header_content_query
                OR p.title        LIKE :paragraph_title_query  
                OR p.content      LIKE :paragraph_content_query  
            
            )"
        )
        ->setParameters($parameters)
        ->getResult();
    }

    
    public function getDocuments($query){

        $parameters = array(
            'title_query'=> '%'.$query.'%',
            'organization_query'=> '%'.$query.'%',
            'name_query'=> '%'.$query.'%'
        );

        $qb = $this->manager->createQueryBuilder();
        
        $qb->select('d')
            ->from('App\Entity\Document', 'd')
            ->innerJoin('App\Entity\Organization', 'o', 'WITH', 'o.id = d.organization')
            ->leftJoin('App\Entity\User', 'u', 'WITH', 'u.id = d.author')
            ->where('d.status IS NULL')
            ->andWhere('d.title LIKE :title_query')
            ->orWhere('o.name LIKE :organization_query')
            ->orWhere("CONCAT(u.firstName, ' ', u.lastName) LIKE :name_query")
            ->setParameters($parameters)
            ->orderBy('d.title', 'ASC');

        $query = $qb->getQuery();

        return $query->execute();
    }

    public function getUsers($query){

        $query = trim($query);

        $parameters = array(
            'name'=> '%'.$query.'%',
        );

        $qb = $this->manager->createQueryBuilder();

        $qb->select('u')
            ->from('App\Entity\User', 'u')
            ->where("CONCAT(u.firstName, ' ',u.lastName) LIKE :name")
            
            ->setParameters($parameters)
            ->orderBy('u.lastName', 'ASC');


        $query = $qb->getQuery();

        return $query->execute();
        
    }

    public function getAntennes($query){

        $query = trim($query);

        $parameters = array(
            'title_query'=> '%'.$query.'%'
        );

        $qb = $this->manager->createQueryBuilder();

        $qb->select('a')
            ->from('App\Entity\Antenne', 'a')
            ->where('a.title LIKE :title_query')
            
            ->setParameters($parameters)
            ->orderBy('a.title', 'ASC');


        $query = $qb->getQuery();

        return $query->execute();
        
    }


}