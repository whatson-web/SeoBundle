<?php

namespace WH\SeoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WH\SeoBundle\Entity\Url;

/**
 * Class ProductImporterCommand
 *
 * @package CatalogueBundle\Command
 */
class HardResetCommand extends ContainerAwareCommand
{
    private $container;
    private $em;

    protected function configure()
    {
        $this
            ->setName('wh:seo:hard-reset')
            ->addArgument(
                'entityClass',
                InputArgument::OPTIONAL
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return bool
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container = $this->getContainer();
        $this->em = $this->container->get('doctrine')->getManager();

        if ($input->getArgument('entityClass') !== null) {
            $entityClass = $input->getArgument('entityClass');

            $this->resetUrls($entityClass);
            $this->createUrls($entityClass);
        } else {
            $this->resetRedirections();
            
            $seoEntities = $this->container->getParameter('wh_seo_entities');

            foreach ($seoEntities as $seoEntityClass => $seoEntityConfig) {
                $this->resetUrls($seoEntityClass);
                $this->createUrls($seoEntityClass);
            }
        }

        return true;
    }

    private function resetRedirections()
    {
        $redirections = $this->em->getRepository('WHSeoBundle:Redirection')->get('all');

        foreach ($redirections as $redirection) {
            $this->em->remove($redirection);
            $this->em->flush();
        }
    }

    /**
     * @param $entityClass
     */
    private function resetUrls($entityClass)
    {
        $urls = $this->em->getRepository('WHSeoBundle:Url')->get(
            'all',
            [
                'conditions' => [
                    'url.entityClass' => $entityClass,
                ],
            ]
        );

        foreach ($urls as $url) {
            $this->em->remove($url);
            $this->em->flush();
        }
    }

    /**
     * @param $entityClass
     */
    private function createUrls($entityClass)
    {
        // Création des urls
        $entities = $this->em->getRepository($entityClass)->get('all');

        foreach ($entities as $entity) {
            foreach ($this->container->getParameter('locales') as $locale) {
                $entity->setTranslatableLocale($locale);
                $this->em->refresh($entity);

                $url = $this->container->get('wh_seo.url_generator')->getUrl($entity);

                if (!$entity->getUrl()) {
                    $urlEntity = new Url();

                    $urlEntity->setEntityClass(get_class($entity));
                    $urlEntity->setEntityId($entity->getId());

                    $entity->setUrl($urlEntity);
                } else {
                    $urlEntity = $entity->getUrl();
                }

                $urlEntity->setTranslatableLocale($locale);
                $urlEntity->setUrl($url);

                $this->em->persist($entity);
                $this->em->flush();
            }
        }
    }
}