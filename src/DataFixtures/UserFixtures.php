<?php

namespace App\DataFixtures;


use App\Entity\User;
use Behat\Transliterator\Transliterator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class UserFixtures extends Fixture
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for($i = 0; $i < 15; $i ++){

            $user = new User();
            $firstName = $faker->firstName;
            $lastName = $faker->lastName;

            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $user->setEmail(Transliterator::transliterate($firstName).'.'.Transliterator::transliterate($lastName).'@reunion.it');
            $user->setPassword(password_hash('user', PASSWORD_BCRYPT));
            $user->setRoles(['ROLE_EMPLOYEE']);

            $manager->persist($user);
        }

        $user = new User();

        $firstName = $faker->firstName;
        $lastName = $faker->lastName;

        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setEmail(Transliterator::transliterate($firstName).'.'.Transliterator::transliterate($lastName).'@reunion.it');
        $user->setPassword(password_hash('admin', PASSWORD_BCRYPT));
        $user->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);


//        $manager->flush();
    }
}