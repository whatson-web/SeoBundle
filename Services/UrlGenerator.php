<?php

namespace WH\SeoBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use WH\LibBundle\Twig\SlugExtension;
use WH\SeoBundle\Entity\Redirection;
use WH\SeoBundle\Entity\Url;

/**
 * Class UrlGenerator
 *
 * @package WH\SeoBundle\Services
 */
class UrlGenerator
{
    protected $container;
    protected $em;

    /**
     * UrlGenerator constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
    }

    /**
     * @param $entity
     *
     * @return string
     */
    public function getUrl($entity)
    {
        $entityClass = get_class($entity);

        $managedEntities = $this->container->getParameter('wh_seo_entities');

        $urlFields = [];

        if (key_exists($entityClass, $managedEntities)) {
            $managedEntity = $managedEntities[$entityClass];
            $urlFields = $managedEntity['urlFields'];
        }

        $urlRepository = $this->em->getRepository('WHSeoBundle:Url');
        $uselfulFunctions = $this->container->get('lib.useful_functions');

        $newUrl = '';

        foreach ($urlFields as $urlField) {
            switch ($urlField['type']) {
                case 'field':
                    $value = $uselfulFunctions->getRecursiveValueOfEntity($entity, $urlField['field']);

                    // Si on a trouvé la valeur du champ, on l'ajoute au l'url
                    if ($value) {
                        // Et éventuellement son préfixe
                        if (isset($urlField['prefix'])) {
                            $newUrl .= $urlField['prefix'];
                        }

                        $newUrl .= $value;

                        // Et/Ou son suffixe
                        if (isset($urlField['suffix'])) {
                            $newUrl .= $urlField['suffix'];
                        }
                    }

                    break;

                case 'slugField':
                    $value = $uselfulFunctions->getRecursiveValueOfEntity($entity, $urlField['field']);
                    $value = strtolower(str_replace(' ', '-', $value));

                    // Si on a trouvé la valeur du champ, on l'ajoute au l'url
                    if ($value) {
                        // Et éventuellement son préfixe
                        if (isset($urlField['prefix'])) {
                            $newUrl .= $urlField['prefix'];
                        }

                        $newUrl .= $value;

                        // Et/Ou son suffixe
                        if (isset($urlField['suffix'])) {
                            $newUrl .= $urlField['suffix'];
                        }
                    }

                    break;

                case 'tree':
                    $field = $urlField['field'];

                    if ($entity->{'get'.ucfirst($field)}()) {
                        $fieldValue = $entity->{'get'.ucfirst($field)}();

                        $fieldUrl = $urlRepository->get(
                            'one',
                            [
                                'conditions' => [
                                    'url.entityClass' => $urlField['entity'],
                                    'url.entityId'    => $fieldValue->getId(),
                                ],
                            ]
                        );

                        // Todo : gérer nombre de niveaux maxs, début/fin (avec une regex)
                        if ($fieldUrl && $fieldUrl->getUrl() != '/') {
                            $newUrl = $fieldUrl->getUrl();
                        }
                    }

                    break;

                case 'string':
                    $newUrl .= $urlField['string'];
                    break;
            }
        }

        if ($entityClass == 'CmsBundle\Entity\Page' && $entity->getPageTemplateSlug() == 'home') {
            $newUrl = '';
        }

        return $newUrl;
    }

    /**
     * @param      $entity
     * @param null $locale
     *
     * @return bool
     */
    public function saveUrl($entity, $locale = null)
    {
        $entityClass = get_class($entity);
        $entityId = $entity->getId();

        $managedEntities = $this->container->getParameter('wh_seo_entities');

        $urlFields = [];

        if (key_exists($entityClass, $managedEntities)) {
            $managedEntity = $managedEntities[$entityClass];
            $urlFields = $managedEntity['urlFields'];
        }

        if (empty($urlFields)) {
            return false;
        }

        $newUrl = $this->getUrl($entity);

        $currentUrl = $entity->getUrl();

        if ($locale && $currentUrl instanceof Url) {
            $currentUrl->setTranslatableLocale($locale);
            $this->em->refresh($currentUrl);
        }

        // Nouvelle url détectée ?
        if (!$currentUrl || $currentUrl->getUrl() != $newUrl) {
            if ($currentUrl) {
                $url = $currentUrl;
            } else {
                $url = new Url();
                $url->setEntityClass($entityClass);
                $url->setEntityId($entityId);
            }
            $url->setUrl($newUrl);

            $this->em->persist($url);

            $entity->setUrl($url);
            $this->em->persist($entity);

            // Y avait-il déjà une url ?
            if ($currentUrl && $currentUrl->getUrl() != $newUrl) {
                // Si l'url a été réécrite manuellement, on ne doit plus l'écraser
                if ($currentUrl->getHasBeenRewrited()) {
                    return false;
                }

                $redirection = new Redirection();
                $redirection->setUrlToRedirect($currentUrl->getUrl());
                $redirection->setRedirectionUrl($newUrl);
                $redirection->setRedirectionType(301);

                $this->em->persist($redirection);
            }

            // Suppression des redirections déjà existantes pour l'url qui va être créée
            $existingRedirections = $this->em->getRepository('WHSeoBundle:Redirection')->get(
                'all',
                [
                    'conditions' => [
                        'redirection.urlToRedirect' => $newUrl,
                    ],
                ]
            );
            foreach ($existingRedirections as $existingRedirection) {
                $this->em->remove($existingRedirection);
            }

            $this->em->flush();

            // Création en cascade
            if (method_exists($entity, 'getChildren') && $entity->getChildren()) {
                foreach ($entity->getChildren() as $child) {
                    $this->saveUrl($child);
                }
            }
        }

        return true;
    }

    /**
     * @param $entity
     *
     * @return bool
     */
    public function createRedirection410($entity)
    {
        $redirection = new Redirection();
        $redirection->setUrlToRedirect($entity->getUrl()->getUrl());
        $redirection->setRedirectionType(410);

        $this->em->persist($redirection);
        $this->em->flush();

        return true;
    }

    /**
     * @param $entity
     *
     * @return array
     */
    public function getMetas($entity)
    {
        $entityClass = get_class($entity);

        $managedEntities = $this->container->getParameter('wh_seo_entities');

        $metas = [
            'title'       => '',
            'description' => '',
            'robots'      => 'index,follow',
        ];

        $defaultMetasFields = [];
        if (key_exists($entityClass, $managedEntities)) {
            $managedEntity = $managedEntities[$entityClass];
            if (isset($managedEntity['defaultMetasFields'])) {
                $defaultMetasFields = $managedEntity['defaultMetasFields'];
            }
        }

        $usefulFunctions = $this->container->get('lib.useful_functions');

        if (!empty($defaultMetasFields)) {
            foreach ($defaultMetasFields as $metaField => $entityField) {
                $metaFieldValue = $usefulFunctions->getRecursiveValueOfEntity($entity, $entityField);
                if ($metaFieldValue) {
                    $metas[$metaField] = $metaFieldValue;
                }
            }
        }

        if ($entity->getMetas()) {
            $entityMetas = $entity->getMetas();
            if ($entityMetas->getTitle()) {
                $metas['title'] = $entityMetas->getTitle();
            }
            if ($entityMetas->getDescription()) {
                $metas['description'] = $entityMetas->getDescription();
            }
            if ($entityMetas->getRobots()) {
                $metas['robots'] = $entityMetas->getRobots();
            }
        }

        return $metas;
    }

}
