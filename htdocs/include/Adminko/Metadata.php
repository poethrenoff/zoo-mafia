<?php
namespace Adminko;

class Metadata
{
    public static $objects = array(
        /**
         * Таблица "Тексты"
         */
        'text' => array(
            'title' => 'Тексты',
            'fields' => array(
                'text_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'text_tag' => array('title' => 'Метка', 'type' => 'string', 'show' => 1, 'sort' => 'asc', 'errors' => array('require', 'alpha'), 'group' => array()),
                'text_title' => array('title' => 'Заголовок', 'type' => 'string', 'show' => 1, 'main' => 1, 'errors' => array('require')),
                'text_content' => array('title' => 'Текст', 'type' => 'text', 'editor' => 1, 'errors' => array('require')),
            ),
        ),
        
        /**
         * Таблица "Новости"
         */
        'news' => array(
            'title' => 'Новости',
            'fields' => array(
                'news_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'news_title' => array('title' => 'Название', 'type' => 'string', 'show' => 1, 'main' => 1, 'errors' => array('require')),
                'news_announce' => array('title' => 'Анонс', 'type' => 'text', 'editor' => 1, 'errors' => array('require')),
                'news_content' => array('title' => 'Текст', 'type' => 'text', 'editor' => 1, 'errors' => array('require')),
                'news_date' => array('title' => 'Дата публикации', 'type' => 'datetime', 'show' => 1, 'sort' => 'desc', 'errors' => array('require')),
            ),
        ),
        
        /**
         * Таблица "Меню"
         */
        'menu' => array(
            'title' => 'Меню',
            'fields' => array(
                'menu_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'menu_parent' => array('title' => 'Родительский элемент', 'type' => 'parent'),
                'menu_title' => array('title' => 'Заголовок', 'type' => 'string', 'show' => 1, 'main' => 1, 'errors' => array('require')),
                'menu_page' => array('title' => 'Раздел', 'type' => 'table', 'table' => 'page', 'show' => 1),
                'menu_url' => array('title' => 'URL', 'type' => 'string', 'show' => 1),
                'menu_order' => array('title' => 'Порядок', 'type' => 'order', 'group' => array('menu_parent')),
                'menu_active' => array('title' => 'Видимость', 'type' => 'active'),
            ),
        ),
        
        /**
         * Таблица "Тизеры"
         */
        'teaser' => array(
            'title' => 'Тизеры',
            'fields' => array(
                'teaser_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'teaser_title' => array('title' => 'Заголовок', 'type' => 'string', 'show' => 1, 'main' => 1, 'errors' => array('require')),
                'teaser_image' => array('title' => 'Изображение', 'type' => 'image', 'upload_dir' => 'teaser', 'errors' => array('require')),
                'teaser_url' => array('title' => 'URL', 'type' => 'string', 'errors' => array('require')),
                'teaser_order' => array('title' => 'Порядок', 'type' => 'order'),
                'teaser_active' => array('title' => 'Видимость', 'type' => 'active'),
            ),
        ),
        
        // Каталог //
        
        /**
         * Таблица "Бренды"
         */
        'brand' => array(
            'title' => 'Бренды',
            'fields' => array(
                'brand_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'brand_title' => array('title' => 'Название', 'type' => 'string', 'main' => 1, 'errors' => array('require')),
                'brand_long' => array('title' => 'Длинное название', 'type' => 'string'),
                'brand_name' => array( 'title' => 'Ссылка', 'type' => 'string', 'no_add' => 1, 'group' => array(), 'errors' => array()),
                'brand_image' => array('title' => 'Логитип', 'type' => 'image', 'upload_dir' => 'brand', 'errors' => array('require')),
                'brand_order' => array('title' => 'Порядок', 'type' => 'order'),
            ),
            'links' => array(
                'product' => array('table' => 'product', 'field' => 'product_brand'),
            ),
        ),
        
        /**
         * Таблица "Каталог"
         */
        'catalogue' => array(
            'title' => 'Каталог',
            'fields' => array(
                'catalogue_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'catalogue_parent' => array('title' => 'Родительский раздел', 'type' => 'parent'),
                'catalogue_title' => array('title' => 'Название', 'type' => 'string', 'show' => 1, 'main' => 1, 'errors' => array('require')),
                'catalogue_name' => array( 'title' => 'Ссылка', 'type' => 'string', 'no_add' => 1, 'group' => array(), 'errors' => array('require')),
                'catalogue_image' => array( 'title' => 'Изображение', 'type' => 'image', 'upload_dir' => 'catalogue', 'errors' => array('require')),
                'catalogue_description' => array('title' => 'Описание', 'type' => 'text', 'editor' => 1),
                'catalogue_order' => array('title' => 'Порядок', 'type' => 'order', 'group' => array('catalogue_parent')),
                'catalogue_active' => array('title' => 'Видимость', 'type' => 'active'),
            ),
            'links' => array(
                'product' => array('table' => 'product', 'field' => 'product_catalogue'),
                'property' => array('table' => 'property', 'field' => 'property_catalogue'),
            ),
        ),
        
        /**
         * Таблица "Товары"
         */
        'product' => array(
            'title' => 'Товары',
            'fields' => array(
                'product_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'product_catalogue' => array('title' => 'Каталог', 'type' => 'table', 'table' => 'catalogue', 'errors' => array('require')),
                'product_brand' => array('title' => 'Бренд', 'type' => 'table', 'table' => 'brand', 'errors' => array('require')),
                'product_title' => array('title' => 'Название', 'type' => 'string', 'main' => 1, 'errors' => array('require')),
                'product_description' => array('title' => 'Описание', 'type' => 'text', 'editor' => 1, 'errors' => array('require')),
                'product_price' => array('title' => 'Цена', 'type' => 'float', 'no_add' => true, 'no_edit' => true),
                'product_rating' => array('title' => 'Рейтинг', 'type' => 'float', 'no_add' => true),
                'product_voters' => array('title' => 'Количество голосов', 'type' => 'int', 'no_add' => true),
                'product_order' => array('title' => 'Порядок', 'type' => 'order', 'group' => array('product_catalogue')),
                'product_active' => array('title' => 'Видимость', 'type' => 'active'),
            ),
            'links' => array(
                'package' => array('table' => 'package', 'field' => 'package_product'),
                'picture' => array('table' => 'picture', 'field' => 'picture_product'),
                'review' => array('table' => 'review', 'field' => 'review_product'),
            ),
            'relations' => array(
                'marker' => array('secondary_table' => 'marker', 'relation_table' => 'product_marker',
                    'primary_field' => 'product_id', 'secondary_field' => 'marker_id', 'title' => 'Маркеры'),
            ),
        ),
        
        /**
         * Таблица "Фасовки"
         */
        'package' => array(
            'title' => 'Фасовки',
            'fields' => array(
                'package_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'package_product' => array('title' => 'Товар', 'type' => 'table', 'table' => 'product', 'errors' => array('require')),
                'package_title' => array('title' => 'Фасовка', 'type' => 'string', 'main' => 1, 'errors' => array('require')),
                'package_price' => array('title' => 'Цена', 'type' => 'float', 'sort' => 'asc', 'show' => 1, 'errors' => array('require')),
            )
        ),
        
        /**
         * Таблица "Изображения"
         */
        'picture' => array(
            'title' => 'Изображения',
            'fields' => array(
                'picture_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'picture_product' => array('title' => 'Товар', 'type' => 'table', 'table' => 'product', 'errors' => array('require')),
                'picture_image' => array('title' => 'Изображение', 'type' => 'image', 'upload_dir' => 'product', 'main' => 1, 'errors' => array('require')),
                'picture_order' => array('title' => 'Порядок', 'type' => 'order', 'group' => array('picture_product')),
            )
        ),
        
        /**
         * Таблица "Маркеры"
         */
        'marker' => array(
            'title' => 'Маркеры',
            'fields' => array(
                'marker_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'marker_title' => array('title' => 'Название', 'type' => 'string', 'main' => 1, 'sort' => 'asc', 'errors' => array('require')),
                'marker_name' => array('title' => 'Системное имя', 'type' => 'string', 'show' => 1, 'errors' => array('require'), 'group' => array()),
                'marker_image' => array('title' => 'Картинка', 'type' => 'image', 'upload_dir' => 'marker'),
            ),
            'relations' => array(
                'product' => array('secondary_table' => 'product', 'relation_table' => 'product_marker',
                    'primary_field' => 'marker_id', 'secondary_field' => 'product_id', 'title' => 'Товары'),
            ),
        ),
        
        /**
         * Таблица "Связь маркеров с товарами"
         */
        'product_marker' => array(
            'title' => 'Связь маркеров с товарами',
            'internal' => true,
            'fields' => array(
                'product_id' => array('title' => 'Товар', 'type' => 'table', 'table' => 'product'),
                'marker_id' => array('title' => 'Маркер', 'type' => 'table', 'table' => 'marker'),
            ),
        ),
        
        /**
         * Таблица "Свойства"
         */
        'property' => array(
            'title' => 'Свойства',
            'fields' => array(
                'property_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'property_catalogue' => array('title' => 'Каталог', 'type' => 'table', 'table' => 'catalogue', 'errors' => array('require')),
                'property_title' => array('title' => 'Название', 'type' => 'string', 'show' => 1, 'main' => 1, 'errors' => array('require')),
                'property_kind' => array('title' => 'Тип свойства', 'type' => 'select', 'show' => 1, 'filter' => 1, 'values' => array(
                        array('value' => 'number', 'title' => 'Число'),
                        array('value' => 'boolean', 'title' => 'Флаг'),
                        array('value' => 'select', 'title' => 'Список')), 'errors' => array('require')),
                'property_unit' => array('title' => 'Единица измерения', 'type' => 'string'),
                'property_filter' => array('title' => 'Присутствует в фильтре', 'type' => 'boolean'),
                'property_order' => array('title' => 'Порядок', 'type' => 'order', 'group' => array('property_catalogue')),
                'property_active' => array('title' => 'Видимость', 'type' => 'active')
            ),
            'links' => array(
                'property_value' => array('table' => 'property_value', 'field' => 'value_property', 'show' => array('property_kind' => array('select')), 'ondelete' => 'cascade'),
            ),
        ),
        
        /**
         * Таблица "Значения свойств"
         */
        'property_value' => array(
            'title' => 'Значения свойств',
            'fields' => array(
                'value_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'value_property' => array('title' => 'Свойство', 'type' => 'table', 'table' => 'property', 'errors' => array('require')),
                'value_title' => array('title' => 'Название', 'type' => 'string', 'show' => 1, 'main' => 1, 'errors' => array('require')),
            ),
        ),
        
        /**
         * Таблица "Свойства товара"
         */
        'product_property' => array(
            'title' => 'Свойства товара',
            'internal' => 1,
            'fields' => array(
                'product_id' => array('title' => 'Товар', 'type' => 'table', 'table' => 'product', 'errors' => array('require')),
                'property_id' => array('title' => 'Свойство', 'type' => 'table', 'table' => 'property', 'errors' => array('require')),
                'value' => array('title' => 'Значение', 'type' => 'string', 'errors' => array('require')),
            ),
        ),
                
        /**
         * Таблица "Пользователи"
         */
        'client' => array(
            'title' => 'Пользователи',
            'no_add' => true, 'no_delete' => true,            
            'fields' => array(
                'client_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'client_title' => array('title' => 'Контактное лицо', 'type' => 'string', 'main' => 1, 'errors' => 'require'),
                'client_email' => array('title' => 'Email', 'type' => 'string', 'show' => 1,  'errors' => 'require|email'),
                'client_password' => array('title' => 'Пароль', 'type' => 'password'),
                'client_phone' => array('title' => 'Телефон', 'type' => 'string', 'errors' => 'require'),
                'client_sum' => array('title' => 'Сумма заказов', 'type' => 'float', 'no_add' => true, 'no_edit' => true),
            ),
            'links' => array(
                'address' => array('table' => 'address', 'field' => 'address_client'),
                'review' => array('table' => 'review', 'field' => 'review_client'),
            ),
        ),
                
        /**
         * Таблица "Адреса"
         */
        'address' => array(
            'title' => 'Адреса',
            'fields' => array(
                'address_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'address_client' => array('title' => 'Пользователь', 'type' => 'table', 'table' => 'client', 'errors' => array('require')),
                'address_text' => array('title' => 'Адрес', 'type' => 'text', 'main' => 1, 'errors' => array('require')),
                'address_default' => array('title' => 'По умолчанию', 'type' => 'default', 'group' => array('address_client')),
            ),
        ),
        
        /**
         * Таблица "Товары пользователя"
         */
        'client_product' => array(
            'title' => 'Товары пользователя',
            'internal' => true,
            'fields' => array(
                'client_id' => array('title' => 'Пользователь', 'type' => 'table', 'table' => 'client'),
                'product_id' => array('title' => 'Товар', 'type' => 'table', 'table' => 'product'),
            ),
        ),
                
        /**
         * Таблица "Способы доставки"
         */
        'delivery' => array(
            'title' => 'Способы доставки',
            'fields' => array(
                'delivery_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'delivery_title' => array( 'title' => 'Название', 'type' => 'string', 'main' => 1, 'errors' => array('require')),
                'delivery_price' => array('title' => 'Цена', 'type' => 'float', 'show' => 1, 'errors' => array('require')),
                'delivery_active' => array('title' => 'Активный', 'type' => 'active'),
            )
        ),
        
        /**
         * Таблица "Скидки"
         */
        'discount' => array(
            'title' => 'Скидки',
            'fields' => array(
                'discount_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'discount_value' => array( 'title' => 'Величина скидки (%)', 'type' => 'float', 'main' => 1, 'errors' => array('require')),
                'discount_sum' => array('title' => ' Сумма заказов', 'type' => 'float', 'show' => 1, 'errors' => array('require')),
            )
        ),
        
        /**
         * Таблица "Заказы"
         */
        'purchase' => array(
            'title' => 'Заказы',
            'no_add' => true, 'no_delete' => true,
            'fields' => array(
                'purchase_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'purchase_client' => array('title' => 'Пользователь', 'type' => 'table', 'table' => 'client', 'main' => 1, 'errors' => 'require'),
                'purchase_phone' => array('title' => 'Телефон', 'type' => 'string', 'errors' => 'require'),
                'purchase_address' => array('title' => 'Адрес', 'type' => 'text'),
                'purchase_request' => array('title' => 'Дата и время доставки', 'type' => 'string'),
                'purchase_comment' => array('title' => 'Комментарий', 'type' => 'text'),
                'purchase_delivery' => array('title' => 'Способ доставки', 'type' => 'table', 'table' => 'delivery', 'errors' => 'require'),
                'purchase_date' => array('title' => 'Дата заказа', 'type' => 'datetime', 'show' => 1, 'sort' => 'desc', 'errors' => 'require'),
                'purchase_sum' => array('title' => 'Сумма заказа', 'type' => 'float', 'show' => 1, 'errors' => 'require'),
                'purchase_status' => array('title' => 'Статус заказа', 'type' => 'select', 'filter' => 1, 'values' => array(
                        array('value' => 'new', 'title' => 'Новый'),
                        array('value' => 'confirm', 'title' => 'Подтвержден'),
                        array('value' => 'deliver', 'title' => 'Доставляется'),
                        array('value' => 'complete', 'title' => 'Выполнен'),
                        array('value' => 'cancel', 'title' => 'Отменен')), 'show' => 1, 'errors' => 'require'),
            ),
            'links' => array(
                'purchase_item' => array('table' => 'purchase_item', 'field' => 'item_purchase'),
            )
        ),
        
        /**
         * Таблица "Позиции заказа"
         */
        'purchase_item' => array(
            'title' => 'Позиции заказа',
            'no_add' => true,
            'fields' => array(
                'item_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'item_purchase' => array('title' => 'Заказ', 'type' => 'table', 'table' => 'purchase', 'errors' => 'require'),
                'item_product' => array('title' => 'Товар', 'type' => 'table', 'table' => 'product', 'main' => 1, 'errors' => array('require')),
                'item_package' => array('title' => 'Фасовка', 'type' => 'table', 'table' => 'package', 'show' => 1, 'errors' => array('require')),
                'item_price' => array('title' => 'Цена', 'type' => 'float', 'show' => 1, 'errors' => array('require')),
                'item_quantity' => array('title' => 'Количество', 'type' => 'int', 'show' => 1, 'errors' => array('require'))
            )
        ),
        
        /**
         * Таблица "Отзывы"
         */
        'review' => array(
            'title' => 'Отзывы',
            'no_add' => true,
            'fields' => array(
                'review_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'review_client' => array('title' => 'Пользователь', 'type' => 'table', 'table' => 'client', 'errors' => 'require'),
                'review_product' => array('title' => 'Товар', 'type' => 'table', 'table' => 'product', 'errors' => 'require'),
                'review_text' => array('title' => 'Отзыв', 'type' => 'text', 'main' => 1, 'errors' => array('require')),
                'review_date' => array('title' => 'Дата отзыва', 'type' => 'datetime', 'show' => 1, 'sort' => 'desc', 'errors' => 'require'),
                'review_status' => array('title' => 'Статус отзыва', 'type' => 'select', 'filter' => 1, 'values' => array(
                        array('value' => 'new', 'title' => 'Новый'),
                        array('value' => 'public', 'title' => 'Опубликован'),
                        array('value' => 'reject', 'title' => 'Отклонен')), 'show' => 1, 'errors' => 'require'),
            )
        ),      
        
        ////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Таблица "Настройки"
         */
        'preference' => array(
            'title' => 'Настройки',
            'class' => 'Builder',
            'fields' => array(
                'preference_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'preference_title' => array('title' => 'Название', 'type' => 'string', 'show' => 1, 'main' => 1, 'errors' => array('require')),
                'preference_name' => array('title' => 'Имя', 'type' => 'string', 'show' => 1, 'filter' => 1, 'errors' => array('require', 'alpha'), 'group' => array()),
                'preference_value' => array('title' => 'Значение', 'type' => 'string', 'show' => 1),
            ),
        ),
        
        /**
         * Таблица "Разделы"
         */
        'page' => array(
            'title' => 'Разделы',
            'fields' => array(
                'page_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'page_parent' => array('title' => 'Родительский раздел', 'type' => 'parent'),
                'page_layout' => array('title' => 'Шаблон', 'type' => 'table', 'table' => 'layout', 'errors' => array('require')),
                'page_title' => array('title' => 'Название', 'type' => 'string', 'main' => 1, 'errors' => array('require')),
                'page_name' => array('title' => 'Каталог', 'type' => 'string', 'show' => 1, 'errors' => array('alpha'), 'group' => array('page_parent')),
                'page_folder' => array('title' => 'Папка', 'type' => 'boolean'),
                'meta_title' => array('title' => 'Заголовок', 'type' => 'text'),
                'meta_keywords' => array('title' => 'Ключевые слова', 'type' => 'text'),
                'meta_description' => array('title' => 'Описание', 'type' => 'text'),
                'page_order' => array('title' => 'Порядок', 'type' => 'order', 'group' => array('page_parent')),
                'page_active' => array('title' => 'Видимость', 'type' => 'active'),
            ),
            'links' => array(
                'block' => array('table' => 'block', 'field' => 'block_page', 'ondelete' => 'cascade'),
            ),
        ),
        
        /**
         * Таблица "Блоки"
         */
        'block' => array(
            'title' => 'Блоки',
            'fields' => array(
                'block_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'block_page' => array('title' => 'Раздел', 'type' => 'table', 'table' => 'page', 'errors' => array('require')),
                'block_module' => array('title' => 'Модуль', 'type' => 'table', 'table' => 'module', 'errors' => array('require')),
                'block_title' => array('title' => 'Название', 'type' => 'string', 'main' => 1, 'errors' => array('require')),
                'block_area' => array('title' => 'Область шаблона', 'type' => 'table', 'table' => 'layout_area', 'errors' => array('require')),
            ),
            'links' => array(
                'block_param' => array('table' => 'block_param', 'field' => 'block', 'ondelete' => 'cascade'),
            ),
        ),
        
        /**
         * Таблица "Шаблоны"
         */
        'layout' => array(
            'title' => 'Шаблоны',
            'fields' => array(
                'layout_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'layout_title' => array('title' => 'Название', 'type' => 'string', 'main' => 1, 'errors' => array('require')),
                'layout_name' => array('title' => 'Системное имя', 'type' => 'string', 'show' => 1, 'errors' => array('require', 'alpha')),
            ),
            'links' => array(
                'page' => array('table' => 'page', 'field' => 'page_layout', 'hidden' => 1),
                'area' => array('table' => 'layout_area', 'field' => 'area_layout', 'title' => 'Области'),
            ),
        ),
        
        /**
         * Таблица "Области шаблона"
         */
        'layout_area' => array(
            'title' => 'Области шаблона',
            'class' => 'Builder',
            'fields' => array(
                'area_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'area_layout' => array('title' => 'Шаблон', 'type' => 'table', 'table' => 'layout', 'errors' => array('require')),
                'area_title' => array('title' => 'Название', 'type' => 'string', 'main' => 1, 'errors' => array('require')),
                'area_name' => array('title' => 'Системное имя', 'type' => 'string', 'show' => 1, 'errors' => array('require', 'alpha')),
                'area_main' => array('title' => 'Главная область', 'type' => 'default', 'show' => 1, 'group' => array('area_layout')),
                'area_order' => array('title' => 'Порядок', 'type' => 'order', 'group' => array('area_layout')),
            ),
            'links' => array(
                'bloсk' => array('table' => 'block', 'field' => 'block_area'),
            ),
        ),
        
        /**
         * Таблица "Модули"
         */
        'module' => array(
            'title' => 'Модули',
            'fields' => array(
                'module_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'module_title' => array('title' => 'Название', 'type' => 'string', 'main' => 1, 'errors' => array('require')),
                'module_name' => array('title' => 'Системное имя', 'type' => 'string', 'show' => 1, 'group' => array(), 'errors' => array('require', 'alpha')),
            ),
            'links' => array(
                'block' => array('table' => 'block', 'field' => 'block_module'),
                'module_param' => array('table' => 'module_param', 'field' => 'param_module', 'title' => 'Параметры', 'ondelete' => 'cascade'),
            ),
        ),
        
        /**
         * Таблица "Параметры модулей"
         */
        'module_param' => array(
            'title' => 'Параметры модулей',
            'fields' => array(
                'param_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'param_module' => array('title' => 'Модуль', 'type' => 'table', 'table' => 'module', 'errors' => array('require')),
                'param_title' => array('title' => 'Название', 'type' => 'string', 'main' => 1, 'errors' => array('require')),
                'param_type' => array('title' => 'Тип параметра', 'type' => 'select', 'filter' => 1, 'values' => array(
                        array('value' => 'string', 'title' => 'Строка'),
                        array('value' => 'int', 'title' => 'Число'),
                        array('value' => 'text', 'title' => 'Текст'),
                        array('value' => 'select', 'title' => 'Список'),
                        array('value' => 'table', 'title' => 'Таблица'),
                        array('value' => 'boolean', 'title' => 'Флаг')), 'show' => 1, 'errors' => array('require')),
                'param_name' => array('title' => 'Системное имя', 'type' => 'string', 'show' => 1, 'group' => array('param_module'), 'errors' => array('require', 'alpha')),
                'param_table' => array('title' => 'Имя таблицы', 'type' => 'select', 'values' => '__OBJECT__', 'show' => 1),
                'param_default' => array('title' => 'Значение по умолчанию', 'type' => 'string'),
                'param_require' => array('title' => 'Обязательное', 'type' => 'boolean'),
                'param_order' => array('title' => 'Порядок', 'type' => 'order', 'group' => array('param_module')),
            ),
            'links' => array(
                'param_value' => array('table' => 'param_value', 'field' => 'value_param', 'show' => array('param_type' => array('select')), 'title' => 'Значения', 'ondelete' => 'cascade'),
                'block_param' => array('table' => 'block_param', 'field' => 'param', 'ondelete' => 'cascade'),
            ),
        ),
        
        /**
         * Таблица "Значения параметров модулей"
         */
        'param_value' => array(
            'title' => 'Значения параметров модулей',
            'fields' => array(
                'value_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'value_param' => array('title' => 'Параметр', 'type' => 'table', 'table' => 'module_param', 'errors' => array('require')),
                'value_title' => array('title' => 'Название', 'type' => 'string', 'main' => 1, 'errors' => array('require')),
                'value_content' => array('title' => 'Значение', 'type' => 'string', 'show' => 1, 'group' => array('value_param'), 'errors' => array('require')),
                'value_default' => array('title' => 'По умолчанию', 'type' => 'default', 'show' => 1, 'group' => array('value_param')),
            ),
        ),
        
        /**
         * Таблица "Параметры блоков"
         */
        'block_param' => array(
            'title' => 'Параметры блоков',
            'internal' => true,
            'fields' => array(
                'block' => array('title' => 'Блок', 'type' => 'table', 'table' => 'block'),
                'param' => array('title' => 'Параметр', 'type' => 'table', 'table' => 'module_param'),
                'value' => array('title' => 'Значение', 'type' => 'text'),
            ),
        ),
        
        /**
         * Таблицы управления правами доступа
         */
        'admin' => array(
            'title' => 'Администраторы',
            'fields' => array(
                'admin_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'admin_title' => array('title' => 'Имя', 'type' => 'string', 'show' => 1, 'main' => 1, 'errors' => array('require')),
                'admin_login' => array('title' => 'Логин', 'type' => 'string', 'show' => 1, 'errors' => array('require', 'alpha'), 'group' => array()),
                'admin_password' => array('title' => 'Пароль', 'type' => 'password'),
                'admin_email' => array('title' => 'Email', 'type' => 'string', 'errors' => array('email')),
                'admin_active' => array('title' => 'Активный', 'type' => 'active'),
            ),
            'relations' => array(
                'admin_role' => array('secondary_table' => 'role', 'relation_table' => 'admin_role',
                    'primary_field' => 'admin_id', 'secondary_field' => 'role_id'),
            ),
        ),
        
        'admin_role' => array(
            'title' => 'Роли администраторов',
            'internal' => true,
            'fields' => array(
                'admin_id' => array('title' => 'Администратор', 'type' => 'table', 'table' => 'admin', 'errors' => array('require')),
                'role_id' => array('title' => 'Роль', 'type' => 'table', 'table' => 'role', 'errors' => array('require')),
            ),
        ),
        
        'role' => array(
            'title' => 'Роли',
            'fields' => array(
                'role_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'role_title' => array('title' => 'Название', 'type' => 'string', 'show' => 1, 'main' => 1, 'errors' => array('require')),
                'role_default' => array('title' => 'Главный администратор', 'type' => 'default', 'show' => 1),
            ),
            'relations' => array(
                'role_object' => array('secondary_table' => 'object', 'relation_table' => 'role_object',
                    'primary_field' => 'role_id', 'secondary_field' => 'object_id'),
            ),
        ),
        
        'role_object' => array(
            'title' => 'Права на системные разделы',
            'internal' => true,
            'fields' => array(
                'role_id' => array('title' => 'Роль', 'type' => 'table', 'table' => 'role', 'errors' => array('require')),
                'object_id' => array('title' => 'Системный раздел', 'type' => 'table', 'table' => 'object', 'errors' => array('require')),
            ),
        ),
        
        'object' => array(
            'title' => 'Системные разделы',
            'fields' => array(
                'object_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'object_parent' => array('title' => 'Родительский раздел', 'type' => 'parent'),
                'object_title' => array('title' => 'Название', 'type' => 'string', 'show' => 1, 'main' => 1, 'errors' => array('require')),
                'object_name' => array('title' => 'Объект', 'type' => 'select', 'values' => '__OBJECT__'),
                'object_order' => array('title' => 'Порядок', 'type' => 'order', 'group' => array('object_parent')),
                'object_active' => array('title' => 'Видимость', 'type' => 'active'),
            )
        ),
        
        /**
         * Утилита "Файл-менеджер"
         */
        'fm' => array(
            'title' => 'Файл-менеджер',
            'class' => 'Fm',
        ),
    );

    
}

//\Adminko\Db\Db::create();
