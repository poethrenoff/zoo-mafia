<?php
namespace Adminko\Model;

use Adminko\Db\Db;
use Adminko\Model\Model;

class MarkerModel extends Model
{
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
