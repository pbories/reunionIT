<?php
/**
 * Created by PhpStorm.
 * User: Etudiant
 * Date: 29/01/2019
 * Time: 12:41
 */

namespace App\DataFixtures;


use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserTestFixtures extends Fixture
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $admin1 = new User();
        $admin1->setFirstName('Jacques');
        $admin1->setLastName('Grenier');
        $admin1->setEmail('jacques.grenier@reunion.it');
        $admin1->setPassword(password_hash('adminadmin', PASSWORD_BCRYPT));
        $admin1->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin1);

        $admin2 = new User();
        $admin2->setFirstName('Margot');
        $admin2->setLastName('Hoarau');
        $admin2->setEmail('margot.hoarau@reunion.it');
        $admin2->setPassword(password_hash('adminadmin', PASSWORD_BCRYPT));
        $admin2->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin2);

        $employee1 = new User();
        $employee1->setFirstName('Lucas');
        $employee1->setLastName('Le Goff');
        $employee1->setEmail('lucas.le-goff@reunion.it');
        $employee1->setPassword(password_hash('user', PASSWORD_BCRYPT));
        $employee1->setRoles(['ROLE_EMPLOYEE']);
        $manager->persist($employee1);

        $employee2 = new User();
        $employee2->setFirstName('Charles');
        $employee2->setLastName('Delorme');
        $employee2->setEmail('charles.delorme@reunion.it');
        $employee2->setPassword(password_hash('user', PASSWORD_BCRYPT));
        $employee2->setRoles(['ROLE_EMPLOYEE']);
        $manager->persist($employee2);

        $employee3 = new User();
        $employee3->setFirstName('Catherine');
        $employee3->setLastName('Hamel');
        $employee3->setEmail('catherine.hamel@reunion.it');
        $employee3->setPassword(password_hash('user', PASSWORD_BCRYPT));
        $employee3->setRoles(['ROLE_EMPLOYEE']);
        $manager->persist($employee3);

        $guest1 = new User();
        $guest1->setFirstName('Marc');
        $guest1->setLastName('Vallet');
        $guest1->setEmail('marc.vallet@reunion.it');
        $guest1->setPassword(password_hash('guest', PASSWORD_BCRYPT));
        $guest1->setRoles(['ROLE_GUEST']);
        $manager->persist($guest1);

        $guest2 = new User();
        $guest2->setFirstName('Gabriel');
        $guest2->setLastName('Jourdan');
        $guest2->setEmail('gabriel.jourdan@reunion.it');
        $guest2->setPassword(password_hash('guest', PASSWORD_BCRYPT));
        $guest2->setRoles(['ROLE_GUEST']);
        $manager->persist($guest2);

        $manager->flush();
    }

}