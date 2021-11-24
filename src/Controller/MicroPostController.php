<?php


namespace App\Controller;

use App\Entity\MicroPost;
use App\Entity\User;
use App\Form\MicroPostType;
use App\Service\MicroPostManager;
use App\Service\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

/**
 * Class MicroPostController
 * @package App\Controller
 *
 * @Route("/micro-post")
 */
class MicroPostController extends AbstractController
{
    /**
     * @var MicroPostManager
     */
    protected $microPostManager;

    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * MicroPostController constructor.
     *
     * @param MicroPostManager $microPostManager
     */
    public function __construct(
        MicroPostManager $microPostManager,
        UserManager $userManager,
        AuthorizationCheckerInterface $authorizationChecker
    ){
        $this->microPostManager = $microPostManager;
        $this->userManager = $userManager;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @Route ("/", name="micro_post_index")
    */
    public function index()
    {
        $user = $this->getUser();
        $usersToFollow = [];
        $microPosRepo = $this->microPostManager->getRepository();

        if($user instanceof User){
            $posts = $microPosRepo->findAllPostByFollowing($user->getFollowing());
            $usersToFollow = count($posts) === 0 ?
                $this->userManager->getRepository()->findByAllWithMoreThan5PostsExceptUser($user): [];
        }else{
            $posts = $microPosRepo->findBy([],['datetime' =>'ASC']);
        }

        return $this->render('micro-post/index.html.twig', [
            'posts' => $posts,
            'usersToFollow' => $usersToFollow
        ]);
    }

    /**
     * @Route ("/edit/{id}", name="micro_post_edit")
     * @Security("is_granted('edit', microPost)", message= "Access denied")
     */
    public function edit(MicroPost $microPost, Request $request)
    {
        //different way to deny the access
        /*$this->denyAccessUnlessGranted('edit', $microPost);
        if(!$this->authorizationChecker->isGranted('edit', $microPost)){
            throw new UnauthorizedHttpException();
        }*/

        $microPost->setDatetime(NEW \DateTime('now'));

        $form = $this->microPostManager->createMicroPostFrom($microPost);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->microPostManager->save($form->getData());
            $this->addFlash('notice', 'MicroPost is correctly removed');
            return $this->redirectToRoute('micro_post_index');
        }

        return $this->render('micro-post/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route ("/remove/{id}", name="micro_post_remove")
     * @Security("is_granted('remove', microPost)", message="Access denied")
     */
    public function remove(MicroPost $microPost)
    {
        //different way to deny the access
        /*$this->denyAccessUnlessGranted('remove', $microPost);
        if(!$this->authorizationChecker->isGranted('remove', $microPost)){
            throw new UnauthorizedHttpException();
        }*/
        $this->microPostManager->remove($microPost);

        $this->addFlash('notice', 'MicroPost is correctly removed');

        return $this->redirectToRoute('micro_post_index');
    }

    /**
     * @Route ("/add", name="micro_post_add")
     * @Security("is_granted('ROLE_USER')")
     */
    public function add(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        $microPost = new MicroPost();
        //$microPost->setDatetime(new \DateTime('now'));
        $microPost->setUser($user);

        $form = $this->microPostManager->createFrom(MicroPostType::class, $microPost);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->microPostManager->save($form->getData());
            $this->addFlash('notice', 'MicroPost is correctly created');
            return $this->redirectToRoute('micro_post_index');
        }

        return $this->render('micro-post/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/user/{username}", name="micro_post_user")
     */
    public function userPosts(User $selectedUser)
    {
        return $this->render('micro-post/user-post.html.twig', [
            //'posts' => $selectedUser->getPosts()
            'posts' => $this->microPostManager->getRepository()->findBy(
                ['user' => $selectedUser],
                ['datetime' => 'DESC']
            ),
            'user' => $selectedUser
        ]);
    }

    /**
     * @Route ("/{id}", name="micro_post_post")
     */
    public function post(MicroPost $microPost)
    {
        if(!$microPost){
            throw new NotFoundResourceException('Post Not Found.');
        }
        return $this->render('micro-post/post.html.twig', [
            'post' => $microPost
        ]);
    }

}