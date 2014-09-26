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
    
    // Возвращает список брендов внутри каталога
    public function getBrandList()
    {
        return Model::factory('brand')->getByCatalogue($this);
    }
    
    // Возвращает URL каталога
    public function getCatalogueUrl()
    {
        return System::urlFor(array('controller' => 'product', 'catalogue' => $this->getCatalogueName()));
    }
}
