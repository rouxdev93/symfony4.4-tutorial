<?php

namespace App\Controller;

use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 * @package App\Controller
 */
class SecurityController extends AbstractController
{
    /**
     * @var AuthenticationUtils
     */
    protected $authenticationUtils;

    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * SecurityController constructor.
     *
     * @param AuthenticationUtils $authenticationUtils
     * @param UserManager $userManager
     */
    public function __construct(
        AuthenticationUtils $authenticationUtils,
        UserManager $userManager
    ){
        $this->authenticationUtils = $authenticationUtils;
        $this->userManager = $userManager;
    }

    /**
     * @Route  ("/login", name="security_login")
     */
    public function login()
    {
        return $this->render(
            'security/login.html.twig',
            [
                'last_username' => $this->authenticationUtils->getLastUsername(),
                'error' => $this->authenticationUtils->getLastAuthenticationError()
            ]
        );
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {

    }

    /**
     * @Route("/confirm/{token}", name="security_confirm")
     */
    public function confirm(string $token)
    {
        $user = $this->userManager->findOneBy($token);

        if(!is_null($user)){
            $user->setIsActive(true);
            $user->setConfirmationToken('');
            $this->userManager->save($user);
        }
        return $this->render('security/confirmation.html.twig', [
           'user' => $user
        ]);

    }

}