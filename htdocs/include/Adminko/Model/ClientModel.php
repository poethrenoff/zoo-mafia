<?php
namespace Adminko\Model;

use Adminko\Db\Db;

class ClientModel extends Model
{
    // Возвращает объект пользователя по email
    public function getByEmail($client_email, $throw_exception = false)
    {
        $record = Db::selectRow('select * from client where client_email = :client_email',
            array('client_email' => $client_email));
        if (empty($record)) {
            if ($throw_exception) {
                throw new \AlarmException("Ошибка. Запись {$this->object}({$client_email}) не найдена.");
            } else {
                return false;
            }
        }
        return $this->get($record['client_id'], $record);
    }
    
    // Возвращает список заказов
    public function getPurchaseList()
    {
        return model::factory('purchase')->getList(
            array('purchase_client' => $this->getId()), array('purchase_date' => 'desc')
        );
    }
    
    // Возвращает список адресов
    public function getAddressList()
    {
        return model::factory('address')->getList(
            array('address_client' => $this->getId()), array('address_default' => 'desc')
        );
    }
       
    // Возвращает адрес по умолчанию
    public function getDefaultAddress()
    {
        $address_list = Model::factory('address')->getList(
            array('address_client' => $this->getId(), 'address_default' => 1), array(), 1
        );
        if (empty($address_list)) {
            return false;
        }
        return current($address_list);
    }
    
    // Возвращает скидку пользователя
    public function getClientDiscount()
    {
        return 1;
    }    
}