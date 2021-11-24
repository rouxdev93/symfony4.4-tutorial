<?php


namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use App\Service\Base\BaseManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * Class NotificationManager
 * @package App\Service
 */
class NotificationManager extends BaseManager
{
    /**
     * NotificationManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param FormFactoryInterface $formFactory
     * @param FlashBagInterface $flashBag
     */
    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory, FlashBagInterface $flashBag)
    {
        parent::__construct($entityManager, $formFactory, $flashBag);
    }

    /**
     * @return \Doctrine\Persistence\ObjectRepository
     */
    public function getRepository()
    {
        return $this->getObjectRepository(Notification::class);
    }

    public function markAllAsReadByUser(User $user)
    {
        $this->getRepository()->markAllAsReadByUser($user);
        $this->em->flush();
    }

}