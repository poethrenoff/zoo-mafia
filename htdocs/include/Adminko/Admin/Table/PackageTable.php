<?php
namespace Adminko\Admin\Table;

use Adminko\Metadata;
use Adminko\Admin\Admin;
use Adminko\Model\Model;

class PackageTable extends Table
{
    protected function actionAddSave($redirect = true)
    {
        $primary_field = parent::actionAddSave(false);
        
        $record = $this->getRecord($primary_field);
        $this->setDefaultPrice($record['package_product']);
        
        if ($redirect) {
            $this->redirect();
        }

        return $primary_field;
    }
    
    protected function actionEditSave($redirect = true)
    {
        $record = $this->getRecord();
        
        parent::actionEditSave(false);
        
        $this->setDefaultPrice($record['package_product']);

        if ($redirect) {
            $this->redirect();
        }
    }
    
    protected function actionDelete($redirect = true)
    {
        $record = $this->getRecord();
        
        parent::actionDelete(false);
        
        $this->setDefaultPrice($record['package_product']);

        if ($redirect) {
            $this->redirect();
        }
    }
        
    protected function actionMove($redirect = true)
    {
        $record = $this->getRecord();
        
        parent::actionMove(false);
        
        $this->setDefaultPrice($record['package_product']);

        if ($redirect) {
            $this->redirect();
        }
    }
    
    protected function setDefaultPrice($product_id)
    {
        Model::factory('product')->get($product_id)->setDefaultPrice()->save();
    }
}
