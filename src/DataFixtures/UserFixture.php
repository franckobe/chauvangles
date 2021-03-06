<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker;

class UserFixture extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder){
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $role = ['ROLE_ADMIN', 'ROLE_USER'];
        $status = ['0', '1'];

        $faker = Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 25; $i++) {
            $user = new User();
            $user->setemail($faker->email);
            $user->setPassword($this->passwordEncoder->encodePassword($user, "toto"));
            $user->setRoles([$role[array_rand($role)]]);
            $user->setStatus($status[array_rand($status)]);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
