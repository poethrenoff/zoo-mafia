<?php
namespace Adminko\Admin\Tool;

use Adminko\System;
use Adminko\Mail;
use Adminko\Db\Db;
use Adminko\Admin\Admin;

class DeliveryTool extends Admin
{
    protected function actionIndex()
    {
        $mail_count = Db::selectCell('select count(*) from delivery_queue');

        if (!$mail_count) {
            Db::delete('delivery_message');
        }

        $prev_mail = Db::selectRow('select * from delivery_storage');

        $this->view->assign('title', $this->object_desc['title']);
        $this->view->assign('mail_count', $mail_count);
        $this->view->assign('prev_mail', $prev_mail);

        $form_url = System::urlFor(array('object' => 'delivery', 'action' => 'send'));
        $this->view->assign('form_url', $form_url);
        $cancel_url = System::urlFor(array('object' => 'delivery', 'action' => 'clear'));
        $this->view->assign('cancel_url', $cancel_url);

        $this->content = $this->view->fetch('admin/delivery/delivery');

        $this->storeState();
    }

    protected function actionSend()
    {
        $email = init_string('email');
        $name = init_string('name');
        $subject = init_string('subject');
        $body = init_string('body');
        $type = init_string('type');

        if ($subject === '') {
            throw new \AlarmException('Ошибка. Не заполнено поле "Тема рассылки".');
        }
        if ($email === '') {
            throw new \AlarmException('Ошибка. Не заполнено поле "От кого".');
        }
        if ($body === '') {
            throw new \AlarmException('Ошибка. Не заполнено поле "Текст рассылки".');
        }
        if ($type === '') {
            throw new \AlarmException('Ошибка. Не заполнено поле "Тип рассылки".');
        }

        Db::delete('delivery_storage');
        Db::insert('delivery_storage', array('storage_subject' => $subject, 'storage_email' => $email,
            'storage_name' => $name, 'storage_body' => $body));

        switch ($type) {
            case 'send_to_all':
                $person_list = Db::selectAll('
                    select person_id from delivery_person');
                break;
            default:
                $person_list = Db::selectAll('
                    select person_id from delivery_person where person_admin = 1');
        }

        if (count($person_list)) {
            $message = Mail::prepareMessage($email, $name, $subject, $body);

            Db::insert('delivery_message', array('message_content' => @base64_encode(gzcompress(serialize($message)))));
            $message_id = Db::lastInsertId();

            foreach ($person_list as $person) {
                Db::insert('delivery_queue', array('queue_message' => $message_id, 'queue_person' => $person['person_id']));
            }
        }

        $this->redirect();
    }

    protected function actionClear()
    {
        Db::delete('delivery_queue');

        $this->redirect();
    }
}
