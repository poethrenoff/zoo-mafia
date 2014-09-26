<?php
namespace Adminko\Model;

use Adminko\System;
use Adminko\Db\Db;

class BrandModel extends Model
{
    // Возвращает объект бренда по системному имени
    public function getByName($brand_name)
    {
        $record = Db::selectRow('select * from brand where brand_name = :brand_name',
            array('brand_name' => $brand_name));
        if (!$record){
            throw new \AlarmException("Ошибка. Запись {$this->object}({$catalogue_name}) не найдена.");
        }
        return $this->get($record['brand_id'], $record);
    }

    // Возвращает список брендов внутри каталога
    public function getByCatalogue($catalogue)
    {
        $records = Db::selectAll('
            select
                distinct brand.*
            from
                brand
                inner join product on product_brand = brand_id
            where
                product_catalogue = :product_catalogue and product_active = :product_active
            order by
                brand_order',
            array('product_catalogue' => $catalogue->getId(), 'product_active' => 1));
        
        return $this->getBatch($records);        
    }
    
    // Возвращает URL бренда
    public function getBrandUrl()
    {
        return System::urlFor(array('controller' => 'product', 'action' => 'brand', 'brand' => $this->getBrandName()));
    }
}
