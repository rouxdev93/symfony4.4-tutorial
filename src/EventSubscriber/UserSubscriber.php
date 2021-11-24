<?php


namespace App\EventSubscriber;

use App\Event\UserSignUpEvent;
use App\Service\EmailManager;
use App\Service\UserManager;
use Twig\Environment;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UserSubscriber
 * @package App\EventSubscriber
 */
class UserSubscriber implements EventSubscriberInterface
{
    /**
     * @var EmailManager
     */
    private $emailManager;

    /**
     * @var UserManager
     */
    private $userManager;

    private $defaultLocale;

    /**
     * UserSubscriber constructor.
     *
     * @param EmailManager $emailManager
     * @param UserManager $userManager
     * @param string $defaultLocale
     *
     */
    public function __construct(
        EmailManager $emailManager,
        UserManager $userManager,
        string $defaultLocale
    )
    {
        $this->emailManager = $emailManager;
        $this->userManager = $userManager;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
          UserSignUpEvent::EVENT_SIGN_UP => 'onUserSignUp'
        ];
    }

    /**
     * @param UserSignUpEvent $userSignUpEvent
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function onUserSignUp(UserSignUpEvent $userSignUpEvent)
    {
        $user = $userSignUpEvent->getUser();
        $user = $this->userManager->createPreferences($user, $this->defaultLocale);

        $emailParameters = $this->emailManager->createEmailOptions(
            $user,
            'email/signup.html.twig',
            ['user' => $user]
        );

        $templateEmailParameters = $this->emailManager->createTemplateEmailOptions(
            $user,
            'email/signup.html.twig',
            [ 'user' => $user ]);

        $email = $this->emailManager->createEmail($emailParameters);
        $templatedEmail = $this->emailManager->createTemplatedEmail($templateEmailParameters);

        $this->emailManager->send($email);
        $this->emailManager->send($templatedEmail);




    }
}