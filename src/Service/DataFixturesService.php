<?php

namespace App\Service;

use App\Entity\MicroPost;
use App\Entity\User;
use App\Entity\UserPreferences;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\Common\DataFixtures\SharedFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class DataFixturesService
 * @package App\Service
 */
class DataFixturesService
{
    private const NUMBER_POSTS = 30;

    private const USERS = [
        [
            'username' => 'olatz',
            'password' => 'olatzdev',
            'email' => 'olatz@gmail.com',
            'fullName' => 'Olatz Romeo',
            'roles' => [User::ROLE_USER]
        ],
        [
            'username' => 'david',
            'password' => 'daviddev',
            'email' => 'david@gmail.com',
            'fullName' => 'David Martin',
            'roles' => [User::ROLE_USER]
        ],
        [
            'username' => 'gus',
            'password' => 'gusdev',
            'email' => 'gus@gmail.com',
            'fullName' => 'Gustavo Verge',
            'roles' => [User::ROLE_USER]
        ],
        [
            'username' => 'superadmin',
            'password' => 'superadmindev',
            'email' => 'superadmin@gmail.com',
            'fullName' => 'Super Admin',
            'roles' => [User::ROLE_ADMIN]
        ]
    ];

    private const POSTS = [
        'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
        'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium.',
        'At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti.',
        'ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.',
        'Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae.',
        'On the other hand, we denounce with righteous indignation and dislike men who are so beguiled and demoralized by the charms.',
        'So blinded by desire, that they cannot foresee the pain and trouble that are bound to ensue.',
    ];

    private const LANGUAGES = [
        'es',
        'en'
    ];


    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    /**
     * DataFixturesService constructor.
     * @param EntityManagerInterface $entityManager
     * @param UserManager $userManager
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserManager $userManager,
        UserPasswordEncoderInterface $userPasswordEncoder
    ){
        $this->em = $entityManager;
        $this->userManager = $userManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->referenceRepository = new ReferenceRepository($this->em);
    }


    /**
     * Create MICROPOST entity fixtures
     */
    public function createMicroPostFixtures()
    {
        for ($i = 0; $i < (self::NUMBER_POSTS); $i++){
            $microPost = new MicroPost();
            $microPost->setText(
                self::POSTS[rand(0, count(self::POSTS)-1)]
            );

            $dateTime = new \DateTime();
            //on Timestamp format
            $randomDate = $this->getRandomDate();
            $microPost->setDatetime($dateTime->setTimestamp($randomDate));

            $microPost->setUser($this->referenceRepository->getReference(
                self::USERS[rand(0, count(self::USERS)-1)]['username'])
            );

            $this->em->persist($microPost);
        }
    }

    /**
     * Create USER entity fixtures
     */
    public function createUserFixtures()
    {
        foreach(self::USERS as $userData)
        {
            $user = new User();
            $user->setUsername($userData['username']);
            $user->setFullName($userData['fullName']);
            $user->setEmail($userData['email']);
            $user->setPassword(
                $this->userPasswordEncoder->encodePassword(
                    $user,
                    $userData['password']
                )
            );
            $user->setIsActive(true);

            $user->setRoles($userData['roles']);

            $this->referenceRepository->addReference(
                $userData['username'],
                $user
            );

            //user is persisted on this required method
            $this->userManager->createPreferences($user, self::LANGUAGES[rand(0,1)]);
        }
    }

    /**
     * @return false|string
     * @throws \Exception
     */
    private function getRandomDate()
    {
        //current date
        $currentDate = new \DateTime(date('Y-m-d H:m:s'));

        // Convert the supplied date to timestamp
        $fMin = strtotime($currentDate->format('Y-m-d H:m:s'));
        $fMax = strtotime($currentDate->add(new \DateInterval('P1M'))->format('Y-m-d H:m:s')); //1 month later

        // Generate a random number from the start and end dates
        $fVal = mt_rand($fMin, $fMax);

        // Convert back to the specified date format
        return date($fVal);
    }
}