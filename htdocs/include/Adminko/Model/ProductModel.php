<?php
namespace Adminko\Model;

use Adminko\System;
use Adminko\Model\Model;

class ProductModel extends Model
{
// Возвращает каталог товара
    public function getCatalogue()
    {
        return Model::factory('catalogue')->get($this->getProductCatalogue());
    }

    // Возвращает URL товара
    public function getProductUrl()
    {
        return System::urlFor(array('controller' => 'product',
            'catalogue' => $this->getCatalogue()->getCatalogueName(), 'action' => 'item', 'id' => $this->getId()));
    }
    
    // Возвращает изображения товара
    public function getProductImageList()
    {
        return Model::factory('picture')->getList(
            array('picture_product' => $this->getId()), array('picture_order' => 'asc')
        );
    }
        
    // Возвращает изображение по умолчанию
    public function getProductImage()
    {
        $picture_list = model::factory('picture')->getList(
            array('picture_product' => $this->getId()), array('picture_order' => 'asc'), 1
        );
        if (empty($picture_list)) {
            return get_preference('default_image');
        }
        $default_image = current($picture_list);
        return $default_image->getPictureImage();
    }
       
    // Возвращает маркеры товара
    public function getMarkerList()
    {
        return Model::factory('marker')->getByProduct($this->getId());
    }
}
