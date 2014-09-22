<?php
use Adminko\Db\Db;
use Adminko\Mail;

include_once dirname(dirname(__FILE__)) . '/config/config.php';

$delivery_message_list = Db::selectAll('select * from delivery_message');

if (!count($delivery_message_list)) exit;

$delivery_message_list = array_reindex($delivery_message_list, 'message_id');

$delivery_queue_list = Db::selectAll('
    select queue_id, queue_message, person_email
    from delivery_queue, delivery_person
    where delivery_queue.queue_person = delivery_person.person_id
    limit 100');

if (!count($delivery_queue_list)) exit;

foreach ($delivery_queue_list as $delivery_queue_item)
{
    if (isset($delivery_message_list[$delivery_queue_item['queue_message']]))
    {
        $delivery_message = $delivery_message_list[$delivery_queue_item['queue_message']];
        
        Mail::sendMessage($delivery_queue_item['person_email'], unserialize(gzuncompress(base64_decode($delivery_message['message_content']))));
        
        Db::delete('delivery_queue', array('queue_id' => $delivery_queue_item['queue_id']));
    }
}
