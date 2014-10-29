<?php
namespace Adminko\Module;

use Adminko\System;
use Adminko\Compare;
use Adminko\Model\Model;

class CompareModule extends Module
{
    protected function actionIndex()
    {
        $compare = Compare::factory();
        
        if ($compare->getType()) {
            $catalogue = model::factory('catalogue')->get($compare->getType());
            
            $property_list = $catalogue->getPropertyList();
            
            $product_list = array(); $property_compare_list = array();
            foreach ($compare->get() as $catalogue_id => $product_list) {
                foreach ($product_list as $product_id) {
                    $product_list[$product_id] = Model::factory('product')->get($product_id);
                    $product_property_list = $product_list[$product_id]->getPropertyList();
                    foreach ($product_property_list as $property_id => $product_property) {
                        $property_compare_list[$property_id][$product_id] =
                            $product_property->getPropertyValue();
                    }
                }
            }
            foreach ($property_compare_list as $property_id => $property_value_list) {
                $property_list[$property_id]->setIsEqual(count($property_value_list) > 1 &&
                    count($property_value_list) == $compare->count() && count(array_unique($property_value_list)) == 1);
                if ($property_list[$property_id]->getIsEqual() && init_string('show') == 'diff') {
                    unset($property_list[$property_id]);
                }
            }
            
            $this->view->assign('product_list', $product_list);
            $this->view->assign('property_list', $property_list);
            
            $this->view->assign('property_compare_list', $property_compare_list);
        }
        
        $this->output['product'] = true;
        $this->content = $this->view->fetch('module/compare/index');
    }
    
    protected function actionInfo()
    {
        $this->view->assign(Compare::factory());
        $this->content = $this->view->fetch('module/compare/info');
    }
    
    protected function actionAdd()
    {
        $product = $this->getProduct(System::id());
        
        $compare = Compare::factory();
        $limit = max(1, intval($this->getParam('limit')));
        
        if ($compare->getType() && $compare->getType() != $product->getProductCatalogue() && !isset($_REQUEST['confirm'])) {
            $this->content = json_encode(array(
                'confirm' => 'Сравнение товаров разных типов невозможно. Добавить новый товар, удалив прежний список?',
            ));
        } else {
            $compare->add($product->getId(), $product->getProductCatalogue());
            
            $this->view->assign($compare);
            $this->view->fetch('module/compare/info');
            
            $this->content = json_encode(array(
                'message' => $this->view->fetch('module/compare/info'),
            ));
        }
    }
    
    protected function actionDelete()
    {
        Compare::factory()->delete(System::id());
        System::redirectBack();
    }
    
    protected function actionClear()
    {
        Compare::factory()->clear();
        System::redirectBack();
    }
       
    /**
     * Получение товара
     */
    public function getProduct($id)
    {
        try {
            $product = Model::factory('product')->get($id);
        } catch (\AlarmException $e) {
            System::notFound();
        }
        if (!$product->getProductActive()) {
            System::notFound();
        }
        return $product;
    }
    
    // Отключаем кеширование
    protected function getCacheKey()
    {
        return false;
    }
}
