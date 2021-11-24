<?php


namespace App\MessagesHandler;

use App\Messages\EmailNotification;
use App\Service\EmailManager;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * Class EmailNotificationHandler
 * @package App\MessagesHandler
 */
class EmailNotificationHandler implements MessageHandlerInterface
{
    /**
     * @var MailerInterface
     */
    protected $mailer;

    /**
     * @var EmailManager
     */
    protected $emailManager;

    /**
     * EmailNotificationHandler constructor.
     * @param MailerInterface $mailer
     * @param EmailManager $emailManager
     */
    public function __construct(
        MailerInterface $mailer,
        EmailManager $emailManager
    )
    {
        $this->mailer = $mailer;
        $this->emailManager = $emailManager;
    }


    /**
     * @param EmailNotification $emailOptions
     */
    public function __invoke(EmailNotification $emailOptions)
    {
        $this->emailManager->send($emailOptions);
        // ... do some work - like sending an SMS message!
    }


}