<?php
namespace Adminko\Module;

use Adminko\System;
use Adminko\Model\Model;

class ProductModule extends Module
{
    // Вывод полного списка новостей
    protected function actionIndex()
    {
        $catalogue_id = 0;
        $catalogue_name = System::getParam('catalogue');

        if ($catalogue_name) {
            try {
                $catalogue = model::factory('catalogue')->getByName($catalogue_name);
            } catch (AlarmException $e) {
                not_found();
            }
            if (!$catalogue->getCatalogueActive()) {
                not_found();
            }

            $catalogue_id = $catalogue->getId();
            $this->view->assign('catalogue', $catalogue);
        }

        $catalogue_list = Model::factory('catalogue')->getList(
            array('catalogue_active' => 1, 'catalogue_parent' => $catalogue_id), array('catalogue_order' => 'asc')
        );

        if (count($catalogue_list)) {
            $this->view->assign('catalogue_list', $catalogue_list);
            $this->content = $this->view->fetch('module/product/catalogue');
        } else {
            $product_list = model::factory('product')->getList(
                array('product_active' => 1, 'product_catalogue' => $catalogue_id), array('product_order' => 'asc')
            );

            $this->view->assign('product_list', $product_list);
            $this->content = $this->view->fetch('module/product/product');
        }
    }

    protected function actionItem()
    {
        try {
            $product = Model::factory('product')->get(System::id());
        } catch (AlarmException $e) {
            not_found();
        }
        if (!$product->getProductActive()) {
            not_found();
        }
        
        $catalogue_name = System::getParam('catalogue');
        try {
            $catalogue = model::factory('catalogue')->getByName($catalogue_name);
        } catch (AlarmException $e) {
            not_found();
        }
        if (!$catalogue->getCatalogueActive()) {
            not_found();
        }

        $this->view->assign($product);
        $this->content = $this->view->fetch('module/product/item');
    }
}