<?php
namespace Adminko\Module;

use Adminko\Mail;
use Adminko\View;
use Adminko\System;
use Adminko\Session;
use Adminko\Model\Model;

class ConsultModule extends Module
{
    protected function actionIndex()
    {
        if (Session::flash('consult_complete')) {
            $this->content = $this->view->fetch('module/consult/complete');
        } else {
            $product = $this->getProduct();
            $error = !empty($_POST) ? $this->sendRequest($product) : array();

            $this->view->assign('error', $error);
            $this->view->assign('product', $product);
            $this->view->assign('client', ClientModule::getInfo());
            $this->content = $this->view->fetch('module/consult/form');
        }
    }

    protected function sendRequest($product)
    {
        $error = array();

        $consult_person = init_string('consult_person');
        $consult_phone = init_string('consult_phone');
        $consult_question = init_string('consult_question');

        if (is_empty($consult_person)) {
            $error['consult_person'] = 'Не заполнено обязательное поле';
        }
        if (is_empty($consult_phone)) {
            $error['consult_phone'] = 'Не заполнено обязательное поле';
        }         
        if (is_empty($consult_question)) {
            $error['consult_question'] = 'Не заполнено обязательное поле';
        }         

        if (count($error)) {
            return $error;
        }

        // Отправка сообщения
        $from_email = get_preference('from_email');
        $from_name = get_preference('from_name');

        $consult_email = get_preference('consult_email');
        $consult_subject = get_preference('consult_subject');

        $consult_view = new View();
        $consult_message = $consult_view->fetch('module/consult/message');

        Mail::send($consult_email, $from_email, $from_name, $consult_subject, $consult_message);

        Session::flash('consult_complete', true);

        System::redirectTo(array('controller' => 'consult', 'id' => $product->getId()));
    }
    
    protected function getProduct()
    {
        try {
            $product = Model::factory('product')->get(System::id());
        } catch (\AlarmException $e) {
            System::notFound();
        }
        if (!$product->getProductActive()) {
            System::notFound();
        }
        return $product;
    }

    // Отключаем кеширование
    protected function getCacheKey()
    {
        return false;
    }
}
