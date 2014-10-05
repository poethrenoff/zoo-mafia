<?php
namespace Adminko\Module;

use Adminko\Date;
use Adminko\System;
use Adminko\View;
use Adminko\Model\Model;

class ProductModule extends Module
{
    protected $sort_field = '';

    protected $sort_order = '';

    // Вывод списка товаров
    protected function actionIndex()
    {
        $catalogue_id = 0;
        $catalogue_name = System::getParam('catalogue');

        if ($catalogue_name) {
            $catalogue = $this->getCatalogue($catalogue_name);
            
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
            $product_filter = array('product_active' => 1, 'product_catalogue' => $catalogue_id);
            if ($brand_id = init_string('brand')) {
                $product_filter['product_brand'] = $brand_id;
            }
            
            $property_request = init_array('property');
            foreach ($catalogue->getPropertyList(true) as $property) {
                if ($property->getPropertyKind() == 'select' && isset($property_request[$property->getId()]) && !is_empty($property_request[$property->getId()])) {
                    $product_filter[] = "exists(select true from product_property where product_id = product.product_id and property_id = {$property->getId()} and value = " . intval($property_request[$property->getId()]) . ")";
                }
                if ($property->getPropertyKind() == 'boolean' && isset($property_request[$property->getId()]) && !is_empty($property_request[$property->getId()]) && in_array($property_request[$property->getId()], array('yes', 'no'))) {
                    $product_filter[] = "exists(select true from product_property where product_id = product.product_id and property_id = {$property->getId()} and value = " . ($property_request[$property->getId()] == 'no' ? 0 : 1) . ")";
                }
                if ($property->getPropertyKind() == 'number' && isset($property_request[$property->getId()]['from']) && !is_empty($property_request[$property->getId()]['from'])) {
                    $product_filter[] = "exists(select true from product_property where product_id = product.product_id and property_id = {$property->getId()} and value >= " . intval($property_request[$property->getId()]['from']) . ")";
                }
                if ($property->getPropertyKind() == 'number' && isset($property_request[$property->getId()]['to']) && !is_empty($property_request[$property->getId()]['to'])) {
                    $product_filter[] = "exists(select true from product_property where product_id = product.product_id and property_id = {$property->getId()} and value <= " . intval($property_request[$property->getId()]['to']) . ")";
                }
            }
            
            $this->setSortMode();
            
            $product_list = model::factory('product')->getList($product_filter,
                array($this->sort_field => $this->sort_order)
            );

            $this->view->assign('product_list', $product_list);
            $this->content = $this->view->fetch('module/product/list');
        }
    }

    // Вывод списка товаров по бренду
    protected function actionBrand()
    {
        $brand_name = System::getParam('brand');

        try {
            $brand = model::factory('brand')->getByName($brand_name);
        } catch (\AlarmException $e) {
            System::notFound();
        }

        $this->view->assign('brand', $brand);
        
        $product_filter = array('product_active' => 1, 'product_brand' => $brand->getId());
        if ($catalogue_id = init_string('catalogue')) {
            $product_filter['product_catalogue'] = $catalogue_id;
        }

        $this->setSortMode();

        $product_list = model::factory('product')->getList($product_filter,
            array($this->sort_field => $this->sort_order)
        );

        $this->view->assign('product_list', $product_list);
        $this->content = $this->view->fetch('module/product/list');
    }

    protected function actionItem()
    {
        $product = $this->getProduct(System::id());
        
        $this->view->assign($product);
        $this->view->assign('client', ClientModule::getInfo());
        $this->content = $this->view->fetch('module/product/item');
    }
    
    protected function actionVote()
    {
        $product = $this->getProduct(System::id());
        $product->addMark(min(5, max(1, init_string('mark'))))->save();
        
        $this->content = json_encode(
            array('rating' => $product->getProductRating())
        );
    }

    protected function actionReview()
    {
        if (!ClientModule::isAuth()) {
            System::redirectTo(array('controller' => 'client'));
        } else {
            $client = ClientModule::getInfo();
            $product = $this->getProduct(System::id());

            try {
                $this->addReview($client, $product);
                
                $this->content = json_encode(
                    array('message' => get_preference('review_message'))
                );
            } catch (\AlarmException $e) {
                $this->content = json_encode(
                    array('error' => $e->getMessage())
                );
            }
        }
    }

    protected function actionMenu()
    {
        $catalogue_tree = Model::factory('catalogue')->getTree(
            Model::factory('catalogue')->getList(
                array('catalogue_active' => 1), array('catalogue_order' => 'asc')
            )
        );

        $this->view->assign($catalogue_tree);
        $this->content = $this->view->fetch('module/product/menu');
    }

    protected function actionMarker()
    {
        foreach (array('novelty', 'bestseller', 'bestprice') as $marker_name) {
            $marker = Model::factory('marker')->getByName($marker_name);
            $product_list = Model::factory('product')->getByMarker($marker_name);

            if ($product_list) {
                $marker_view = new View();
                $marker_view->assign('marker', $marker);
                $marker_view->assign('product_list', $product_list);

                $this->content .= $marker_view->fetch('module/product/marker');
            }
        }
    }

    protected function setSortMode()
    {
        $sort = init_string('sort');
        $order = init_string('order');

        $this->sort_order = $order == 'desc' ? 'desc' : 'asc';
        $this->view->assign('order', $this->sort_order);

        if ($sort == 'price') {
            $this->sort_field = 'product_price';
            $this->view->assign('sort', 'price');
        } elseif ($sort == 'name') {
            $this->sort_field = 'product_title';
            $this->view->assign('sort', 'name');
        } else {
            $this->sort_field = 'product_order';
            $this->view->assign('sort', 'order');
        }
    }
  
    // Добавляет отзыв к товару
    public function addReview($client, $product)
    {
        $review_text = trim(init_string('review_text'));
        
        if (is_empty($review_text)) {
            throw new \AlarmException('Поле обязательно для заполнения');
        }

        // Добавление отзыва
        $review = Model::factory('review')
            ->setReviewClient($client->getId())
            ->setReviewProduct($product->getId())
            ->setReviewText($review_text)
            ->setReviewDate(Date::now())
            ->setReviewStatus('new')
            ->save();
        
        return $review;
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
    
    /**
     * Получение каталога
     */
    public function getCatalogue($catalogue_name)
    {
        try {
            $catalogue = model::factory('catalogue')->getByName($catalogue_name);
        } catch (\AlarmException $e) {
            System::notFound();
        }
        if (!$catalogue->getCatalogueActive()) {
            System::notFound();
        }
        return $catalogue;
    }

}
