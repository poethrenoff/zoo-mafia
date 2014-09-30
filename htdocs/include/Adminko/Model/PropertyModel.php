<?php
namespace Adminko\Model;

class PropertyModel extends Model
{
    // Значение свойства товара
    protected $property_value = null;
    
    // Устанавливает значение свойства товара
    public function setPropertyValue($property_value)
    {
        $this->property_value = $property_value;
        return $this;
    }
    
    // Возвращает значение свойства товара
    public function getPropertyValue()
    {
        return $this->property_value;
    }
    
    // Возвращает список возможных значений свойства
    public function getValueList()
    {
        return Model::factory('property_value')->getList(array('value_property' => $this->getId()), array('value_title' => 'asc'));
    }
}
