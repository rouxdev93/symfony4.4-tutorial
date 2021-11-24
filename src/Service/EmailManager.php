<?php


namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;

/**
 * Class EmailManager
 * @package App\Service
 */
class EmailManager
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var string
     */
    private $emailFrom;

    /**
     * UserSubscriber constructor.
     *
     * @param MailerInterface $mailer
     * @param Environment $twig
     * @param string $emailFrom
     *
     */
    public function __construct(
        MailerInterface $mailer,
        Environment $twig,
        string $emailFrom
    )
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->emailFrom = $emailFrom;
    }


    /**
     * @param User $user
     * @param $htmlTemplate
     * @param array $htmlParameters
     * @return array
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function createEmailOptions(User $user, $htmlTemplate, array $htmlParameters)
    {
        return $emailParameters = [
            'from' => $this->emailFrom,
            'to' => $user->getEmail(),
            //'cc' => '', //Copies of an email to recipients other than the main recipients.PUBLIC
            //'bcc' => '',//CCO.Copies of an email to recipients other than the main recipients.PRIVATE
            //'replyTo' => 'olatz@example.com',
            //'priority' => Email::PRIORITY_HIGH,
            'subject' => 'Welcome to the app!',
            //'text' => 'TSending emails is fun again!',
            'html' => $this->getHtmlFromView($htmlTemplate, $htmlParameters)
        ];
    }


    /**
     * @param User $user
     * @param $htmlTemplate
     * @param array $htmlParameters
     * @return array
     */
    public function createTemplateEmailOptions(User $user, $htmlTemplate, array $htmlParameters)
    {
        return [
            'from' => $this->emailFrom,
            'to' => $user->getEmail(),
            'subject' => 'Welcome to the app with Template!',
            'htmlTemplate' => $htmlTemplate,
            'context' => $htmlParameters
        ];
    }

    /**
     * @param array $emailParameters
     * @return Email
     */
    public function createEmail(array $emailParameters)
    {
        $email = (new Email())
            ->from($emailParameters['from'])
            ->to($emailParameters['to'])
            //->cc($options['cc'])
            //->bcc($options['bcc'])
            //->replyTo($options['replyTo'])
            //->priority($options['priority'])
            ->subject($emailParameters['subject'])
            //->text($emailParameters['text'])
            ->html($emailParameters['html'])
        ;

        return $email;
    }

    /**
     * @param array $emailParameters
     * @return TemplatedEmail
     */
    public function createTemplatedEmail(array $emailParameters)
    {
        $templateEmail = (new TemplatedEmail())
            ->from($emailParameters['from'])
            ->to(new Address($emailParameters['to']))
            ->subject($emailParameters['subject'])
            ->htmlTemplate($emailParameters['htmlTemplate'])// path of the Twig template to render
            ->context($emailParameters['context']) // pass variables (name => value) to the template
        ;

        return $templateEmail;
    }

    /**
     * @param $view
     * @param array $parameters
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function getHtmlFromView($view, array $parameters)
    {
        return $this->twig->render($view, $parameters);
    }

    /**
     * @param $email
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function send($email)
    {
        $this->mailer->send($email);
    }


    public function sendWelcomeMessage(User $user)
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->emailFrom, 'Default EmailFrom'))
            ->to(new Address($user->getEmail(), $user->getFullName()))
            ->subject('Welcome to the MicroPost app!')
            ->htmlTemplate('email/signup.html.twig')
            ->context([
                'user' => $user,
            ]);
        $this->mailer->send($email);

        return $email;
    }
}