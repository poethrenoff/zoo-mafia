<?php
namespace Adminko\Module;

use Adminko\Cookie;
use Adminko\Mail;
use Adminko\Session;
use Adminko\System;
use Adminko\Paginator;
use Adminko\Model\Model;
use Adminko\Module\Module;
use Adminko\Valid\Valid;

class ClientModule extends Module
{
    const SESSION_VAR = '__client__';
    
    /**
     * Текущий пользователь
     */
    protected $client = null;

    public function actionIndex()
    {
        if (self::isAuth()) {
            System::redirectTo(array('controller' => 'client/purchase'));
        } else {
            $error = !empty($_POST) ? $this->authFromRequest() : array();

            $this->view->assign('error', $error);
            $this->content = $this->view->fetch('module/client/form');
        }
    }

    /**
     * Регистрация
     */
    public function actionRegistration()
    {
        if (Session::flash('registration_complete')) {
            $this->content = $this->view->fetch('module/client/registration/complete');
        } else {
            $error = !empty($_POST) ? $this->addClient() : array();

            $this->view->assign('error', $error);
            $this->content = $this->view->fetch('module/client/registration/form');
        }
    }

    /**
     * Восстановление пароля
     */
    public function actionRecovery()
    {
        if (Session::flash('recovery_complete')) {
            $this->content = $this->view->fetch('module/client/recovery/complete');
        } else {
            $error = !empty($_POST) ? $this->recoveryPassword() : array();

            $this->view->assign('error', $error);
            $this->content = $this->view->fetch('module/client/recovery/form');
        }
    }

    /**
     * Ваши настройки
     */
    public function actionProfile()
    {
        if (!self::isAuth()) {
            System::redirectTo(array('controller' => 'client'));
        } elseif (Session::flash('profile_complete')) {
            $this->content = $this->view->fetch('module/client/profile/complete');
        } else {
            $this->client = self::getInfo();
            $error = !empty($_POST) ? $this->saveClient() : array();

            $this->view->assign('error', $error);
            $this->view->assign('client', $this->client);
            $this->content = $this->view->fetch('module/client/profile/form');
        }
    }

    /**
     * Ваши заказы
     */
    public function actionPurchase()
    {
        if (!self::isAuth()) {
            System::redirectTo(array('controller' => 'client'));
        } else {
            $this->client = self::getInfo();

            $total = $this->client->getPurchaseCount();
            $count = max(1, intval($this->getParam('count', 5)));
            $pages = Paginator::create($total, array('by_page' => $count));
            $purchase_list = $this->client->getPurchaseList($pages['by_page'], $pages['offset']);
            
            $this->view->assign('client', $this->client);
            $this->view->assign('purchase_list', $purchase_list);
            $this->view->assign('pages', Paginator::fetch($pages));
            $this->content = $this->view->fetch('module/client/purchase/index');
        }
    }
    
    /**
     * Ваш скидка
     */
    public function actionDiscount()
    {
        if (!self::isAuth()) {
            System::redirectTo(array('controller' => 'client'));
        } else {
            $discount_list = Model::factory('discount')
                ->getList(array(), array('discount_value' => 'asc'));
            $this->view->assign('discount_list', $discount_list);            
            
            $this->client = self::getInfo();
            $this->view->assign('client', $this->client);
            $this->content = $this->view->fetch('module/client/discount/index');
        }
    }
    
    /**
     * Ваши товары
     */
    public function actionProduct()
    {
        if (!self::isAuth()) {
            System::redirectTo(array('controller' => 'client'));
        } else {
            $this->client = self::getInfo();
            $this->view->assign('client', $this->client);
            $this->content = $this->view->fetch('module/client/product/index');
        }
    }

    /**
     * Удаление товара
     */
    public function actionDeleteProduct()
    {
        if (!self::isAuth()) {
            System::redirectTo(array('controller' => 'client'));
        } else {
            $this->client = self::getInfo();   
            $product = $this->getProduct(System::id());
            
            $this->client->deleteClientProduct($product);
            
            System::redirectBack();
        }
    }
    
    /**
     * Ваши адреса
     */
    public function actionAddress()
    {
        if (!self::isAuth()) {
            System::redirectTo(array('controller' => 'client'));
        } else {
            $this->client = self::getInfo();
            $this->view->assign('client', $this->client);
            $this->content = $this->view->fetch('module/client/address/index');
        }
    }

    /**
     * Добавление адреса
     */
    public function actionAddAddress()
    {
        if (!self::isAuth()) {
            System::redirectTo(array('controller' => 'client'));
        } else {
            $this->client = self::getInfo();
            $error = !empty($_POST) ? $this->addAddress() : array();
            
            $this->view->assign('error', $error);
            $this->view->assign('client', $this->client);
            $this->content = $this->view->fetch('module/client/address/add');
        }
    }

    /**
     * Редактирование адреса
     */
    public function actionEditAddress()
    {
        if (!self::isAuth()) {
            System::redirectTo(array('controller' => 'client'));
        } else {
            $this->client = self::getInfo();
            $address = $this->getAddress();
            
            $error = !empty($_POST) ? $this->saveAddress($address) : array();
            
            $this->view->assign('error', $error);
            $this->view->assign('address', $address);
            $this->view->assign('client', $this->client);
            $this->content = $this->view->fetch('module/client/address/edit');
        }
    }

    /**
     * Удаление адреса
     */
    public function actionDeleteAddress()
    {
        if (!self::isAuth()) {
            System::redirectTo(array('controller' => 'client'));
        } else {
            $this->client = self::getInfo();   
            $address = $this->getAddress();
            
            $address->delete();
            $address->assignDefault();
            
            System::redirectBack();
        }
    }
    
    /**
     * Выбор адреса по умолчанию
     */
    public function actionDefaultAddress()
    {
        if (!self::isAuth()) {
            System::redirectTo(array('controller' => 'client'));
        } else {
            $this->client = self::getInfo();   
            $address = $this->getAddress();
            
            $address->makeDefault();
            
            System::redirectBack();
        }
    }
    
    /**
     * Выход с сайта
     */
    public function actionLogout()
    {
        if (self::isAuth()) {
            unset($_SESSION[self::SESSION_VAR]);
            self::clearClientCookie();
        }

        System::redirectBack();
    }

    /**
     * Панель пользователя
     */
    public function actionInfo()
    {
        $this->view->assign('client', self::getInfo());
        $this->content = $this->view->fetch('module/client/info');
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////
    
    /**
     * Добавление нового пользователя
     */
    public function addClient()
    {
        $error = array();

        $field_list = array(
            'client_title', 'client_email', 'client_password', 'client_password_confirm', 'client_phone');
        foreach ($field_list as $field_name) {
            if (is_empty($$field_name = trim(init_string($field_name)))) {
                $error[$field_name] = 'Поле обязательно для заполнения';
            }
        }

        if (!isset($error['client_email']) && !Valid::factory('email')->check($client_email)) {
            $error['client_email'] = 'Поле заполнено некорректно';
        }
        if (!isset($error['client_email']) && Model::factory('client')->getByEmail($client_email)) {
            $error['client_email'] = 'Пользователь с таким электронным адресом уже зарегистрирован';
        }
        if (!isset($error['client_password']) && !isset($error['client_password_confirm']) &&
            strcmp($client_password, $client_password_confirm)) {
            $error['client_password_confirm'] = 'Пароли не совпадают';
        }

        if (count($error)) {
            return $error;
        }

        // Добавление пользователя
        $client = Model::factory('client')
            ->setClientTitle($client_title)
            ->setClientEmail($client_email)
            ->setClientPassword(md5($client_password))
            ->setClientPhone($client_phone)
            ->save();

        $from_email = get_preference('from_email');
        $from_name = get_preference('from_name');
        $subject = get_preference('registration_subject');

        $message = TextModule::getByTag('registration_letter');
        $message = str_replace('{client_email}', $client_email, $message);
        $message = str_replace('{client_password}', $client_password, $message);

        Mail::send($client_email, $from_email, $from_name, $subject, $message);

        Session::flash('registration_complete', true);

        System::redirectBack();
    }

    /**
     * Изменение личных данных
     */
    public function saveClient()
    {
        $error = array();

        $field_list = array(
            'client_title', 'client_email', 'client_phone');
        foreach ($field_list as $field_name) {
            if (is_empty($$field_name = trim(init_string($field_name)))) {
                $error[$field_name] = 'Поле обязательно для заполнения';
            }
        }

        if (!isset($error['client_email']) && !Valid::factory('email')->check($client_email)) {
            $error['client_email'] = 'Поле заполнено некорректно';
        }
        if (!isset($error['client_email']) && ($client = Model::factory('client')->getByEmail($client_email)) && ($client->getId() != $this->client->getId())) {
            $error['client_email'] = 'Пользователь с таким электронным адресом уже зарегистрирован';
        }

        if (count($error)) {
            return $error;
        }

        $field_list = array(
            'client_password_old', 'client_password', 'client_password_confirm');
        $change_password = false;
        foreach ($field_list as $field_name) {
            $change_password |=!is_empty($$field_name = trim(init_string($field_name)));
        }

        if ($change_password) {
            foreach ($field_list as $field_name) {
                if (is_empty($$field_name)) {
                    $error[$field_name] = 'Поле обязательно для заполнения';
                }
            }

            if (!isset($error['client_password_old']) && strcmp(md5($client_password_old), $this->client->getClientPassword())) {
                $error['client_password_old'] = 'Неверное значение старого пароля';
            }
            if (!isset($error['client_password']) && !isset($error['client_password_confirm']) &&
                strcmp($client_password, $client_password_confirm)) {
                $error['client_password_confirm'] = 'Пароли не совпадают';
            }
        }

        if (count($error)) {
            return $error;
        }

        // Сохранение профиля
        $this->client
            ->setClientTitle($client_title)
            ->setClientEmail($client_email)
            ->setClientPhone($client_phone);
        if (!is_empty($client_password)) {
            $this->client->setClientPassword(md5($client_password));
        }
        $this->client->save();

        if (init_cookie('client')) {
            self::setClientCookie($this->client);
        }

        Session::flash('profile_complete', true);

        System::redirectBack();
    }

    /**
     * Отправка нового пароля
     */
    public function recoveryPassword()
    {
        $error = array();

        $field_list = array('client_email');
        foreach ($field_list as $field_name)
            if (is_empty($$field_name = trim(init_string($field_name))))
                $error[$field_name] = 'Поле обязательно для заполнения';

        if (!isset($error['client_email']) && !Valid::factory('email')->check($client_email)) {
            $error['client_email'] = 'Поле заполнено некорректно';
        }

        if (!isset($error['client_email']) && !($client = Model::factory('client')->getByEmail($client_email))) {
            $error['client_email'] = 'Пользователь с таким электронным адресом не зарегистрирован';
        }

        if (count($error)) {
            return $error;
        }

        $client_password = generate_key(8);
        $client->setClientPassword(md5($client_password))->save();

        $from_email = get_preference('from_email');
        $from_name = get_preference('from_name');
        $subject = get_preference('recovery_subject');

        $message = TextModule::getByTag('recovery_letter');
        $message = str_replace('{client_password}', $client_password, $message);

        Mail::send($client_email, $from_email, $from_name, $subject, $message);

        Session::flash('recovery_complete', true);

        System::redirectBack();
    }
      
    /**
     * Добавление нового адреса
     */
    public function addAddress()
    {
        $error = array();

        $address_text = trim(init_string('address_text'));
        $address_default = trim(init_string('address_default'));
        
        if (is_empty($address_text)) {
            $error['address_text'] = 'Поле обязательно для заполнения';
        }

        if (count($error)) {
            return $error;
        }

        // Добавление адреса
        $address = Model::factory('address')
            ->setAddressText($address_text)
            ->setAddressClient($this->client->getId())
            ->save();
        
        if ($address_default) {
            $address->makeDefault();
        } else {
            $address->assignDefault();
        }
        
        System::redirectTo(array('controller' => 'client/address'));
    }
  
    /**
     * Изменение нового адреса
     */
    public function saveAddress($address)
    {
        $error = array();

        $address_text = trim(init_string('address_text'));
        $address_default = trim(init_string('address_default'));
        
        if (is_empty($address_text)) {
            $error['address_text'] = 'Поле обязательно для заполнения';
        }

        if (count($error)) {
            return $error;
        }

        // Сохранение адреса
        $address
            ->setAddressText($address_text)
            ->save();
        
        if ($address_default) {
            $address->makeDefault();
        }
        
        System::redirectTo(array('controller' => 'client/address'));
    }
  
    /**
     * Получение адреса
     */
    public function getAddress()
    {
        try {
            $address = Model::factory('address')->get(System::id());
        } catch (\AlarmException $e) {
            System::notFound();
        }
        if ($address->getAddressClient() != $this->client->getId()) {
            System::notFound();
        }
        return $address;
    }
    
    /**
     * Получение товара
     */
    public function getProduct($id)
    {
        try {
            $product = Model::factory('product')->get($id);
        } catch (\AlarmException $e) {
            System::notFound();
        }
        if (!$product->getProductActive()) {
            System::notFound();
        }
        return $product;
    }
    
    /**
     * Аутентификация из формы
     */
    public static function authFromRequest()
    {
        $error = array();

        $field_list = array(
            'client_email', 'client_password');
        foreach ($field_list as $field_name) {
            if (is_empty($$field_name = trim(init_string($field_name)))) {
                $error[$field_name] = 'Поле обязательно для заполнения';
            }
        }

        if (!isset($error['client_email']) && !Valid::factory('email')->check($client_email)) {
            $error['client_email'] = 'Поле заполнено некорректно';
        }

        if (count($error)) {
            return $error;
        }

        try {
            $client = self::auth($client_email, md5($client_password));
        } catch (\Exception $e) {
            return array(
                'client_email' => $e->getMessage(),
            );
        }

        if (init_string('client_remember')) {
            self::setClientCookie($client);
        }

        $_SESSION[self::SESSION_VAR] = $client->getId();

        System::redirectBack();
    }

    /**
     * Аутентификация по кукам
     */
    public static function authFromCookie()
    {
        @list($client_email, $client_password) = Cookie::getData('client');

        try {
            $client = self::auth($client_email, $client_password);
        } catch (\Exception $e) {
            return false;
        }

        $_SESSION[self::SESSION_VAR] = $client->getId();

        return true;
    }

    /**
     * Авторизован ли пользователь
     */
    public static function isAuth()
    {
        if (isset($_SESSION[self::SESSION_VAR])) {
            return true;
        }
        return self::authFromCookie();
    }

    /**
     * Возвращает информацию о текущем пользователе
     */
    public static function getInfo()
    {
        if (self::isAuth()) {
            try {
                return Model::factory('client')->get($_SESSION[self::SESSION_VAR]);
            } catch (\AlarmException $e) {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Аутентификация пользователя по логину и паролю
     */
    public static function auth($client_email, $client_password)
    {
        $client = Model::factory('client')->getByEmail($client_email);

        if (!$client || strcmp($client->getClientPassword(), $client_password)) {
            throw new \Exception('Неверный email или пароль');
        }

        return $client;
    }

    /**
     * Установка пользовательских кук
     */
    public static function setClientCookie($client)
    {
        cookie::setData(
            'client', array(
            $client->getClientEmail(),
            $client->getClientPassword(),
            ), time() + 60 * 60 * 24 * 7, '/'
        );
    }

    /**
     * Очистка пользовательских кук
     */
    public static function clearClientCookie()
    {
        cookie::setData(
            'client', null, time() - 60 * 60 * 24, '/'
        );
    }

    /**
     * Отключаем кеширование
     */
    protected function getCacheKey()
    {
        return false;
    }
}
