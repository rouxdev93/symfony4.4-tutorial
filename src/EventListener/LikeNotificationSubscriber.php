<?php


namespace App\EventListener;

use App\Entity\LikeNotification;
use App\Entity\MicroPost;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\PersistentCollection;

class LikeNotificationSubscriber implements EventSubscriber
{
    const LIKED_BY_FIELDNAME = 'likedBy';

    public function getSubscribedEvents()
    {
        return [
            Events::onFlush
        ];
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        /* @var PersistentCollection $collectionUpdate */
        foreach ($uow->getScheduledCollectionUpdates() as $collectionUpdate){

            if(!$collectionUpdate->getOwner() instanceof MicroPost){
                continue;
            }

            $mappingAttr = $collectionUpdate->getMapping();
            if(self::LIKED_BY_FIELDNAME !== $mappingAttr['fieldName']){
                continue;
            }

            //array of elements added to the collection
            $insertDiff = $collectionUpdate->getInsertDiff();
            if (!count($insertDiff)) {
                return;
            }

            /* if(empty($insertDiff)){ //(!count($insertDiff))
                return;
            }*/

            /** @var MicroPost $microPost*/
            $microPost = $collectionUpdate->getOwner();

            $notification = new LikeNotification();
            $notification->setUser($microPost->getUser());
            $notification->setMicroPost($microPost);
            $notification->setLikedBy(reset($insertDiff));

            $em->persist($notification);

            //If you create and persist a new entity in onFlush,
            // then calling EntityManager#persist() is not enough. You have to execute an additional call to
            // $unitOfWork->computeChangeSet($classMetadata, $entity).
            //https://latteandcode.medium.com/symfony-c%C3%B3mo-detectar-cambios-en-una-colecci%C3%B3n-de-una-entidad-f7e1bf5f93ff
            $uow->computeChangeSet(
                $em->getClassMetadata(LikeNotification::class),
                $notification
            );
        }
    }

}