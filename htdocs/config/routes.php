<?php
/**
 * Пользовательские правила маршрутизации
 */
$routes = array(
    // Путь к каталогу
    '/product/@catalogue' => array(
        'controller' => 'product',
        'catalogue' => '\w+',
    ),
    // Путь к голосованию
    '/product/vote/@id' => array(
        'controller' => 'product',
        'action' => 'vote',
    ),
    // Путь к отзыву
    '/product/review/@id' => array(
        'controller' => 'product',
        'action' => 'review',
    ),
    // Путь к каталогу
    '/product/brand/@brand' => array(
        'controller' => 'product',
        'brand' => '\w+',
        'action' => 'brand',
    ),
    // Путь к товару
    '/product/@catalogue/@id' => array(
        'controller' => 'product',
        'catalogue' => '\w+',
        'action' => 'item',
    ),
);
