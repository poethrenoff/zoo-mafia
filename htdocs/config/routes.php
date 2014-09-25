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
        // Путь к товару
        '/product/@catalogue/@id' => array(
            'controller' => 'product',
            'catalogue' => '\w+',
            'action' => 'item',
        ),
);
