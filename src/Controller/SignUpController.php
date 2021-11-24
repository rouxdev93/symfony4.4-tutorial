<?php


namespace App\Controller;

use App\Entity\User;
use App\Event\UserSignUpEvent;
use App\Form\UserType;
use App\Service\EmailManager;
use App\Service\UserManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SignUpController extends AbstractController
{
    /**
     * @var EmailManager
     */
    protected $emailManager;

    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /*
     * @var MessageBusInterface
     */
    protected $messageBus;

    /**
     * SignUpController constructor.
     *
     * @param UserManager $userManager
     * @param EmailManager $emailManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param MessageBusInterface $messageBus
     */
    public function __construct(
        UserManager $userManager,
        EmailManager $emailManager,
        EventDispatcherInterface $eventDispatcher,
        MessageBusInterface $messageBus
    ){
        $this->userManager = $userManager;
        $this->emailManager = $emailManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->messageBus = $messageBus;
    }

    /**
     * @Route ("/signup", name="user_signup")
    */
    public function signUp(Request $request)
    {
        $user = new User();
        $form = $this->userManager->createFrom(UserType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $user = $this->userManager->encodepassword($form->getData());

            //TODO: Execute with Messenger component
            /*$emailOptions = $this->emailManager->createEmailOptions(
                $user,
                $this->emailManager->getHtmlFromView('email/signup.html.twig', ['user' => $user])
            );
            $this->messageBus->dispatch(new EmailNotification($emailOptions));*/

            $userSignUpEvent = new UserSignUpEvent($user);
            $this->eventDispatcher->dispatch($userSignUpEvent, UserSignUpEvent::EVENT_SIGN_UP);

            $this->addFlash('notice', 'User is correctly created');
            return $this->redirectToRoute('micro_post_index');
        }

        return $this->render( 'signup/signup.html.twig',[
                'form' => $form->createView(),
            ]
        );
    }
}