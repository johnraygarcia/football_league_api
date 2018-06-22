<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Team;
use http\Env\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class TeamController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
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
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/api/teams/{id}/", name="team_detail")
     */
    public function show($id) {

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);
        $team = $this->getDoctrine()
            ->getManager()
            ->getRepository(Team::class)
            ->find($id);
        $jsonContent = $serializer->serialize($team, "json");
        return new JsonResponse(json_decode($jsonContent));
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
