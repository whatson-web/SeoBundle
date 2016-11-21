<?php

namespace WH\SeoBundle\Controller\SuperAdmin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use WH\BackendBundle\Controller\Backend\BaseController;

/**
 * @Route("/sudo/seo/config")
 *
 * Class ConfigController
 *
 * @package WH\SeoBundle\Controller\SuperAdmin
 */
class ConfigController extends BaseController
{

	public $bundlePrefix = 'WH';
	public $bundle = 'SeoBundle';
	public $entity = 'Config';
	public $type = 'SuperAdmin';

	/**
	 * @Route("/preview/", name="sudo_wh_seo_config_preview")
	 *
	 * @return string
	 */
	public function previewAction()
	{
		$renderVars = array();

		$entityPathConfig = $this->getEntityPathConfig();

		$config = $this->getConfig($entityPathConfig, 'preview');

		$globalConfig = $this->getGlobalConfig($entityPathConfig);
		$renderVars['globalConfig'] = $globalConfig;

		$renderVars['title'] = $config['title'];

		$renderVars['breadcrumb'] = $this->getBreadcrumb(
			$config['breadcrumb'],
			$entityPathConfig
		);

		$configEntities = $this->getParameter('wh_seo_entities');
		$renderVars['configEntities'] = $configEntities;

		return $this->render(
			'WHSeoBundle:SuperAdmin/Config:preview.html.twig',
			$renderVars
		);
	}

}
