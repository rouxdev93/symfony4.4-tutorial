<?php


namespace App\Controller;

use App\Entity\MicroPost;
use App\Entity\User;
use App\Service\Base\BaseManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LikesController
 * @package App\Controller
 * @Route ("/likes")
 */
class LikesController extends AbstractController
{
    /**
     * @var BaseManager
     */
    protected $baseManager;

    /**
     * LikesController constructor.
     *
     * @param BaseManager $baseManager
     */
    public function __construct(
        BaseManager $baseManager
    ){
        $this->baseManager = $baseManager;
    }

    /**
     * @Route ("/like/{id}", name="likes_like")
    */
    public function  like(MicroPost $microPost)
    {
        $currentUser = $this->getUser();
        if(!$currentUser instanceof User){
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }
        $microPost->like($currentUser);
        $this->baseManager->save($microPost);

        return new JsonResponse([
            'count' => $microPost->getLikedBy()->count()
        ]);
    }

    /**
     * @Route ("/unlike/{id}", name="likes_unlike")
     */
    public function unlike(MicroPost $microPost)
    {
        $currentUser = $this->getUser();
        if(!$currentUser instanceof User){
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }
        $microPost->unlike($currentUser);
        $this->baseManager->save($microPost);

        return new JsonResponse([
            'count' => $microPost->getLikedBy()->count()
        ]);
    }
}