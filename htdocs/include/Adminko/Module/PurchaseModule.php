<?php
namespace Adminko\Module;

use Adminko\Cart;
use Adminko\Mail;
use Adminko\Session;
use Adminko\View;
use Adminko\Model\Model;
use Adminko\Module\Module;
use Adminko\Valid\Valid;

class PurchaseModule extends Module
{
    /**
     * Текущий пользователь
     */
    protected $client = null;
    
    /**
     * Корзина
     */
    protected $cart = null;
    
    /**
     * Оформление заказа
     */
    protected function actionIndex()
    {
        if (session::flash('purchase_complete')) {
            $this->content = $this->view->fetch('module/purchase/complete');
        } else {
            $this->client = ClientModule::getInfo();
            $this->cart = Cart::factory();
            
            $error = !empty($_POST) && $this->cart->getQuantity() ? $this->addPurchase() : array();
            
            $delivery_list = Model::factory('delivery')
                ->getList(array('delivery_active' => 1), array('delivery_price' => 'asc'));
            $this->view->assign('delivery_list', $delivery_list);

            $this->view->assign('free_delivery_sum', get_preference('free_delivery_sum'));
            
            $this->view->assign('error', $error);
            $this->view->assign('cart', $this->cart);
            $this->view->assign('client', $this->client);
            $this->content = $this->view->fetch('module/purchase/form');
        }
    }
    
    /**
     * Создание заказа
     */
    protected function addPurchase()
    {
        $error = array();
        
        $field_list = array(
            'client_title', 'client_email', 'client_phone',
            'client_address', 'client_address_new', 'client_address_text',
            'purchase_request', 'purchase_comment',
            'purchase_delivery');
        foreach ($field_list as $field_name) {
            $$field_name = trim(init_string($field_name));
        }
        
        if ($this->client) {
            if (is_empty($client_phone)) {
                $error['client_phone'] = 'Поле обязательно для заполнения';
            }
            
            if ($client_address_new) {
                if (is_empty($client_address_text)) {
                    $error['client_address'] = 'Поле обязательно для заполнения';
                }
            } else {
                try {
                    $address = Model::factory('address')->get($client_address);
                } catch (\AlarmException $e) {
                    $error['client_address'] = 'Поле обязательно для заполнения';
                }
                if (is_empty($error['client_address']) &&
                        $address->getAddressClient() != $this->client->getId()) {
                    $error['client_address'] = 'Поле обязательно для заполнения';
                }
                if (is_empty($error['client_address'])) {
                    $client_address_text = $address->getAddressText();
                }
            }
        } else {
            $field_list = array(
                'client_title', 'client_email', 'client_phone', 'client_address_text');
            foreach ($field_list as $field_name) {
                if (is_empty($$field_name)) {
                    $error[$field_name] = 'Поле обязательно для заполнения';
                }
            }

            if (!isset($error['client_email']) && !Valid::factory('email')->check($client_email)) {
                $error['client_email'] = 'Поле заполнено некорректно';
            }
            if (!isset($error['client_email']) && Model::factory('client')->getByEmail($client_email)) {
                $error['client_email'] = 'Пользователь с таким электронным адресом уже зарегистрирован';
            }
        }
        
        try {
            $delivery = Model::factory('delivery')->get($purchase_delivery);
        } catch (\AlarmException $e) {
            $error['purchase_delivery'] = 'Не выбран способ доставки';
        }
                
        if (count($error)) {
            return $error;
        }
        
        if (!$this->client) {
            $client_password = generate_key(8);
            
            // Добавление пользователя
            $client = Model::factory('client')
                ->setClientTitle($client_title)
                ->setClientEmail($client_email)
                ->setClientPassword(md5($client_password))
                ->setClientPhone($client_phone)
                ->save();
            
            // Добавление адреса
            $address = Model::factory('address')
                ->setAddressTitle('Основной адрес')
                ->setAddressText($client_address_text)
                ->setAddressClient($client->getId())
                ->setAddressDefault(true)
                ->save();
            
            $from_email = get_preference('from_email');
            $from_name = get_preference('from_name');
            $subject = get_preference('registration_subject');

            $message = TextModule::getByTag('registration_letter');
            $message = str_replace('{client_email}', $client_email, $message);
            $message = str_replace('{client_password}', $client_password, $message);

            Mail::send($client_email, $from_email, $from_name, $subject, $message);
            
            $this->client = $client;
        } else {
            if ($client_address_new) {
                // Добавление адреса
                $address = Model::factory('address')
                    ->setAddressTitle('Новый адрес')
                    ->setAddressText($client_address_text)
                    ->setAddressClient($this->client->getId())
                    ->save();
                $address->assignDefault();
            }
        }
        
        $purchase_sum = $this->cart->getSum() * $this->client->getClientDiscount();
        if ($purchase_sum < get_preference('free_delivery_sum')) {
            $purchase_sum += $delivery->getDeliveryPrice();
        }
        
        // Сохранение заказа
        $purchase = Model::factory('purchase')
            ->setPurchaseClient($this->client->get_id())
            ->setPurchasePhone($client_phone)
            ->setPurchaseAddress($address->getAddressText())
            ->setPurchaseRequest($purchase_request)
            ->setPurchaseComment($purchase_comment)
            ->setPurchaseDelivery($delivery->getId())
            ->setPurchaseDate(Date::now())
            ->setPurchaseSum($purchase_sum)
            ->setPurchaseStatus('new')
            ->save();
        
        // Сохранение позиций заказа
        foreach($this->cart->get() as $item) {
            $package = Adminko\Model\Model::factory('package')->get($item->id);

            Model::factory('purchase_item')
                ->setItemPurchase($purchase->getId())
                ->setItemProduct($package->getPackageProduct())
                ->setItemPackage($package->getId())
                ->setItemPrice($item->price)
                ->setItemQuantity($item->quantity)
                ->save();
        }
        
        // Отправка сообщения
        $from_email = get_preference('from_email');
        $from_name = get_preference('from_name');
        
        $client_email = $this->client->get_client_email();
        $client_subject = get_preference('client_subject');
        
        $manager_email = get_preference('manager_email');
        $manager_subject = get_preference('manager_subject');
        
        $purchase_view = new View();
        $purchase_view->assign('purchase', $purchase);
        $purchase_view->assign('client', $this->client);
        
        $client_message = $purchase_view->fetch('module/purchase/client_message');
        $manager_message = $purchase_view->fetch('module/purchase/manager_message');
        
        Mail::send($client_email, $from_email, $from_name, $client_subject, $client_message);
        Mail::send($manager_email, $from_email, $from_name, $manager_subject, $manager_message);
        
        Session::flash('purchase_complete', true);
        
        $this->cart->clear();
        
        System::redirectBack();
    }
    
    // Отключаем кеширование
    protected function getCacheKey()
    {
        return false;
    }
}