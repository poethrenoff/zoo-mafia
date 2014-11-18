<?php
namespace Adminko\Admin\Table;

class BrandTable extends Table
{
    protected function actionAddSave($redirect = true)
    {
        if (!init_string('brand_name')) {
            $_REQUEST['brand_name'] = to_file_name(init_string('brand_title'), true);
        }
        unset($this->fields['brand_name']['no_add']);

        $primary_field = parent::actionAddSave(false);

        if ($redirect) {
            $this->redirect();
        }

        return $primary_field;
    }
}
