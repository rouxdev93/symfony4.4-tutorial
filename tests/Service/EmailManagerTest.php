<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Service\EmailManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Twig\Environment;

class EmailManagerTest extends TestCase
{
    public function testSendWelcomeMessage()
    {
        $user = new User();
        $user->setFullName('Olatz Romeo');
        $user->setEmail('oromeo@gmail.com');

        //In a unit test, instead of using real objects that really do send emails...
        // or render Twig templates, we use mocks.
        $symfonyMailerMock = $this->createMock(MailerInterface::class);
        $symfonyMailerMock->expects($this->once())
            ->method('send');

        $twigMock = $this->createMock(Environment::class);

        $mailer = new EmailManager($symfonyMailerMock, $twigMock, 'noreply@example.com');
        $email = $mailer->sendWelcomeMessage($user);

        //These check the subject, that the email is sent to exactly one person
        // and checks to make sure that the "to" has the right info.
        $this->assertSame('Welcome to the MicroPost app!', $email->getSubject());
        $this->assertCount(1, $email->getTo());
        /** @var Address[] $namedAddresses */
        $namedAddresses = $email->getTo();
        $this->assertInstanceOf(Address::class, $namedAddresses[0]);
        $this->assertSame('Olatz Romeo', $namedAddresses[0]->getName());
        $this->assertSame('oromeo@gmail.com', $namedAddresses[0]->getAddress());
    }
}