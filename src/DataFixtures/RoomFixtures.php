<?php

namespace App\DataFixtures;


use App\Entity\Room;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class RoomFixtures extends Fixture
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('en_EN');
        for($i = 0; $i < 15; $i ++){
            $room = new Room();

            $room->setName($faker->city);
            $room->setCapacity(10);
            $room->setFeatures(['Wifi','Chauffage au sol']);
            $room->setPicture($faker->imageUrl());

            $manager->persist($room);
        }

        $manager->flush();
    }
}
