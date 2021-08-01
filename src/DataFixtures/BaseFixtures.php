<?php

namespace App\DataFixtures;

use App\Entity\Helo;

use Doctrine\Persistence\ObjectManager;

class BaseFixtures extends AppFixtures
{
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
        for($i=0 ;$i<40;$i++)
        {
            $helo = new Helo();
            $helo->setTitle($this->faker->city);
            $helo->setAge($this->faker->randomElement(self::$age));
            $manager->persist($helo);
        }
        $manager->flush();


    }
}
