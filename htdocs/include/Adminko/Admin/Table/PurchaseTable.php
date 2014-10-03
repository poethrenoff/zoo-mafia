<?php
namespace Adminko\Admin\Table;

use Adminko\Metadata;
use Adminko\Model\Model;

class PurchaseTable extends Table
{
    protected function actionEditSave($redirect = true)
    {
        $record = $this->getRecord();
        
        parent::actionEditSave(false);
        
        if ($record['purchase_status'] == 'complete' || init_string('purchase_status') == 'complete') {
            $this->updateClientSum($record['purchase_client']);
        }

        if ($redirect) {
            $this->redirect();
        }
    }
        
    protected function updateClientSum($client_id)
    {
        Metadata::$objects['client']['fields']['client_sum']['no_edit'] = false;
        
        Model::factory('client')->get($client_id)->updateClientSum()->save();
        
        Metadata::$objects['client']['fields']['client_sum']['no_edit'] = true;
    }
}
