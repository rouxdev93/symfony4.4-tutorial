<?php

namespace App\Service;

use App\Entity\MicroPost;
use App\Form\MicroPostType;
use App\Service\Base\BaseManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * Class MicroPostManager
 * @package App\Service
 */
class MicroPostManager extends BaseManager
{
    /**
     * MicroPostManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param FormFactoryInterface $formFactory
     * @param FlashBagInterface $flashBag
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag
    ){
        parent::__construct($entityManager, $formFactory, $flashBag);
    }

    /**
     * @return \Doctrine\Persistence\ObjectRepository
     */
    public function getRepository()
    {
        return $this->getObjectRepository(MicroPost::class);
    }

    /**
     * @param MicroPost $microPost
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createMicroPostFrom(MicroPost $microPost)
    {
        return $this->formFactory->create(MicroPostType::class, $microPost);
    }

    /**
     * @param MicroPost $microPost
     */
    public function remove(MicroPost $microPost)
    {
        $this->em->remove($microPost);
        $this->em->flush();
    }


    /**
     * @param $id
     * @return object|null
     */
    public function find($id)
    {
        return $this->getRepository()->find($id);
    }
}