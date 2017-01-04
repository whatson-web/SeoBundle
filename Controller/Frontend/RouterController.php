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
	 * @Route("{url}", name="ft_wh_seo_router_dispatch", requirements={"url":".+"})
	 *
	 * @param string  $url
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function dispatchAction($url = '/', Request $request)
	{
		$em = $this->get('doctrine')->getManager();

		$redirection = $em->getRepository('WHSeoBundle:Redirection')->get(
			'one',
			array(
				'conditions' => array(
					'redirection.urlToRedirect' => $url,
				),
			)
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
				array(
					'url' => $redirection->getRedirectionUrl(),
				),
				$redirection->getRedirectionType()
			);
		}

		$url = $em->getRepository('WHSeoBundle:Url')->get(
			'one',
			array(
				'conditions' => array(
					'url.url' => $url,
				),
			)
		);
		if ($url) {
			$entityClass = $url->getEntityClass();

			$entityBundle = '';
			if (preg_match('#(.*)\\\(.*)\\\Entity\\\.*#', $entityClass)) {
				$entityBundle = preg_replace('#(.*)\\\(.*)\\\Entity\\\.*#', '$1$2', $entityClass);
			}
			if (preg_match('#(.*)\\\Entity\\\.*#', $entityClass)) {
				$entityBundle = preg_replace('#(.*)\\\Entity\\\.*#', '$1', $entityClass);
			}

			$entityName = preg_replace('#.*\\\Entity\\\(.*)#', '$1', $entityClass);

			$entityAction = $entityBundle . ':Frontend/' . $entityName . ':view';

			$response = $this->forward(
				$entityAction,
				array(
					'id'      => $url->getEntityId(),
					'request' => $request,
				)
			);

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
			array(
				'metas' => $metas,
			)
		);
	}

}
