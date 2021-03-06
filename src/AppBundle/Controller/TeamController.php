<?php namespace AppBundle\Controller;

use AppBundle\Entity\League;
use AppBundle\Entity\Strip;
use AppBundle\Entity\Team;
use AppBundle\Form\StripType;
use AppBundle\Form\TeamType;
use AppBundle\Service\FileUploader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Security("is_anonymous() or is_authenticated()")
 */
class TeamController extends Controller
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var FileUploader
     */
    private $fileUploader;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * TeamController constructor.
     * @param \AppBundle\Service\FileUploader $fileUploader
     */
    public function __construct(
        FileUploader $fileUploader,
        EntityManagerInterface $entityManager
    ){

        $this->serializer = new Serializer(
            array(new ObjectNormalizer()),
            array(new XmlEncoder(), new JsonEncoder()));
        $this->fileUploader = $fileUploader;
        $this->entityManager = $entityManager;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/teams", name="team_list")
     * @Method("GET")
     */
    public function list() {

        $em = $this->entityManager;
        $teams = $em->getRepository(Team::class)->findAll();
        $arrayCollection = array();

        foreach($teams as $team) {
            $arrayCollection[] = [
                'name' => $team->getName(),
                'strip' => json_decode($this->serializer
                    ->serialize($team->getStrip(), "json")),
                'league' => json_decode($this->serializer
                    ->serialize($team->getLeague(), "json"))
            ];
        }

        return new JsonResponse($arrayCollection);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/api/teams/{id}/", name="team_detail")
     */
    public function show($id) {

        $team = $this
                    ->entityManager
                    ->getRepository(Team::class)
                    ->find($id);
        $jsonContent = $this->serializer->serialize($team, "json");
        return new JsonResponse(json_decode($jsonContent));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/api/teams", name="create_team")
     * @Method("POST")
     */
    public function create(Request $request) {

        $data = json_decode($request->getContent());
        $leagueId = $data->league->id;

        $league = $this->entityManager
            ->getRepository(League::class)
            ->find($leagueId);

        $team = new Team();
        $team->setName($data->name)
            ->setLeague($league);

        $this->entityManager->persist($league);
        $this->entityManager->persist($team);
        $this->entityManager->flush();

        return new \Symfony\Component\HttpFoundation\Response('New Team Created', 201);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/api/teams", name="update_team")
     * @Method("PUT")
     */
    public function update(Request $request) {


        $data = json_decode($request->getContent(), true);
        $id = $data['id'];
        $managedTeam = $this->entityManager
            ->getRepository(Team::class)->find($id);
        $managedTeam->setName($data['name']);
        if(array_key_exists('league', $data)) {
            $leagueId = $data['league']['id'];
            $league = $this->entityManager->getRepository(League::class)
                ->find($leagueId);
            $managedTeam->setLeague($league);
        }

        $this->entityManager->persist($managedTeam);
        $this->entityManager->flush();

        return $this->redirect($this->generateUrl('team_detail', ["id" => $managedTeam->getId()]));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/api/teams/strip", name="upload_team_strip")
     * @Method("POST")
     */
    public function uploadStrip(Request $request){

        $team = $this
                ->getDoctrine()
                ->getRepository(Team::class)
                ->find($request->get("team_id"));

        $strip = new Strip();
        $form = $this->createForm(StripType::class, $strip);
        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $request->files->get('strip');
            $targetDir = $this->getParameter('strips_directory');
            $fileName = $this->fileUploader->upload($file, $targetDir);

            $em = $this->entityManager;
            $strip->setStrip('/uploads/strips/'. $fileName);
            $team->setStrip($strip);
            $em->persist($strip);
            $em->persist($team);
            $em->flush();

            return $this->redirect($this->generateUrl('team_detail', ["id" => $team->getId()]));
        }
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }
}
