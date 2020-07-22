<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Pole;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Poste;
use App\Entity\Sheet;
use App\Entity\Groupe;
use App\Entity\Header;
use App\Entity\Antenne;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Entity\Section;
use App\Entity\Category;
use App\Entity\Paragraph;
use Cocur\Slugify\Slugify;
use App\Entity\SubCategory;
use App\Entity\Interlocutor;
use App\Entity\Organization;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder){
        
        $this->encoder = $encoder;
        
    }


    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        $genres = ['male', 'female'];

        // Gestion des organismes
        $organizations = [];

        for($i = 0; $i <= 30; $i++){

            $organization = new Organization();
            $organization->setName($faker->company())
                         ->setAddress($faker->streetAddress())
                         ->setCity($faker->city())
                         ->setPostCode(21000)
                         ->setPhoneNumber($faker->e164PhoneNumber())
                         ->setEmail($faker->email())
                         ->setWebsite($faker->url());

            $organizations[] = $organization;
            $manager->persist($organization);

                for($j = 0; $j <= mt_rand(2,7); $j++){

                    $interlocutor = new Interlocutor();

                    $genre = $faker->randomElement($genres);

                    $interlocutor->setFirstName($faker->firstName($genre))
                                ->setLastName($faker->lastName())
                                ->setFunction($faker->jobTitle())
                                ->setEmail($faker->email)
                                ->setPhonenumber($faker->e164PhoneNumber())
                                ->setOrganization($organization);

                    $manager->persist($interlocutor);

                }
        }

        // // Gestion des antennes        $antennes = [];

        $antenneNames = array(
            'Centre-Ville',
            'Garantie Jeunes',
            'Fontaine d\'Ouche',
            'Grésilles',
            'Longvic',
            'Quetigny',
            'Chenôve',
            'Talant',
            'Marsannay la Côte',
            'Auxonne');

        for($i = 0; $i <= 9; $i++){
        
            $antenne = new Antenne();
            $city = $faker->city();
            $antenne->setTitle($antenneNames[$i])
                    ->setAddress($faker->streetAddress())
                    ->setPostcode(21000)
                    ->setCity($city)
                    ->setLatitude(47.324098)
                    ->setLongitude(5.037072)
                    ->setPhonenumber($faker->e164PhoneNumber());

            $manager->persist($antenne);
            $antennes[] = $antenne;
        }

        
        // // Gestion des postes
        
        $postes = [];

        $posteNames = array(
            'Conseiller(ère) en insertion professionnelle',
            'Chargé(e) d\'accueil',
            'Psychologue',
            'Responsable de Secteur Vie',
            'Conseiller(ère) PLIE',
            'Assitant(e) administrative',
            'Plateforme Mobilité',
            'Directeur',
            'Directeur Adjoint', 
            'Chargée de communication');

        for($i = 0; $i <= 9; $i++){
        
            $poste = new Poste();
            $poste->setTitle($posteNames[$i]);

            $manager->persist($poste);
            $postes[] = $poste;
        }


        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $adminUser = new User();
        $adminUser->setFirstName('Aleksander')
                ->setLastName('Buczek')
                ->setEmail('aleksander@buczek.fr')
                ->setPhonenumber($faker->e164PhoneNumber())
                ->setIntroduction($faker->sentence(mt_rand(1,2)))
                ->setHash($this->encoder->encodePassword($adminUser, 'password'))
                ->addUserRole($adminRole) 
                ->setAntenne($antennes[array_rand($antennes)])
                // ->setGroupe($groupes[array_rand($groupes)])
                ->setPoste($postes[array_rand($postes)]);

        $manager->persist($adminUser);

        // // Gestion des groupes
        
        // $groupes = [];

        $groupeNames = array(
            'Emploi',
            'Formation',
            'Vie Sociale',
            'Communication',
            'Accueil',
            'Informatique',
            'Garantie Jeunes',
            'Administration',
            'Direction',
            'Autre');

        for($i = 0; $i <= 9; $i++){
        
            $groupe = new Groupe();
            $groupe->setTitle($groupeNames[$i])
                   ->setResponsable($adminUser);

            $manager->persist($groupe);
            $groupes[] = $groupe;
        }


        // $users = [];

        // $firstNames = array(
        //     'Jacques',
        //     'Frédéric',
        //     'Geneviève',
        //     'Delphine',
        //     'Karine',
        //     'Carine',
        //     'Amélie',
        //     'Jonathan',
        //     'Jennifer', 
        //     'Mario');

        // $lastNames = array(
        //     'Sennegon',
        //     'Remond',
        //     'Lhuiller',
        //     'Belle',
        //     'Léon',
        //     'Malardier',
        //     'Morisot',
        //     'Messiant',
        //     'Brocca', 
        //     'Horvat');

        // $genres = ['male', 'female'];

        // // Nous gérons les utilisateurs
        for($i = 0; $i <= 40; $i++){

            $user = new User();

            $genre = $faker->randomElement($genres);

            // $picture = 'https://randomuser.me/api/portraits/';
            // $pictureId = $faker->numberBetween(1, 99) . '.jpg';

            // $picture .= ($genre == 'male' ? 'men/' : 'women/') . $pictureId; // condition ternaire

            $hash = $this->encoder->encodePassword($user, 'password');
            

            $user->setFirstName($faker->firstName($genre))
                 ->setLastName($faker->lastName())
                 ->setEmail($faker->email())
                 ->setPhonenumber($faker->e164PhoneNumber())
                 ->setIntroduction($faker->sentence(mt_rand(1, 2)))
                 ->setHash($hash)
                 ->setAntenne($antennes[array_rand($antennes)])
                 ->setGroupe($groupes[array_rand($groupes)])
                 ->setPoste($postes[array_rand($postes)]);

                $manager->persist($user);
                $users[] = $user;
        }

        // Gestion des Pôles
        for($i = 0; $i <= 4; $i++){
            
            $pole = new Pole();

            $title = $faker->sentence(mt_rand(1,2));

            $slugify = new Slugify();
            $slug = $slugify->slugify($title);

            $colors = array('#00b8d8', '#3f20e7', '#ffb400', '#1adba2', '#ff4169', '#0a0c0d');

            // "bleu" => "#00b8d8",
            // "violet" => "#3f20e7",
            // "jaune" => "#ffb400",
            // "vert" => "#1adba2",
            // "rouge" => "#ff4169",
            // "noir" => "#0a0c0d"

            $pole->setTitle($title)
                 ->setSlug($slug)
                 ->setColor($colors[$i])
                 ->setPlace($i)
                 ->setPictureFilename('pole_default.jpg');

            $manager->persist($pole);

            for($j = 1; $j <= 6; $j++){

                $category = new Category();
    
                $title = $faker->sentence(mt_rand(1,2));
    
                $slugify = new Slugify();
                $slug = $slugify->slugify($title);
    
                $category->setTitle($title)
                         ->setSlug($slug)
                         ->setPole($pole);
                
                $manager->persist($category);

                // Gestion des sous-catégories
                for($k = 1; $k <= mt_rand(3,10); $k++){

                    $subCategory = new SubCategory;

                    $title = $faker->sentence(mt_rand(1,5));

                    $slugify = new Slugify();
                    $slug = $slugify->slugify($title);

                    

                    $subCategory->setTitle($title)
                                ->setSlug($slug)
                                ->setCategory($category);
                                // ->setAuthors($author);
                    
                    
                    $authors = [];
                    
                    for($l = 1; $l <= mt_rand(1,2); $l++){
                        
                        $author = $users[array_rand($users)];
                        $subCategory->addAuthor($author);

                        $authors[] = $author;

                    }

                    $manager->persist($subCategory);

                    // Gestion des fiches (=sheets)
                    for($m = 1; $m <= mt_rand(5,20); $m++){
                        
                        $sheet = new Sheet;

                        $title = $faker->sentence(mt_rand(2,10));
                        $subtitle = $faker->sentence(mt_rand(2,10));
                        $organization = $organizations[array_rand($organizations)];
                        $interlocutors = $organization->getInterlocutors();
                        $updatedAt = $faker->dateTimeBetween('-7 months');

                        $content = '';

                        // Gestion du corps d'une fiche
                        // for($n = 1; $n <= mt_rand(1,6); $n++){
    
                        //     $content .= '<h1>' . $faker->sentence(mt_rand(1,4)) . '</h1><div class="mb-5">';
                            
                        //     for($t = 1; $t <= mt_rand(1,3); $t++){
                        //         $content .= $faker->paragraph(mt_rand(10, 30));
                        //     }
    
                        //     $content .= '</div>';
    
                        // }

                        for($nb = 1; $nb <= mt_rand(2,7); $nb++){

                            $paragraph = new Paragraph();
                            
                            $paragraph->setTitle($faker->sentence(mt_rand(2,6)));
                            $paragraph->setContent($faker->text(1000));
                            $paragraph->setPlace($nb);
                            $paragraph->setSheet($sheet);

                            $manager->persist($paragraph);

                        }

                        $sheet->setTitle($title)
                          ->setSubtitle($subtitle)
                          ->setIntroduction($faker->text(1000))
                          ->setOrganization($organization)
                          ->setUpdatedAt($updatedAt)
                          ->setSubCategory($subCategory)
                          ->setViews(mt_rand(1, 1000))
                          ->setFront(0)
                          ->setAuthor($authors[array_rand($authors)]);

                        $interlocutors = $organization->getInterlocutors();

                        foreach($interlocutors as $interlocutor){
                            
                        // Une chance sur deux d'ajouter un interlocuteur
                            // $chance = mt_rand(1,2);
                                    // if($chance == 1
                                $sheet->addInterlocutor($interlocutor);
                            // }
                            

                        }

                        $manager->persist($sheet);
                    
                    }
                        

                }
            }
        }
        

        
        // // Gestion des catégories et des sous-catégories de la documentation
        // for($i = 1; $i <= 6; $i++){
        //     $category = new Category();

        //     $title = $faker->sentence(mt_rand(1,2));

        //     $slugify = new Slugify();
        //     $slug = $slugify->slugify($title);

        //     $category->setTitle($title)
        //              ->setSlug($slug);

        //     // Gestion des sous-catégories
        //     for($j = 1; $j <= mt_rand(3,10); $j++){

        //         $subCategory = new SubCategory;

        //         $title = $faker->sentence(mt_rand(1,5));

        //         $slugify = new Slugify();
        //         $slug = $slugify->slugify($title);

        //         $author = $users[array_rand($users)];

        //         $subCategory->setTitle($title)
        //                     ->setSlug($slug)
        //                     ->setCategory($category)
        //                     ->setAuthor($author);

        //         $manager->persist($subCategory);

        //         // Gestion des fiches (=sheets)

        //         for($k = 1; $k <= mt_rand(5,20); $k++){
                    
        //             $sheet = new Sheet;

        //             $title = $faker->sentence(mt_rand(2,10));
        //             $organization = $faker->sentence(mt_rand(1,4));
        //             $updatedAt = $faker->dateTimeBetween('-7 months');

        //             $content = '';

        //             // Gestion du corps d'une fiche
        //             for($p = 1; $p <= mt_rand(1,6); $p++){

        //                 $content .= '<h1>' . $faker->sentence(mt_rand(1,4)) . '</h1><div class="mb-5">';
                        
        //                 for($t = 1; $t <= mt_rand(1,3); $t++){
        //                     $content .= $faker->paragraph();
        //                 }

        //                 $content .= '</div>';

        //             }


        //             $sheet->setTitle($title)
        //                   ->setOrganization($organization)
        //                   ->setUpdatedAt($updatedAt)
        //                   ->setContent($content)
        //                   ->setSubCategory($subCategory);


        //             // Gestion des Headers et Sections

        //             // Header
        //             for($l = 1; $l <= mt_rand(1,4); $l++){

        //                 $header = new Header;

        //                 $title = $faker->sentence(mt_rand(1, 2));

        //                 $header->setTitle($title)
        //                        ->setSheet($sheet);

        //                 $manager->persist($header);

        //                     // Section
        //                     for($m = 1; $m <= mt_rand(2,6); $m++){

        //                         $section = new Section;

        //                         $title = $faker->sentence(mt_rand(1, 2));
        //                         $content = $faker->sentence(mt_rand(1, 6));

        //                         $section->setTitle($title)
        //                                ->setContent($content)
        //                                ->setHeader($header);

        //                         $manager->persist($section);
                                
        //                     }

                        

        //             }

        //             $manager->persist($sheet);
        //         }


        //     }

        //     $manager->persist($category);
        // }

        $manager->flush();
    }

    
}
