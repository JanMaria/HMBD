<?php

namespace App\DataFixtures;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
      $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user
          ->setEmail('bhubhu@bhubhu.pl')
          ->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'zxcvb'
          // ->setPassword('$argon2i$v=19$m=1024,t=2,p=2$c2FXbDYvYkpWdVFpTjJEaA$4lxEWunStvhQ9qx1T4L3Z5rG83tNz3Y1KTJMEgDI9MU');
          ));
        $manager->persist($user);

        $manager->flush();
    }
}
