<?php

namespace App\DataFixtures;

use App\Entity\FriendShip;
use App\Entity\Helo;

use App\Entity\PostComment;
use App\Entity\PostDislikes;
use App\Entity\PostLike;
use App\Entity\Profile;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class BaseFixtures extends AppFixtures implements DependentFixtureInterface
{
    /**
     * @var UserPasswordHasherInterface
     */
    private $encoder;
    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder=$encoder;
    }
    private static $age = [
        12,
        14,
        12,
        18,
        70,
        32,
    ];
    private static $title = [
       'alloha',
        'batona',
        'isslama',
        'balata', 'khaybo3a', 'namibia', 'zaytona', 'labala',
    ];
    public function loadData(ObjectManager $manager)
    {

        $user1 = new User();
        $user1->setEmail('abdelhadi12mouzafir@gmail.com');
        $user1->setName('Mouzafir');
        $user1->setPrename('Abdelhadi');
        $user1->setOGusername('OG.rox');
        $user1->setRoles(["ROLE_ADM"]);
        $user1->setPassword($this->encoder->hashPassword($user1,'896618google'));
        $user1->setGender(0);
        $user1->setIsVerified(true);
        $manager->persist($user1);
        $Users[]=$user1;

        for($i=0 ;$i<40;$i++)
        {
            $user = new User();
            $profile= new Profile();
            $user->setEmail($this->faker->email);
            $user->setName($this->faker->lastName);
            $user->setPrename($this->faker->firstName);
            $user->setPassword($this->encoder->hashPassword($user,'password'));
            $user->setIsVerified($this->faker->boolean(60));
            $user->setOGusername('OG.'.$this->faker->name);
            $user->setGender($this->faker->numberBetween(0,2));
            $manager->persist($user);
            $profile->setUser($user);
            $profile->setAbout($this->faker->realText(100));
            $manager->persist($profile);
            $users[]=$user;
        }

        for($i=0 ;$i<40;$i++)
        {
            $helo = new Helo();
            $helo->setTitle($this->faker->sentence(2));
            $helo->setContent($this->faker->realText(200));
            $helo->setAge($this->faker->numberBetween(1,1000000));
            $helo->setUser($this->faker->randomElement($users));
            $tags = $this->getRandomReferences(Tag::class, $this->faker->numberBetween(0, 5));
            foreach ($tags as $tag) {
                $helo->addTag($tag);
            }
            $helo->setUpdatedAt($this->faker->dateTimeBetween('-30 days','now'));
            $helo->setPublishedAt($helo->getUpdatedAt());
            $manager->persist($helo);
            for($j=0;$j<mt_rand(0,50);$j++)
            {
                $pin=new PostLike();
                $pin->setPin($helo);
                $pin->setUser($this->faker->randomElement($users));
                $manager->persist($pin);


            }
            for($j=0;$j<mt_rand(0,20);$j++)
            {

                $dis=new PostDislikes();
                $dis->setPin($helo);
                $dis->setUser($this->faker->randomElement($users));
                $manager->persist($dis);

            }
            for($j=0;$j<mt_rand(0,45);$j++)
            {

                $comment=new PostComment();
                $comment->setComment($this->faker->realText(30));
                $comment->setUser($this->faker->randomElement($users));
                $comment->setPin($helo);
                $comment->setIsDeleted($this->faker->boolean(10));
                $comment->setCreatedAt($this->faker->dateTimeBetween('-50 days','now'));
                $manager->persist($comment);

            }

            for($j=0;$j<mt_rand(0,70);$j++)
            {

                $friend=new FriendShip();
                $friend->setUser($this->faker->randomElement($users));
                $friend->setFriend($this->faker->randomElement($users));
                $manager->persist($friend);

            }

        }

        $manager->flush();


    }
    public function getDependencies()
    {
        return [
            TagFixtures::class,
        ];
    }
}
