<?php
namespace Adminko\Module;

use Adminko\System;
use Adminko\Paginator;
use Adminko\Model\Model;

class NewsModule extends Module
{
    // Вывод полного списка новостей
    protected function actionIndex()
    {
        $model_news = Model::factory('news');
        
        $total = $model_news->getCount();
        $count = max(1, intval($this->getParam('count')));
        
        $pages = Paginator::create($total, array('by_page' => $count));
        
        $item_list = $model_news->getList(array(), array('news_date' => 'desc'), $pages['by_page'], $pages['offset']);
        
        $this->view->assign('item_list', $item_list);
        $this->view->assign('pages', Paginator::fetch($pages));
        
        $this->content = $this->view->fetch('module/news/list');
    }

    // Вывод конкретной новости
    protected function actionItem()
    {
        try {
            $item = Model::factory('news')->get(System::id());
        } catch (\AlarmException $e) {
            System::notFound();
        }
        
        $this->view->assign($item);
        $this->output['meta_title'] = $item->getNewsTitle();
        $this->content = $this->view->fetch('module/news/item');
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////
    
    // Дополнительные параметры хэша модуля
    protected function extCacheKey()
    {
        return parent::extCacheKey() +
            ($this->action == 'item' ? array('_id' => System::id()) : array());
    }
}