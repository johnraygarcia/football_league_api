<?php namespace AppBundle\Controller;

use AppBundle\Entity\League;
use AppBundle\Entity\Team;
use Doctrine\ORM\NoResultException;
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

        return $this->redirect($this->generateUrl('league_detail', ["id" => $league->getId()]));
    }

    /**
     * @return \http\Env\Response
     * @Route("/api/league/{id}/teams", name="team_list_in_a_league")
     * @Method("GET")
     */
    public function getTeamsInLeague($id) {

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $teams = $this->getDoctrine()
            ->getManager()
            ->createQuery('
                SELECT t FROM ' . Team::class . ' t
                JOIN t.league l
                WHERE l.id=:leagueId')
            ->setParameter('leagueId', $id)
            ->getResult();
        $arrayCollection = array();

        if($teams) :
            foreach($teams as $team) {
                $arrayCollection[] = [
                    'name' => $team->getName(),
                    'strip' => json_decode($serializer
                        ->serialize($team->getStrip(), "json"))
                ];
            }

            return new JsonResponse($arrayCollection);
        endif;

        return new NoResultException("No teams");
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/api/league/{id}", name="delete_league")
     * @Method("DELETE")
     */
    public function delete($id) {

        $em = $this->getDoctrine()
            ->getManager();

        $league = $em->getRepository(League::class)
            ->findOneBy(["id"=>$id]);
        $em->remove($league);
        $em->flush();

        return new \Symfony\Component\HttpFoundation\Response("League successfully deleted", 200);
    }
}
