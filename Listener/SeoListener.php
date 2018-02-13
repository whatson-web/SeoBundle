<?php

namespace WH\SeoBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\ContainerInterface;
use WH\SeoBundle\Entity\Redirection;
use WH\SeoBundle\Entity\Url;

/**
 * Class SeoListener
 *
 * @package WH\SeoBundle\Listener
 */
class SeoListener implements EventSubscriber
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
        ];
    }

    /**
     * ApplicationFileItemListener constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param LifecycleEventArgs $args
     *
     * @return bool
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $em = $args->getObjectManager();
        $entity = $args->getObject();

        if ($this->entityMustBeManaged($entity)) {
            $this->saveUrl($entity);
        }

        if ($entity instanceof Url) {
            $this->saveRedirectionIfUrlHasBeenRewrited($args);
        }

        return true;
    }

    /**
     * @param LifecycleEventArgs $args
     *
     * @return bool
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $em = $args->getObjectManager();
        $entity = $args->getObject();

        if ($this->entityMustBeManaged($entity)) {
            $this->saveUrl($entity);
        }

        if ($entity instanceof Url) {
            $this->saveRedirectionIfUrlHasBeenRewrited($args);
        }

        return true;
    }

    /**
     * @param LifecycleEventArgs $args
     *
     * @return bool
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($this->entityMustBeManaged($entity)) {
            $this->createRedirection410($entity);
        }

        if ($entity instanceof Url) {
        }

        return true;
    }

    /**
     * @param $entity
     *
     * @return bool
     */
    private function entityMustBeManaged($entity)
    {
        $entityClass = get_class($entity);

        $managedEntities = $this->container->getParameter('wh_seo_entities');

        $entityMustBeManaged = false;
        if (key_exists($entityClass, $managedEntities)) {
            $entityMustBeManaged = true;
        }

        return $entityMustBeManaged;
    }

    /**
     * @param LifecycleEventArgs $args
     *
     * @return bool
     */
    private function saveRedirectionIfUrlHasBeenRewrited(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();
        $changes = $uow->getEntityChangeSet($entity);

        if (isset($changes['url']) && $changes['url'][0] && $changes['url'][0] != $changes['url'][1]) {
            $redirection = new Redirection();
            $redirection->setUrlToRedirect($changes['url'][0]);
            $redirection->setRedirectionUrl($changes['url'][1]);
            $redirection->setRedirectionType(301);

            $em->persist($redirection);

            $entity->setHasBeenRewrited(true);

            $em->persist($entity);

            $em->flush();

            return true;
        }

        return false;
    }

    /**
     * @param $entity
     *
     * @return bool
     */
    private function saveUrl($entity)
    {
        if (method_exists($entity, 'getTranslatableLocale')) {
            $this->container->get('wh_seo.url_generator')->saveUrl($entity, $entity->getTranslatableLocale());
        } else {
            $this->container->get('wh_seo.url_generator')->saveUrl($entity);
        }

        return true;
    }

    /**
     * @param $entity
     *
     * @return bool
     */
    private function createRedirection410($entity)
    {
        $this->container->get('wh_seo.url_generator')->createRedirection410($entity);

        return true;
    }

}