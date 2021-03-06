<?php
namespace Adminko\Model;

use Adminko\System;
use Adminko\Db\Db;

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
    
    // Возвращает бренд товара
    public function getBrand()
    {
        return Model::factory('brand')->get($this->getProductBrand());
    }
    
    // Возвращает изображения товара
    public function getPictureList()
    {
        return Model::factory('picture')->getList(
            array('picture_product' => $this->getId()), array('picture_order' => 'asc')
        );
    }
        
    // Возвращает изображение по умолчанию
    public function getProductImage()
    {
        $picture_list = Model::factory('picture')->getList(
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
        
    // Возвращает список товаров по маркеру
    public function getByMarker($marker_name, $limit = 3)
    {
        $records = Db::selectAll('
            select product.* from product
                inner join catalogue on product_catalogue = catalogue_id
                inner join product_marker using(product_id)
                inner join marker using(marker_id)
            where marker_name = :marker_name and 
                product_active = :product_active and catalogue_active = :catalogue_active
            order by rand() limit ' . $limit,
            array('marker_name' => $marker_name, 'product_active' => 1, 'catalogue_active' => 1));
        
        return $this->getBatch($records);
    }
        
    // Возвращает фасовки товара
    public function getPackageList()
    {
        return Model::factory('package')->getList(
            array('package_product' => $this->getId()), array('package_order' => 'asc')
        );
    }
    
    // Возвращает фасовку по умолчанию
    public function getDefaultPackage()
    {
        $package_list = model::factory('package')->getList(
            array('package_product' => $this->getId()), array('package_order' => 'asc'), 1
        );
        if (empty($package_list)) {
            return false;
        }
        return current($package_list);
    }
       
    // Устанавливает цену по умолчанию
    public function setDefaultPrice()
    {
        $package = $this->getDefaultPackage();
        
        $this->setProductPrice(
            $package ? $package->getPackagePrice() : 0
        );
        
        return $this;
    }
    
    // Поисковый запрос
    public function getSearchResult($search_value, $order = array())
    {
        $search_words = preg_split('/\s+/', $search_value);
            
        $filter_clause = array();
        foreach (array('product_title', 'product_description') as $field_name) {
            $field_filter_clause = array();
            foreach ($search_words as $search_index => $search_word) {
                $field_prefix = $field_name . '_' . $search_index;
                $field_filter_clause[] = 'lower(' . $field_name . ') like :' . $field_prefix;
                $filter_binds[$field_prefix] = '%' . mb_strtolower($search_word , 'utf-8') . '%';
            }
            $filter_clause[] = join(' and ', $field_filter_clause);
        }
        
        $order_conds = array();
        foreach ($order as $field => $dir) {
            $order_conds[] = "{$field} {$dir}";
        }
        
        $records = Db::selectAll('
            select product.* from product
                inner join catalogue on product.product_catalogue = catalogue.catalogue_id
            where (' . join(' or ', $filter_clause) . ') and
                product_active = :product_active and catalogue_active = :catalogue_active
            ' . ($order_conds ? 'order by ' . join(', ', $order_conds) : ''),
            $filter_binds + array('product_active' => 1, 'catalogue_active' => 1)
        );
        
        return $this->getBatch($records);
    }
        
    // Возвращает свойства товара
    public function getPropertyList()
    {
        $product_property_list = Db::selectAll('
                select
                    property.*, ifnull(property_value.value_title, product_property.value) as property_value
                from
                    property
                    left join product_property on product_property.property_id = property.property_id
                    inner join product on product_property.product_id = product.product_id and
                        product.product_catalogue = property.property_catalogue
                    left join property_value on property_value.value_property = property.property_id and
                        property_value.value_id = product_property.value
                where
                    product_property.product_id = :product_id and property.property_active = :property_active
                order by
                    property.property_order',
            array('product_id' => $this->getId(), 'property_active' => 1)
        );
        
        $property_list = array();
        foreach ($product_property_list as $product_property) {
            $property = Model::factory('property')->get($product_property['property_id'], $product_property)
                ->setPropertyValue($product_property['property_value']);
            $property_list[$property->getId()] = $property;
        }
        return $property_list;
    }
            
    // Возвращает список товаров пользователя
    public function getByClient($client)
    {
        $records = Db::selectAll('
            select distinct product.*
            from product
                inner join client_product using(product_id)
            where client_id = :client_id and product_active = :product_active',
                array('client_id' => $client->getId(), 'product_active' => 1));
        
        return $this->getBatch($records);
    }
            
    // Возвращает список товаров по бренду
    public function getByBrand($limit = 3)
    {
        $records = Db::selectAll('
            select product.* from product
                inner join catalogue on product_catalogue = catalogue_id
            where product_brand = :product_brand and product_id <> :product_id and 
                product_active = :product_active and catalogue_active = :catalogue_active
            order by rand() limit ' . $limit,
            array('product_brand' => $this->getProductBrand(), 'product_id' => $this->getId(),
                'product_active' => 1, 'catalogue_active' => 1));
        
        return $this->getBatch($records);
    }
    
    // Добавляет оценку товару
    public function addMark($mark)
    {
        $voters = $this->getProductVoters();
        $rating = $this->getProductRating();
        
        $this->setProductVoters($voters + 1);
        $this->setProductRating(($rating * $voters + $mark) / ($voters + 1));
        
        return $this;
    }

    // Возвращает отзывы к товару
    public function getReviewList()
    {
        return Model::factory('review')->getList(
            array('review_product' => $this->getId(), 'review_status' => 'public'),
                array('review_date' => 'desc')
        );
    }
}
