<?php

namespace WH\SeoBundle\Controller\Frontend;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WH\BackendBundle\Controller\Backend\BaseController;

/**
 * Class RouterController
 *
 * @package WH\SeoBundle\Controller\Frontend
 */
class RouterController extends BaseController
{
    /**
     * @Route("{url}", name="ft_wh_seo_router_dispatch", requirements={"url":".*"})
     *
     * @param string  $url
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function dispatchAction($url = '/', Request $request)
    {
        if ($url == '') {
            $url = '/';
        }

        $em = $this->get('doctrine')->getManager();

        $redirection = $em->getRepository('WHSeoBundle:Redirection')->get(
            'one',
            [
                'conditions' => [
                    'redirection.urlToRedirect' => $url,
                ],
            ]
        );

        if ($redirection) {
            if ($redirection->getRedirectionType() == 410) {
                $response = new Response();
                $response->headers->set('Content-Type', 'text/html');
                $response->setStatusCode(410);

                return $response;
            }

            return $this->redirectToRoute(
                'ft_wh_seo_router_dispatch',
                [
                    'url' => $redirection->getRedirectionUrl(),
                ],
                $redirection->getRedirectionType()
            );
        }

        $conditions = [
            'url.url' => $url,
        ];

        $translationRepository = $em->getRepository('Gedmo\\Translatable\\Entity\\Translation');
        $translationUrl = $translationRepository->createQueryBuilder('translation')
            ->where('translation.content = :translationContent')
            ->andWhere('translation.objectClass = :translationObjectClass')
            ->andWhere('translation.locale = :translationLocale')
            ->setParameter('translationContent', $url)
            ->setParameter('translationObjectClass', 'WH\SeoBundle\Entity\Url')
            ->setParameter('translationLocale', $request->getLocale())
            ->getQuery()
            ->getOneOrNullResult();

        if ($translationUrl) {
            $conditions = [
                'url.id' => $translationUrl->getForeignKey(),
            ];
        }

        $url = $em->getRepository('WHSeoBundle:Url')->get(
            'one',
            [
                'conditions' => $conditions,
            ]
        );
        if ($url) {
            $entityClass = $url->getEntityClass();

            $entities = $this->getParameter('wh_seo_entities');

            if (!empty($entities[$entityClass]['frontController'])) {
                $response = $this->forward(
                    $entities[$entityClass]['frontController'],
                    [
                        'id'      => $url->getEntityId(),
                        'request' => $request,
                    ]
                );
            } else {
                $entityBundle = '';

                if (preg_match('#(.*)\\\(.*)\\\Entity\\\.*#', $entityClass)) {
                    $entityBundle = preg_replace('#(.*)\\\(.*)\\\Entity\\\.*#', '$1$2', $entityClass);
                } elseif (preg_match('#(.*)\\\Entity\\\.*#', $entityClass)) {
                    $entityBundle = preg_replace('#(.*)\\\Entity\\\.*#', '$1', $entityClass);
                }

                $entityName = preg_replace('#.*\\\Entity\\\(.*)#', '$1', $entityClass);

                $entityAction = $entityBundle.':Frontend/'.$entityName.':view';

                $response = $this->forward(
                    $entityAction,
                    [
                        'id'      => $url->getEntityId(),
                        'request' => $request,
                    ]
                );
            }

            return $response;
        }

        throw new NotFoundHttpException('Cette page n\'existe pas ou plus');
    }

    /**
     * @param $entity
     *
     * @return Response
     */
    public function metasAction($entity)
    {
        $metas = $this->get('wh_seo.url_generator')->getMetas($entity);

        return $this->render(
            'WHSeoBundle:FrontEnd/Router:metas.html.twig',
            [
                'metas' => $metas,
            ]
        );
    }

    /**
     * @param         $urlId
     * @param Request $request
     *
     * @return Response
     */
    public function hreflangsAction($urlId, Request $request)
    {
        $em = $this->get('doctrine')->getManager();

        $url = $em->getRepository('WHSeoBundle:Url')->get(
            'one',
            [
                'conditions' => [
                    'url.id' => $urlId,
                ],
            ]
        );

        $locales = $this->getParameter('locales');

        $hreflangs = [];
        foreach ($locales as $locale) {
            if ($locale == $request->getLocale()) {
                continue;
            }

            $url->setTranslatableLocale($locale);
            $em->refresh($url);

            $hreflangs[$locale] = $this->generateUrl(
                'ft_wh_seo_router_dispatch',
                [
                    'url'     => $url->getUrl(),
                    '_locale' => $locale,
                ]
            );
        }

        $url->setTranslatableLocale(null);
        $em->refresh($url);

        return $this->render(
            'WHSeoBundle:FrontEnd/Router:hreflangs.html.twig',
            [
                'hreflangs' => $hreflangs,
            ]
        );
    }

    /**
     * @param         $urlId
     * @param Request $request
     *
     * @return Response
     */
    public function languageSwitcherAction($urlId, Request $request)
    {
        $em = $this->get('doctrine')->getManager();

        $url = $em->getRepository('WHSeoBundle:Url')->get(
            'one',
            [
                'conditions' => [
                    'url.id' => $urlId,
                ],
            ]
        );

        $locales = $this->getParameter('locales');

        $urls = [];
        foreach ($locales as $locale) {
            $url->setTranslatableLocale($locale);
            $em->refresh($url);

            $urls[$locale] = $this->generateUrl(
                'ft_wh_seo_router_dispatch',
                [
                    'url'     => $url->getUrl(),
                    '_locale' => $locale,
                ]
            );
        }

        return $this->render(
            '@WHSeo/FrontEnd/Router/language-switcher.html.twig',
            [
                'defaultLocale' => $request->getLocale(),
                'urls'          => $urls,
            ]
        );
    }

}
