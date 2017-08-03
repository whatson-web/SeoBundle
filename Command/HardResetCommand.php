<?php

namespace WH\SeoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
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
    protected function configure()
    {
        $this
            ->setName('wh:seo:hard-reset')
            ->setDescription('');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return bool
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();

        // Reset des redirections
        $redirections = $em->getRepository('WHSeoBundle:Redirection')->get('all');
        foreach ($redirections as $redirection) {
            $em->remove($redirection);
            $em->flush();
        }

        // Reset des urls
        $urls = $em->getRepository('WHSeoBundle:Url')->get('all');
        foreach ($urls as $url) {
            $em->remove($url);
            $em->flush();
        }

        // Création des urls
        $seoEntities = $container->getParameter('wh_seo_entities');
        foreach ($seoEntities as $seoEntityClass => $seoEntityConfig) {
            $entities = $em->getRepository($seoEntityClass)->get('all');

            foreach ($entities as $entity) {
                foreach ($container->getParameter('locales') as $locale) {
                    $entity->setTranslatableLocale($locale);
                    $em->refresh($entity);

                    $url = $container->get('wh_seo.url_generator')->getUrl($entity);

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

                    $em->persist($entity);
                    $em->flush();
                }
            }
        }

        return true;
    }
}
