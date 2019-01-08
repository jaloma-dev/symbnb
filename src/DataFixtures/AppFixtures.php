<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
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
        $faker = Factory::create('FR-fr');

        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $adminUser = new user();
        $adminUser->setFirstName('Benjamin')
            ->setLastName('Beaudoin')
            ->setEmail('benjamin@symfony.com')
            ->setHash($this->encoder->encodePassword($adminUser, 'password'))
            ->setPicture('https://previews.123rf.com/images/2nix/2nix1408/2nix140800099/30818272-anonyme-profil-avatar-ic%C3%B4ne-vector-.jpg')
            ->setIntroduction($faker->sentence())
            ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>')
            ->addUserRole($adminRole);

        $manager->persist($adminUser);

        $users= [];
        $genres = ['male', 'female'];

        for($h=1; $h<=mt_rand(8,15); $h++){
            $user = new User();

            $genre= $faker->randomElement($genres);
            $picture ="https://randomuser.me/api/portraits/";
            $pictureId = $faker->numberBetween(1,99) .'.jpg';

            $picture .= ($genre == 'male' ? 'men/' : 'women/' ) . $pictureId;

            $user->setFirstName($faker->firstName($genre))
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                ->setHash($this->encoder->encodePassword($user, 'password'))
                ->setIntroduction($faker->sentence())
                ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>')
                ->setPicture($picture);

                $manager->persist($user);

                $users[] = $user;
        }
            
        for($i=1; $i<mt_rand(15,35); $i++){
            $ad = new Ad();

            $title = $faker->sentence();
            $coverImage = $faker->imageUrl(1000,350);
            $introduction = $faker->paragraph(2);
            $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';
            
            $user = $users[mt_rand(0, count($users) -1)];

            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(40,200))
                ->setRooms(mt_rand(1,6))
                ->setAuthor($user);
                
            for($j=1; $j <= mt_rand(2,5); $j++){
                $image = new Image();
                
                $image->setUrl($faker->imageUrl())
                    ->setCaption($faker->sentence())
                    ->setAd($ad);
                
                $manager->persist($image);
            }
            $manager->persist($ad);
        }
        $manager->flush();
    }
}
