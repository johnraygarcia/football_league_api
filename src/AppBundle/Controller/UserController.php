<?php namespace AppBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * @Security("is_anonymous() or is_authenticated()")
 */
class UserController extends AbstractController {

    /**
     * @var \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * @var \Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface
     */
    private $jwtEncoder;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $objectManager;

    /**
     * UserController constructor.
     * @param \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface $encoder
     * @param \Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface $jwtEncoder
     * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
     */
    public function __construct(
        UserPasswordEncoderInterface $encoder,
        JWTEncoderInterface $jwtEncoder,
        ObjectManager $objectManager
    ){
        $this->encoder = $encoder;
        $this->jwtEncoder = $jwtEncoder;
        $this->objectManager = $objectManager;
    }

    /**
     * @Route("/user/token")
     * @Method("POST")
     */
    public function tokenAction(Request $request) {
        $user = $this->objectManager
            ->getRepository('AppBundle:User')
            ->findOneBy(["username" => $request->getUser()]);

        if(!$user) {
            throw new BadCredentialsException();
        }

        $isPasswordValid = $this->encoder
            ->isPasswordValid($user, $request->getPassword());

        if(!$isPasswordValid) {
            throw new BadCredentialsException();
        }

        $token = $this->jwtEncoder->encode([
            "username" => $user->getUsername(),
            "exp" => time() + 3600
        ]);

        return new JsonResponse(["token" => $token]);
    }
}