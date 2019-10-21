<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Sheet;
use App\Entity\Header;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Entity\Section;
use App\Entity\Category;
use Cocur\Slugify\Slugify;
use App\Entity\SubCategory;
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

        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $adminUser = new User();
        $adminUser->setFirstName('Aleksander')
                ->setLastName('Buczek')
                ->setEmail('aleksander@buczek.fr')
                ->setIntroduction($faker->sentence())
                ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>')
                ->setHash($this->encoder->encodePassword($adminUser, 'password'))
                ->setPicture('https://media.licdn.com/dms/image/C5603AQHu7cPy83UvbA/profile-displayphoto-shrink_200_200/0?e=1573689600&v=beta&t=C7ocQcYLwiW4o_zB9GaSlzHCxUgb0NSpXvU1Dk1ahco')
                ->addUserRole($adminRole);

        $manager->persist($adminUser);

        $users = [];
        $genres = ['male', 'female'];

        // Nous gérons les utilisateurs
        for($i = 1; $i <= 10; $i++){

            $user = new User();

            $genre = $faker->randomElement($genres);

            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1, 99) . '.jpg';

            $picture .= ($genre == 'male' ? 'men/' : 'women/') . $pictureId; // condition ternaire

            $hash = $this->encoder->encodePassword($user, 'password');
            

            $user->setFirstName($faker->firstname($genre))
                 ->setLastName($faker->lastname)
                 ->setEmail($faker->email)
                 ->setIntroduction($faker->sentence())
                 ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>')
                 ->setHash($hash)
                 ->setPicture($picture);

                $manager->persist($user);
                $users[] = $user;
        }

        // Nous gérons les annonces
        for($i = 1; $i <= 30; $i++){

            $ad =  new Ad();

            $title = $faker->sentence();
            $coverImage = $faker->imageUrl(1000, 350);
            $introduction = $faker->paragraph(2);
            $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';

            $user = $users[array_rand($users)]; // array_rand($users) destinée à obtenir un index de tableau aléatoirement

            $ad->setTitle($title)
            ->setCoverImage($coverImage)
            ->setIntroduction($introduction)
            ->setContent($content)
            ->setPrice(mt_rand(40,200))
            ->setRooms(mt_rand(1,5))
            ->setAuthor($user);
        


            for($j = 1; $j <= mt_rand(2,5); $j++){

                $image = new Image();
                $image->setUrl($faker->imageUrl())
                      ->setCaption($faker->sentence())
                      ->setAd($ad);
                
                $manager->persist($image);
            }

            $manager->persist($ad);


             // Gestion des réservations

            for($j = 1; $j <= mt_rand(0,10); $j++){

                $booking = new Booking();

                $createdAt = $faker->dateTimeBetween('-6 months');
                $startDate = $faker->dateTimeBetween('-3 months');

                // Gestion de la date de fin
                $duration = mt_rand(3, 10);

                $endDate = (clone $startDate)->modify("+$duration days");
                $amount = $ad->getPrice() * $duration;
                
                $booker = $users[array_rand($users)];

                $comment = $faker->paragraph();

                $booking->setBooker($booker)
                        ->setAd($ad)
                        ->setStartDate($startDate)
                        ->setEndDate($endDate)
                        ->setCreatedAt($createdAt)
                        ->setAmount($amount)
                        ->setComment($comment);

                
                // Gestion des commentaires

                if(mt_rand(0, 1)){

                    $comment = new Comment;

                    $rating = mt_rand(1,5);
                    $content = $faker->paragraph();

                    $comment->setAd($ad)
                            ->setAuthor($booker)
                            ->setRating($rating)
                            ->setContent($content);

                    $manager->persist($comment);

                }


                $manager->persist($booking);
            }

            

        }

        $manager->persist($ad);

        //
        
        // Gestion des catégories et des sous-catégories de la documentation
        for($i = 1; $i <= 6; $i++){
            $category = new Category();

            $title = $faker->sentence(mt_rand(1,2));

            $slugify = new Slugify();
            $slug = $slugify->slugify($title);

            $category->setTitle($title)
                     ->setSlug($slug);

            // Gestion des sous-catégories
            for($j = 1; $j <= mt_rand(3,10); $j++){

                $subCategory = new SubCategory;

                $title = $faker->sentence(mt_rand(1,5));

                $slugify = new Slugify();
                $slug = $slugify->slugify($title);

                $subCategory->setTitle($title)
                            ->setSlug($slug)
                            ->setCategory($category);

                $manager->persist($subCategory);

                // Gestion des fiches (=sheets)

                for($k = 1; $k <= mt_rand(5,20); $k++){
                    
                    $sheet = new Sheet;

                    $title = $faker->sentence(mt_rand(2,10));
                    $organization = $faker->sentence(mt_rand(1,4));
                    $updatedAt = $faker->dateTimeBetween('-6 months');
                    $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>'; 

                    $sheet->setTitle($title)
                          ->setOrganization($organization)
                          ->setUpdatedAt($updatedAt)
                          ->setContent($content)
                          ->setSubCategory($subCategory);


                    // Gestion des Headers et Sections

                    // Header
                    for($l = 1; $l <= mt_rand(1,4); $l++){

                        $header = new Header;

                        $title = $faker->sentence(mt_rand(1, 2));

                        $header->setTitle($title)
                               ->setSheet($sheet);

                        $manager->persist($header);

                            // Section
                            for($m = 1; $m <= mt_rand(2,6); $m++){

                                $section = new Section;

                                $title = $faker->sentence(mt_rand(1, 2));
                                $content = $faker->sentence(mt_rand(1, 6));

                                $section->setTitle($title)
                                       ->setContent($content)
                                       ->setHeader($header);

                                $manager->persist($section);
                                
                            }

                        

                    }

                    $manager->persist($sheet);
                }


            }

            $manager->persist($category);
        }

        $manager->flush();
    }

    
}
