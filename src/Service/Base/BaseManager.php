<?php


namespace App\Service\Base;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * Class BaseManager
 * @package App\Service\Base
 */
class BaseManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var FlashBagInterface
     */
    protected $flashBag;

    /**
     * BaseManager constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param FormFactoryInterface $formFactory
     * @param FlashBagInterface $flashBag
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag
    ){
        $this->em = $entityManager;
        $this->formFactory = $formFactory;
        $this->flashBag = $flashBag;
    }

    /**
     * @param $entityClass
     * @return \Doctrine\Persistence\ObjectRepository
     */
    public function getObjectRepository($entityClass)
    {
        return $this->em->getRepository($entityClass);
    }

    /**
     * @param $formTypeClass
     * @param $entity
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createFrom($formTypeClass, $entity, $options = [])
    {
        return $this->formFactory->create($formTypeClass, $entity, $options = []);
    }

    /**
     * @param $entity
     * @return mixed
     */
    public function save($entity)
    {
        if($entity){
            //There's no need to call persist() whe making changes
            if(!$entity->getId()){
                $this->em->persist($entity);
            }
            $this->em->flush();
        }
        return $entity;
    }

}