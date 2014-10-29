<?php
namespace Adminko\Model;

class PropertyModel extends Model
{
    // Значение свойства товара
    protected $property_value = null;
    
    // Значение свойства одинаково для группы товаров
    protected $is_equal = false;
    
    // Возвращает список возможных значений свойства
    public function getValueList()
    {
        return Model::factory('property_value')->getList(array('value_property' => $this->getId()), array('value_title' => 'asc'));
    }
}
