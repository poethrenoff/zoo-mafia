<?php
namespace Adminko\Model;

use Adminko\System;
use Adminko\Db\Db;

class CatalogueModel extends HierarchyModel
{
    // Возвращает объект каталога по системному имени
    public function getByName($catalogue_name)
    {
        $record = Db::selectRow('select * from catalogue where catalogue_name = :catalogue_name',
            array('catalogue_name' => $catalogue_name));
        if (!$record){
            throw new \AlarmException("Ошибка. Запись {$this->object}({$catalogue_name}) не найдена.");
        }
        return $this->get($record['catalogue_id'], $record);
    }

    // Возвращает список каталогов, содержащих бренд
    public function getByBrand($brand)
    {
        $records = Db::selectAll('
            select
                distinct catalogue.*
            from
                catalogue
                inner join product on product_catalogue = catalogue_id
            where
                product_brand = :product_brand and product_active = :product_active
            order by
                catalogue_order',
            array('product_brand' => $brand->getId(), 'product_active' => 1));
        
        $catalogue_list = $this->getBatch($records);
        
        foreach ($catalogue_list as $catalogue_item) {
            while ($parent_id = $catalogue_item->getParentId()->get()) {
                $catalogue_item = Model::factory('catalogue')->get($parent_id);
                if (!isset($catalogue_list[$parent_id])) {
                    $catalogue_list[$parent_id] = $catalogue_item;
                }
            }
        }
        
        return Model::factory('catalogue')->getTree($catalogue_list);
    }
    
    // Возвращает список брендов внутри каталога
    public function getBrandList()
    {
        return Model::factory('brand')->getByCatalogue($this);
    }
    
    // Возвращает список свойств каталога
    public function getPropertyList($only_filter = false)
    {
        $property_cond = array('property_catalogue' => $this->getId(), 'property_active' => 1);
        if ($only_filter) {
            $property_cond['property_filter'] = 1;
        }
        return Model::factory('property')->getList($property_cond, array('property_order' => 'asc'));
    }
    
    // Возвращает URL каталога
    public function getCatalogueUrl()
    {
        return System::urlFor(array('controller' => 'product', 'catalogue' => $this->getCatalogueName()));
    }
}
