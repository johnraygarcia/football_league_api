<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Team;
use http\Env\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TeamController extends Controller
{

    /**
     * @return \http\Env\Response
     * @Route("/api/teams", name="team_list")
     * @Method("GET")
     */
    public function list() {
        $teams = $this->getDoctrine()->getRepository(Team::class)->findAll();
        $arrayCollection = array();

        foreach($teams as $team) {
            $arrayCollection[] = array(
                'id' => $team->getId(),
                'name' => $team->getName()
            );
        }

        return new JsonResponse($arrayCollection);
    }

    /**
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/api/teams/{slug}/", name="team_detail")
     */
    public function show($slug) {
        $response = new \Symfony\Component\HttpFoundation\Response();
        return $response->setContent(json_encode(["test"=>$slug]));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/api/teams", name="create_team")
     * @Method("POST")
     */
    public function create(){

        $team = new Team();
        $team->setName('Team 1');

        $em = $this->getDoctrine()->getManager();
        $em->persist($team);
        $em->flush();

        return new \Symfony\Component\HttpFoundation\Response('New Team Created', 201);
    }
}
