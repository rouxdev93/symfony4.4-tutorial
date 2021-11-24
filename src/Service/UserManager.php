<?php


namespace App\Service;

use App\Entity\User;
use App\Entity\UserPreferences;
use App\Security\TokenGenerator;
use App\Service\Base\BaseManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserManager
 * @package App\Service
 */
class UserManager extends BaseManager
{
    const USER_TOKEN_LENGTH = 30;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    /**
     * UserManager constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param FormFactoryInterface $formFactory
     * @param FlashBagInterface $flashBag
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param TokenGenerator $tokenGenerator
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        UserPasswordEncoderInterface $userPasswordEncoder,
        TokenGenerator $tokenGenerator
    ){
        parent::__construct($entityManager,$formFactory, $flashBag);
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * @return \Doctrine\Persistence\ObjectRepository
     */
    public function getRepository()
    {
        return $this->getObjectRepository(User::class);
    }

    /**
     * @param string $token
     * @return object|null
     */
    public function findOneBy(string $token)
    {
        return $this->getRepository()->findOneBy([
            'confirmationToken' => $token
        ]);
    }

    /**
     * @param User $user
     * @return User
     */
    public function encodepassword(User $user)
    {
        $password = $this->userPasswordEncoder->encodePassword(
            $user,
            $user->getPlainPassword()
        );
        $user->setPassword($password);
        $user->setConfirmationToken($this->tokenGenerator->getRandomSecureToken(self::USER_TOKEN_LENGTH));

        $this->save($user);

        return $user;
    }

    public function createPreferences(User $user, $locale)
    {
        $preferences = new UserPreferences();
        $preferences->setLocale($locale);

        //this doesn't need to be done as we have indicated in the entity
        // that the preferences are goin to be persisted in cascade.
        //$this->em->persist($preferences);

        $user->setPreferences($preferences);

        $this->save($user);

        return $user;
    }

}