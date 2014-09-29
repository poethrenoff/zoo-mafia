<?php
namespace Adminko\Model;

class PropertyModel extends Model
{
    // Возвращает значения свойства
    public function getValueList()
    {
        return Model::factory('property_value')->getList(array('value_property' => $this->getId()), array('value_title' => 'asc'));
    }
}
