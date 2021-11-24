<?php


namespace App\Controller;

use App\Entity\User;
use App\Service\Base\BaseManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FollowingController
 * @package App\Controller
 * @Security("is_granted('ROLE_USER')")
 * @Route("/following")
 */
class FollowingController extends AbstractController
{
    /**
     * @var BaseManager
     */
    protected $baseManager;

    /**
     * FollowingController constructor.
     *
     * @param BaseManager $baseManager
     */
    public function __construct(
        BaseManager $baseManager
    ){
        $this->baseManager = $baseManager;
    }


    /**
     * @Route("/follow/{id}", name="following_follow")
     */
    public function follow(Request $request, User $userToFollow)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if($currentUser->getId() !== $userToFollow->getId()){
            $currentUser->follow($userToFollow);
            $this->baseManager->save($currentUser);
        }

        return $this->redirectToRoute('micro_post_user', [
            'username' => $userToFollow->getUsername()]
        );
    }

    /**
     * @Route("/unfollow/{id}", name="following_unfollow")
     */
    public function unFollow(Request $request, User $userToUnFollow)
    {
        /** @var User $userToUnFollow */
        $currenUser = $this->getUser();
        $currenUser->unfollow($userToUnFollow);

        $this->baseManager->em->flush();

        return $this->redirectToRoute('micro_post_user', [
                'username' => $userToUnFollow->getUsername()]
        );
    }

}