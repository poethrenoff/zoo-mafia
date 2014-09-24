<?php
namespace Adminko\Module;

use Adminko\Model\Model;

class BannerModule extends Module
{
    protected function actionIndex()
    {
        $item = Model::factory('banner')->getBanner();

        $this->view->assign($item);
        $this->content = $this->view->fetch('module/banner/item');
    }

    // Отключаем кеширование
    protected function getCacheKey()
    {
        return false;
    }
}
