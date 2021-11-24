<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class BlogController
 * @Route("/blog")
 * @package App\Controller
 */
class BlogController extends AbstractController
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * BlogController constructor.
     * @param SessionInterface $session
     * @param RouterInterface $router
     */
    public function __construct(
        SessionInterface $session,
        RouterInterface $router
    )
    {
        $this->session = $session;
        $this->router = $router;
    }

    /**
     * @Route("/", name="blog_index")
     *
     * @return Response
     */
    public function index()
    {
        $view = 'blog/index.html.twig';
        $view = 'base.html.twig';
        return $this->render($view,[
            //'posts'=> $this->session->get('posts')
        ]);
    }

    /**
     * @Route("/add", name="blog_add")
     */
    public function add()
    {
        $posts = $this->session->get('posts');
        $posts[uniqid()] = [
          'title' => 'A random title'. rand(1, 500),
          'text' => 'Some random text nr'. rand(1, 500)
        ];

        $this->session->set('posts', $posts);

        return new RedirectResponse($this->router->generate('blog_index'));
    }

    /**
     * @Route("/show/{id}", name="blog_show")
     *
     * @param $id
     * @return Response
     */
    public function show($id)
    {
        $posts = $this->session->get('posts');

        if(!$posts || !isset($posts[$id])){
            throw new NotFoundHttpException('Post not found');
        }

        return $this->render('blog/post.html.twig',[
            'id'=> $id,
            'post' => $posts[$id]
        ]);
    }
}