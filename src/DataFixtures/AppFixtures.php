<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use http\Exception\BadQueryStringException;

abstract class AppFixtures extends Fixture
    /**
     * @var ObjectManager
     */
{    private $manager;
    /**
     * @var Generator
     */
    protected $faker;
    public function load(ObjectManager $manager)
    {


        $this->manager=$manager;
        $this->faker=Factory::create();
        $this->loadData($manager);


    }
    abstract public function loadData(ObjectManager $em);
}
