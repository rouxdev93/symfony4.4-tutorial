<?php


namespace App\Controller;

use App\Entity\Notification;
use App\Service\NotificationManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use \Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class NotificationController
 * @Security ("is_granted('ROLE_USER')")
 * @Route ("/notification")
 * @package App\Controller
 */
class NotificationController extends AbstractController
{

    /**
     * @var NotificationManager
     */
    protected $notificationManager;

    /**
     * NotificationController constructor.
     */
    public function __construct(NotificationManager $notificationManager)
    {
        $this->notificationManager = $notificationManager;
    }

    /**
     * @Route ("/unread-count", name="notification_unread")
     */
    public function unreadNotificationsCount()
    {
        return new JsonResponse([
            'count' => $this->notificationManager->getRepository()->findUnSeenByUser($this->getUser())
        ]) ;
    }

    /**
     * @Route ("/all", name="notification_all")
     */
    public function showAllNotification()
    {
        return $this->render('notification/notifications.html.twig',
            [
                'notifications' => $this->notificationManager->getRepository()->findBy([
                    'seen' => false,
                    'user' => $this->getUser()
                ])
            ]);
    }

    /**
     * @Route("/acknowledge/{id}", name="notification_acknowledge")
     *
     * @param Notification $notification
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function acknowledge(Notification $notification)
    {
        $notification->setSeen(true);
        $this->notificationManager->save($notification);

        return $this->redirectToRoute('notification_all');
    }


    /**
     * @Route("/acknowledge-all", name="notification_acknowledge_all")
     */
    public function acknowledgeAll()
    {
        $this->notificationManager->markAllAsReadByUser($this->getUser());

        return $this->redirectToRoute('notification_all');
    }

}