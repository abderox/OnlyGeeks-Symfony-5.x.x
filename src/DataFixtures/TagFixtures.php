<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagFixtures extends AppFixtures
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(10, Tag::class, function() {
            $tag = new Tag();
            $tag->setName($this->faker->realText(20));

            return $tag;
        });

        $manager->flush();
    }
}
