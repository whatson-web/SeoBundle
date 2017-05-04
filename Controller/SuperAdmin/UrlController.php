<?php

namespace WH\SeoBundle\Controller\SuperAdmin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use WH\BackendBundle\Controller\Backend\BaseController;

/**
 * @Route("/sudo/seo/urls")
 *
 * Class UrlController
 *
 * @package WH\SeoBundle\Controller\SuperAdmin
 */
class UrlController extends BaseController
{

    public $bundlePrefix = 'WH';
    public $bundle = 'SeoBundle';
    public $entity = 'Url';
    public $type = 'SuperAdmin';

    /**
     * @Route("/index/", name="sudo_wh_seo_url_index")
     *
     * @param Request $request
     *
     * @return string
     */
    public function indexAction(Request $request)
    {
        $indexController = $this->get('bk.wh.back.index_controller');

        return $indexController->index($this->getEntityPathConfig(), $request);
    }

    /**
     * @Route("/update/{id}", name="sudo_wh_seo_url_update")
     *
     * @param         $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateAction($id, Request $request)
    {
        $updateController = $this->get('bk.wh.back.update_controller');

        return $updateController->update($this->getEntityPathConfig(), $id, $request);
    }
}
