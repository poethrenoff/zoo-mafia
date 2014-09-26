<?php
namespace Adminko\Model;

use Adminko\Db\Db;

class MarkerModel extends Model
{
    // Возвращает объект маркера по системному имени
    public function getByName($marker_name)
    {
        $record = Db::selectRow('select * from marker where marker_name = :marker_name',
            array('marker_name' => $marker_name));
        if (!$record){
            throw new \AlarmException("Ошибка. Запись {$this->object}({$marker_name}) не найдена.");
        }
        return $this->get($record['marker_id'], $record);
    }
    
    // Получение маркеров товара
    public function getByProduct($product_id)
    {
        $records = Db::selectAll('
            select marker.* from marker
                inner join product_marker on product_marker.marker_id = marker.marker_id
            where product_marker.product_id = :product_id', array('product_id' => $product_id));
        return $this->getBatch($records);
    }
}
