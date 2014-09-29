<?php
namespace Adminko\Module;

use Adminko\Model\Model;

class SearchModule extends ProductModule
{
    protected function actionIndex()
    {
        $search_value = trim(init_string('search'));
        
        $this->setSortMode();
            
        $product_list = Model::factory('product')->getSearchResult($search_value,
            array($this->sort_field => $this->sort_order)
        );
        
        $this->view->assign('product_list', $product_list);
        $this->content = $this->view->fetch('module/search/result');
    }
    
    protected function actionForm()
    {
        $this->content = $this->view->fetch('module/search/form');
    }
}
