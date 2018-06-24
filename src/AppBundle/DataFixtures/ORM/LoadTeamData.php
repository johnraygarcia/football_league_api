<?php namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\League;
use AppBundle\Entity\Team;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadTeamData implements FixtureInterface {

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


        // League
        $league = new League();
        $league->setName("FIFA World Cup");
        $manager->persist($league);
        $league = $manager->merge($league);

        // Team 1
        $team1 = new Team();
        $team1->setName("Team 1");
        $team1->setLeague($league);
        $manager->persist($team1);

        // Team 2
        $team2 = new Team();
        $team2->setName("Team 2");
        $team2->setLeague($league);
        $manager->persist($team2);

        $manager->flush();
    }
}