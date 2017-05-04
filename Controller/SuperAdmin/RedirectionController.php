<?php

namespace WH\SeoBundle\Controller\SuperAdmin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use WH\BackendBundle\Controller\Backend\BaseController;

/**
 * @Route("/sudo/seo/redirections")
 *
 * Class RedirectionController
 *
 * @package WH\SeoBundle\Controller\SuperAdmin
 */
class RedirectionController extends BaseController
{

    public $bundlePrefix = 'WH';
    public $bundle = 'SeoBundle';
    public $entity = 'Redirection';
    public $type = 'SuperAdmin';

    /**
     * @Route("/index/", name="sudo_wh_seo_redirection_index")
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
     * @Route("/create/", name="sudo_wh_seo_redirection_create")
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function createAction(Request $request)
    {
        $createController = $this->get('bk.wh.back.create_controller');

        return $createController->create($this->getEntityPathConfig(), $request);
    }

    /**
     * @Route("/update/{id}", name="sudo_wh_seo_redirection_update")
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

    /**
     * @Route("/delete/{id}", name="sudo_wh_seo_redirection_delete")
     *
     * @param         $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction($id)
    {
        $deleteController = $this->get('bk.wh.back.delete_controller');

        return $deleteController->delete($this->getEntityPathConfig(), $id);
    }
}
