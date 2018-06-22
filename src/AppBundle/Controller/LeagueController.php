<?php

namespace AppBundle\Controller;

use AppBundle\Entity\League;
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

class LeagueController extends Controller
{

    /**
     * @return \http\Env\Response
     * @Route("/api/leagues", name="league_list")
     * @Method("GET")
     */
    public function list() {
        $leagues = $this->getDoctrine()->getRepository(League::class)->findAll();
        $arrayCollection = array();

        foreach($leagues as $league) {
            $arrayCollection[] = array(
                'id' => $league->getId(),
                'name' => $league->getName()
            );
        }

        return new JsonResponse($arrayCollection);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/api/leagues/{id}/", name="league_detail")
     */
    public function show($id) {

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);
        $team = $this->getDoctrine()
            ->getManager()
            ->getRepository(League::class)
            ->find($id);
        $jsonContent = $serializer->serialize($team, "json");
        return new JsonResponse(json_decode($jsonContent));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/api/leagues", name="create_league")
     * @Method("POST")
     */
    public function create(Request $request){

        $data = json_decode($request->getContent(), true);
        $league = new League();
        $league->setName($data['name']);

        $em = $this->getDoctrine()->getManager();
        $em->persist($league);
        $em->flush();

        return new \Symfony\Component\HttpFoundation\Response('New League Created', 201);
    }
}
