<?php namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData implements FixtureInterface, ContainerAwareInterface {

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager){

        $passwordEncoder = $this->container->get('security.password_encoder');
        $user1 = new User();
        $user1->setUsername("john_doe");
        $user1->setPassword($passwordEncoder->encodePassword($user1, 'secret123'));
        $manager->persist($user1);

        $user2 = new User();
        $user2->setUsername("jane_doe");
        $user2->setPassword($passwordEncoder->encodePassword($user2, 'secret321'));
        $manager->persist($user2);

        $manager->flush();
    }

    public function setContainer(ContainerInterface $container = null){
        $this->container = $container;
    }
}