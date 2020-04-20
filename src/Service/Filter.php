<?php

namespace App\Service;

use App\Repository\SheetRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class Filter{

    private $manager;

    public function __construct(EntityManagerInterface $manager){
        $this->manager = $manager;

    }

    public function getFiles($user, $userRepo, $sheetRepo){

        $sheets = $this->getSheets($user, $userRepo, $sheetRepo);
        $documents =  $this->getDocuments($user);
        
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

    public function getSheets($user, $userRepo, $sheetRepo){

        // $sheetsUpToDate = [];
        // $sheetsToValidate = [];
        // $sheetsToCorrect = [];
        // $sheetsObsolete = [];
        // $sheetsWellObsolete = [];

            
        //     $parameters = array(
        //         'author'=> $user
        //     );
        
        //     // En cours de validation / A corriger
        //     $sheetsToValidate = $this->manager->createQuery(
        //                             "SELECT s
        //                             FROM App\Entity\Sheet s
        //                             WHERE s.status = 'TO_VALIDATE' AND s.author = :author
        //                             ORDER BY s.updatedAt DESC
        //                             "
        //                         )
        //                         ->setParameters($parameters)
        //                         ->getResult();
                        
        //     // A corriger
        //     $sheetsToCorrect = $this->manager->createQuery(
        //                             "SELECT s
        //                             FROM App\Entity\Sheet s
        //                             WHERE s.status = 'TO_CORRECT' AND s.author = :author
        //                             ORDER BY s.updatedAt DESC
        //                             "
        //                         )
        //                         ->setParameters($parameters)
        //                         ->getResult();
                            

        //     // A jour / Bientôt obsolète / Obsolète

        //     // A jour

        //     $startDate = new DateTime();
        //     $endDate = new DateTime();

        //     $endDate->modify('-3 months');

        //     $parameters = array(
        //         'date_start'=> $startDate,
        //         'date_end'=> $endDate,
        //         'author'=> $user
        //     );

        //     $sheetsUpToDate =  $this->manager->createQuery(
        //         'SELECT s
        //         FROM App\Entity\Sheet s
        //         WHERE s.updatedAt < :date_start AND s.updatedAt > :date_end AND s.author = :author AND s.status IS NULL
        //         '
        //     )
        //     ->setParameters($parameters)
        //     ->getResult();

        //     // Bientôt obsolètes
        //     $startDate = new DateTime();
        //     $endDate = new DateTime();

        //     $startDate->modify('-5 months'); 
        //     $endDate->modify('-6 months'); 

        //     $parameters = array(
        //         'date_start'=> $startDate,
        //         'date_end' => $endDate,
        //         'author'=> $user
        //     );

        //     $q = "";

        //     $sheetsWellObsolete =  $this->manager->createQuery(
        //         'SELECT s
        //         FROM App\Entity\Sheet s
        //         WHERE s.updatedAt < :date_start AND s.updatedAt > :date_end '. $q .' AND s.author = :author AND s.status IS NULL
        //         '
        //     )
        //     ->setParameters($parameters)
        //     ->getResult();

        //     // Obsolète
        //     $startDate = new DateTime();
        //     $endDate = new DateTime();

        //     $startDate->modify('-6 months');
        //     $endDate = "";

            
        //     $parameters = array(
        //         'date_start'=> $startDate,
        //         'author'=> $user
        //     );

        //     $q = "";

        //     $sheetsObsolete =  $this->manager->createQuery(
        //         'SELECT s
        //         FROM App\Entity\Sheet s
        //         WHERE s.updatedAt < :date_start '. $q .' AND s.author = :author AND s.status IS NULL
        //         '
        //     )
        //     ->setParameters($parameters)
        //     ->getResult();
   
        // dump($sheetsUpToDate);


        // return array(
        //     'sheetsToValidate' => $sheetsToValidate,
        //     'sheetsToCorrect' => $sheetsToCorrect,
        //     'sheetsUpToDate' => $sheetsUpToDate,
        //     'sheetsWellObsolete' => $sheetsWellObsolete,
        //     'sheetsObsolete' => $sheetsObsolete
        // );

        $sheets= $sheetRepo->findByAuthor($user);


        dump($sheets);

        // Tri des fiches selon la date
        $sheetsUpToDate = [];
        $sheetsToValidate = [];
        $sheetsToCorrect = [];
        $sheetsObsolete = [];
        $sheetsWellObsolete = [];


        // Bientôt obsolète
        $wellObsolete_start = new DateTime();
        $wellObsolete_start->modify('-5 months');

        $wellObsolete_end = new DateTime();
        $wellObsolete_end->modify('-6 months');

        

        // TRI
        foreach($sheets as $sheet){

        $status = $sheet->getStatus();

            // En attente de validation / A corriger
            if($status){

                // En attente de validation
                if($status == "TO_VALIDATE"){

                    $sheetsToValidate[] = $sheet;

                }

                // A corriger
                if($status == "TO_CORRECT"){

                    $sheetsToValidate[] = $sheet;

                }

            }else{

                $updatedAt = $sheet->getUpdatedAt();

                // Obsolete
                // Supérieur à 6 mois
                if($updatedAt <  $wellObsolete_end){

                    $sheetsObsolete[] = $sheet;


                // Entre 5 et 6 mois
                }elseif($updatedAt <  $wellObsolete_start){

                    $sheetsWellObsolete[] = $sheet;

                }else{

                    $sheetsUpToDate[] = $sheet;

                }

            }

        }
    
        return array(
            'sheetsToValidate' => $sheetsToValidate,
            'sheetsToCorrect' => $sheetsToCorrect,
            'sheetsUpToDate' => $sheetsUpToDate,
            'sheetsWellObsolete' => $sheetsWellObsolete,
            'sheetsObsolete' => $sheetsObsolete
        );

    }

    public function getDocuments($user){

        $documentsUpToDate = [];
        $documentsObsolete = [];
        $documentsWellObsolete = [];

                                 

            // A jour / Bientôt obsolète / Obsolète

            // A jour

            $startDate = new DateTime();
            $endDate = new DateTime();

            $endDate->modify('-3 months');

            $parameters = array(
                'date_start'=> $startDate,
                'date_end'=> $endDate,
                'author'=> $user
            );

            $documentsUpToDate =  $this->manager->createQuery(
                'SELECT d
                FROM App\Entity\Document d
                WHERE d.updatedAt < :date_start AND d.updatedAt > :date_end AND d.author = :author AND d.status IS NULL
                '
            )
            ->setParameters($parameters)
            ->getResult();

            // Bientôt obsolètes
            $startDate = new DateTime();
            $endDate = new DateTime();

            $startDate->modify('-5 months'); 
            $endDate->modify('-6 months'); 

            $parameters = array(
                'date_start'=> $startDate,
                'date_end' => $endDate,
                'author'=> $user
            );

            $q = "";

            $documentsWellObsolete =  $this->manager->createQuery(
                'SELECT d
                FROM App\Entity\Document d
                WHERE d.updatedAt < :date_start AND d.updatedAt > :date_end '. $q .' AND d.author = :author AND d.status IS NULL
                '
            )
            ->setParameters($parameters)
            ->getResult();

            // Obsolète
            $startDate = new DateTime();
            $endDate = new DateTime();

            $startDate->modify('-6 months');
            $endDate = "";

            
            $parameters = array(
                'date_start'=> $startDate,
                'author'=> $user
            );

            $q = "";

            $documentsObsolete =  $this->manager->createQuery(
                'SELECT d
                FROM App\Entity\Document d
                WHERE d.updatedAt < :date_start '. $q .' AND d.author = :author AND d.status IS NULL
                '
            )
            ->setParameters($parameters)
            ->getResult();
   


        return array(
            'documentsUpToDate' => $documentsUpToDate,
            'documentsWellObsolete' => $documentsWellObsolete,
            'documentsObsolete' => $documentsObsolete
        );

    }

    public function getAdminFiles($responsable, $userRepo, $sheetRepo){


        // Recherche des GROUPES dont l'utilisateur est responsable
        $groupes = $responsable->getAdminGroupes();

        // Recherche des UTILISATEURS qui font partie de ce groupe
        $users = [];
        foreach ($groupes as $groupe) {
            $user = $userRepo->findByGroupe($groupe);
            $users = array_merge_recursive($users, $user);
        }

        // Recherche des FICHES des utilisateurs
        $sheets = [];
        foreach ($users as $user) {
        
            $s = $sheetRepo->findByAuthor($user);
            $sheets = array_merge_recursive($sheets, $s);

        }

        dump($sheets);

        // Tri des fiches selon la date
        $sheetsUpToDate = [];
        $sheetsToValidate = [];
        $sheetsToCorrect = [];
        $sheetsObsolete = [];
        $sheetsWellObsolete = [];


        // Bientôt obsolète
        $wellObsolete_start = new DateTime();
        $wellObsolete_start->modify('-5 months');

        $wellObsolete_end = new DateTime();
        $wellObsolete_end->modify('-6 months');

        

        // TRI
        foreach($sheets as $sheet){

        $status = $sheet->getStatus();

            // En attente de validation / A corriger
            if($status){

                // En attente de validation
                if($status == "TO_VALIDATE"){

                    $sheetsToValidate[] = $sheet;

                }

                // A corriger
                if($status == "TO_CORRECT"){

                    $sheetsToValidate[] = $sheet;

                }

            }else{

                $updatedAt = $sheet->getUpdatedAt();

                // Obsolete
                // Supérieur à 6 mois
                if($updatedAt <  $wellObsolete_end){

                    $sheetsObsolete[] = $sheet;


                // Entre 5 et 6 mois
                }elseif($updatedAt <  $wellObsolete_start){

                    $sheetsWellObsolete[] = $sheet;

                }else{

                    $sheetsUpToDate[] = $sheet;

                }

            }

        }
  
        return array(
            'filesToValidate' => $sheetsToValidate,
            'filesToCorrect' => $sheetsToCorrect,
            'filesUpToDate' => $sheetsUpToDate,
            'filesWellObsolete' => $sheetsWellObsolete,
            'filesObsolete' => $sheetsObsolete
        );

    }

    function date_compare($a, $b)
    {
        $t1 = strtotime($a['updatedAt']);
        $t2 = strtotime($b['updatedAt']);
        return $t1 - $t2;
    }   

    // Pour récupérer les notifications, j'ai besoin de récupérer l'ensemble des fiches et documents
    public function getAdminNotifications($current_user, $userRepo, $sheetRepo){

        // Bientôt obsolète
        $wellObsolete_start = new DateTime();
        $wellObsolete_start->modify('-5 months');

        $wellObsolete_end = new DateTime();
        $wellObsolete_end->modify('-6 months');

        $status = "default";
        $icon = "default";
        $text = "default";
        $bool = "default"; 
        
        // Mise en forme dans une trame notification
        $notifications = [];



        // POUR LE RESPONSABLE
        // Recherche des GROUPES dont l'utilisateur est responsable
        $groupes = $current_user->getAdminGroupes();

        // Recherche des UTILISATEURS qui font partie de ce groupe
        $users = [];
        foreach ($groupes as $groupe) {
            $user = $userRepo->findByGroupe($groupe);
            $users = array_merge_recursive($users, $user);
        }

        
        $limit = 5;
        $counter = 0;

        // Recherche des FICHES des utilisateurs (par date)
        foreach ($users as $user) {
        
            $sheets = $sheetRepo->findBy(
                array('author'=> $user), 
                array('updatedAt' => 'DESC')
              );
            

            foreach($sheets as $sheet){

                $updatedAt = $sheet->getUpdatedAt();
                $status = $sheet->getStatus();
                $date = $sheet->getUpdatedAt();

                if($updatedAt < $wellObsolete_start || $status){

                    if($counter < $limit){

                        // Format text
                        $title = $sheet->getTitle();

                        if(strlen($title) > 30){
                            $title = substr($title, 0, 30) . '...';
                        }

                            // En attente de validation / A corriger
                            if($status){

                                // En attente de validation
                                if($status == "TO_VALIDATE"){

                                    $icon = "<i class='uil uil-pause-circle'></i>";
                                    $text = "<strong>". $title ." </strong> est en attente de validation";
                                    $color = "light";

                                }

                                // A corriger
                                if($status == "TO_CORRECT"){

                                    $icon = "<i class='uil uil-exclamation-circle'></i>";
                                    $text = "<strong>". $title ." </strong> est à corriger";
                                    $color = "rouge";

                                }

                            }else{


                                

                                // Obsolete
                                // Supérieur à 6 mois
                                if($updatedAt <  $wellObsolete_end){

                                    $icon = "<i class='uil uil-times-circle'></i>";
                                    $text = "<strong>". $title ." </strong> est obsolète";
                                    $color = "rouge";
                                    $date->modify('+6 months');


                                // Entre 5 et 6 mois
                                }elseif($updatedAt <  $wellObsolete_start){


                                    $icon = "<i class='uil uil-minus-circle'></i>";
                                    $text = "<strong>". $title ." </strong> est bientôt obsolète";
                                    $color = "orange";
                                    $date->modify('+5 months');

                                }

                            }


                            $result = $date->format('Y-m-d H:i:s');

                            
                            setlocale(LC_TIME, "fr_FR", "French");
                            $formated_date = strftime("%d %B %G %H:%M", strtotime($result));
                            $formated_date = mb_convert_encoding($formated_date, 'UTF-8', 'UTF-8');
                            
                            $notifications[] = array(
                                'id' => $sheet->getId(),
                                'icon' => $icon,
                                'text' => $text,
                                'date' => $formated_date,
                                'color' => $color
                                // 'well_obsolete' => $wellObsolete_end,
                                // 'bool' => $bool

                            );

                        

                    }
                    $counter = $counter + 1;

                }
            }

        }
  
        return array(
            
            'notifications' => $notifications,
            'counter' => $counter
        );

    }


    // Pour récupérer les notifications, j'ai besoin de récupérer l'ensemble des fiches et documents
    public function getNotifications($user, $userRepo, $sheetRepo){

        // Bientôt obsolète
        $wellObsolete_start = new DateTime();
        $wellObsolete_start->modify('-5 months');

        $wellObsolete_end = new DateTime();
        $wellObsolete_end->modify('-6 months');

        $status = "default";
        $icon = "default";
        $text = "default";
        $bool = "default"; 
        
        // Mise en forme dans une trame notification
        $notifications = [];

        
        $sheets = $sheetRepo->findBy(
            array('author'=> $user), 
            array('updatedAt' => 'DESC')
            );
        

        $limit = 5;
        $counter = 0;
    
        foreach($sheets as $sheet){

            $updatedAt = $sheet->getUpdatedAt();
            $status = $sheet->getStatus();

            $date = $sheet->getUpdatedAt();

            if($updatedAt < $wellObsolete_start || $status){

                if($counter < $limit){

                    // Format text
                    $title = $sheet->getTitle();

                    if(strlen($title) > 30){
                        $title = substr($title, 0, 30) . '...';
                    }

                        // En attente de validation / A corriger
                        if($status){

                            // En attente de validation
                            if($status == "TO_VALIDATE"){

                                $icon = "<i class='uil uil-pause-circle'></i>";
                                $text = "<strong>". $title ." </strong> est en attente de validation";
                                $color = "light";

                            }

                            // A corriger
                            if($status == "TO_CORRECT"){

                                $icon = "<i class='uil uil-exclamation-circle'></i>";
                                $text = "<strong>". $title ." </strong> est à corriger";
                                $color = "rouge";

                            }

                        }else{
       
                            // Obsolete
                            // Supérieur à 6 mois
                            if($updatedAt <  $wellObsolete_end){

                                $icon = "<i class='uil uil-times-circle'></i>";
                                $text = "<strong>". $title ." </strong> est obsolète";
                                $color = "rouge";
                                $date->modify('+6 months');


                            // Entre 5 et 6 mois
                            }elseif($updatedAt <  $wellObsolete_start){


                                $icon = "<i class='uil uil-minus-circle'></i>";
                                $text = "<strong>". $title ." </strong> est bientôt obsolète";
                                $color = "orange";
                                $date->modify('+5 months');

                            }

                        }

                        $result = $date->format('Y-m-d H:i:s');

                        
                        setlocale(LC_TIME, "fr_FR", "French");
                        $formated_date = strftime("%d %B %G %H:%M", strtotime($result));
                        $formated_date = mb_convert_encoding($formated_date, 'UTF-8', 'UTF-8');

                        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
                        
                        $notifications[] = array(
                            'id' => $sheet->getId(),
                            'icon' => $icon,
                            'text' => $text,
                            'date' => $formated_date,
                            'color' => $color

                            // 'well_obsolete' => $wellObsolete_end,
                            // 'bool' => $bool

                        );

                    

                }
                $counter = $counter + 1;

            }
        }
  
        return array(
            
            'notifications' => $notifications,
            'counter' => $counter
        );

    }


}