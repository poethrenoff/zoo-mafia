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
    public function getPurchaseCount()
    {
        return model::factory('purchase')->getCount(
            array('purchase_client' => $this->getId())
        );
    }
    
    // Возвращает список заказов
    public function getPurchaseList($limit = null, $offset = null)
    {
        return Model::factory('purchase')->getList(
            array('purchase_client' => $this->getId()), array('purchase_date' => 'desc'), $limit, $offset
        );
    }
    
    // Возвращает список адресов
    public function getAddressList()
    {
        return Model::factory('address')->getList(
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
        $discount_sum = Db::selectCell('select discount_value from discount
                where discount_sum <= :discount_sum order by discount_sum desc',
            array('discount_sum' => $this->getClientSum())
        );
        return (100 - $discount_sum) / 100; 
    }
    
    // Устанавливает сумму заказов пользователя
    public function updateClientSum()
    {
        $client_sum = Db::selectCell('select sum(purchase_sum) from purchase
                where purchase_client = :purchase_client and purchase_status = :purchase_status',
            array('purchase_client' => $this->getId(), 'purchase_status' => 'complete')
        );
        return $this->setClientSum($client_sum);
    }
    
    // Возвращает товары пользователя
    public function getClientProductList()
    {
        return Model::factory('product')->getByClient($this);
    }
    
    // Обновляет список товаров пользователя
    public function updateClientProduct($purchase)
    {
        $client_product_list = array_keys(array_reindex(
            Db::selectAll('select distinct product_id from client_product where client_id = :client_id',
                array('client_id' => $this->getId())), 'product_id'));
        $purchase_product_list = array_keys(array_reindex(
            Db::selectAll('select distinct item_product from purchase_item where item_purchase = :item_purchase',
                array('item_purchase' => $purchase->getId())), 'item_product'));
        
        $diff_product_list = array_diff($purchase_product_list, $client_product_list);
        foreach ($diff_product_list as $product_id) {
            Db::insert('client_product', array('product_id' => $product_id, 'client_id' => $this->getId()));
        }
        return $this;
    }
    
    // Удаляет товар пользователя из списка
    public function deleteClientProduct($product)
    {
        Db::delete('client_product', array('product_id' => $product->getId(), 'client_id' => $this->getId()));
        return $this;
    }
}