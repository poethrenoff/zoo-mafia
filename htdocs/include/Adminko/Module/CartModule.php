<?php
namespace Adminko\Module;

use Adminko\Cart;
use Adminko\System;
use Adminko\Model\Model;

class CartModule extends Module
{
    protected function actionIndex()
    {
        $this->view->assign(Cart::factory());
        $this->content = $this->view->fetch('module/cart/cart');
    }

    protected function actionInfo()
    {
        $this->view->assign(Cart::factory());
        $this->content = $this->view->fetch('module/cart/info');
    }

    protected function actionAdd()
    {
        $package = $this->getPackage(System::id());
        $quantity = max(1, intval(init_string('quantity')));
        Cart::factory()->add(
            $package->getId(), $package->getPackagePrice(), $quantity
        );

        $this->actionInfo();
    }

    protected function actionSave()
    {
        if (!empty($_POST)) {
            if (isset($_POST['quantity']) && is_array($_POST['quantity'])) {
                Cart::factory()->clear();

                foreach ($_POST['quantity'] as $id => $quantity) {
                    $package = $this->getPackage($id);
                    $quantity = max(1, intval($quantity));
                    Cart::factory()->add(
                        $package->getId(), $package->getPackagePrice(), $quantity
                    );
                }
            }
        }
        $this->actionInfo();
    }

    protected function actionDelete()
    {
        Cart::factory()->delete(System::id());
        System::redirectBack();
    }

    protected function actionClear()
    {
        Cart::factory()->clear();
        System::redirectBack();
    }

    // Получаем фасовку
    protected function getPackage($id)
    {
        try {
            $package = Model::factory('package')->get($id);
            $product = Model::factory('product')->get($package->getPackageProduct());
        } catch (\AlarmException $e) {
            System::notFound();
        }
        if (!$product->getProductActive()) {
            System::notFound();
        }
        return $package;
    }
    
    // Отключаем кеширование
    protected function getCacheKey()
    {
        return false;
    }
}
