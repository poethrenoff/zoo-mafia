<?php
namespace Adminko\Model;

class AddressModel extends Model
{
    // Делает текущий адрес по умолчанию
    public function makeDefault()
    {
        $client = Model::factory('client')->get($this->getAddressClient());
        $default_address = $client->getDefaultAddress();
        if ($default_address) {
            $default_address->setAddressDefault(false)->save();
        }
        $this->setAddressDefault(true)->save();
    }
    
    // Выбирает новый адрес по умолчанию
    public function assignDefault()
    {
        $client = Model::factory('client')->get($this->getAddressClient());
        $address_list = $client->getAddressList();
        if ($address_list) {
            $default_address = current($address_list);
            if (!$default_address->getAddressDefault()) {
                $default_address->setAddressDefault(true)->save();
            }
        }
    }
}