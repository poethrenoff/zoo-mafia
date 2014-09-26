<?php
namespace Adminko\Module;

use Adminko\Model\Model;

class BrandModule extends Module
{
    protected function actionIndex()
    {
        $brand_list = Model::factory('brand')->getList(array(), array('brand_order' => 'asc'));

        $this->view->assign('brand_list', $brand_list);
        $this->content = $this->view->fetch('module/brand/list');
    }
}
