<?php
/*---------------------------------------------------------------------------
 * @Project: Alto CMS
 * @Project URI: http://altocms.com
 * @Description: Advanced Community Engine
 * @Copyright: Alto CMS Team
 * @License: GNU GPL v2 & MIT
 *----------------------------------------------------------------------------
 */

/**
 * @package plugin Categories
 * @since   0.9.5
 */

$config = array();

$config['multicategory'] = false;   // один блог может быть включен в несколько категорий

$config['select_category'] = true;  // возможность выбирать категорию при создании блога; действует, только если параметр multicategory = false
$config['change_category'] = 0;  // возможность изменить категорию при редактировании блога; действует, только если параметр multicategory = false

$config['category_page'] = 'topics'; // что выводится на странице категории, варианты: topics, blogs
//$config['category_page'] = 'blogs'; // что выводится на странице категории, варианты: topics, blogs

/*
 * Параметры для формирования главной плагина Категории
 */
// Топ-статьи
$config['topic_top_period'] = 30; // количество дней, если 0 или закомментировано, то за весь период
$config['topic_top_number'] = 4;  // Количество топ-статей
$config['topic_top_rating'] = 1;  // Минимальный рейтинг статьи для попадания в топ (если не нужно, то закомментируйте строку)

$config['topic_new_number'] = 6;  // Количество новых статей
$config['topic_new_rating'] = 0;  // Минимальный рейтинг статьи для попадания в новые (если не нужно, то закомментируйте строку)

/*
 * Предзаданные размеры превью-изображений
 */
$config['preview']['size'] = array(
    'category-home' => '208x208crop', // размер по умолчанию
);

// Прямой эфир
$config['widgets']['stream'] = array(
    'action'   => array(
        'index',
        'filter',
        'blogs',
        'blog' => array('{topics}', '{topic}', '{blog}'),
        'tag',
        'category',
    ),
);

// Теги
$config['widgets']['tags'] = array(
    'action'   => array(
        'index',
        'filter',
        'blog' => array('{topics}', '{topic}', '{blog}'),
        'tag',
        'category',
    ),
);

// Блоги
$config['widgets']['blogs'] = array(
    'action'   => array(
        'index',
        'filter',
        'blog' => array('{topics}', '{topic}', '{blog}')
    ),
);

// Категории
$config['widgets']['categories'] = array(
    'name'     => 'categories',
    'group'    => 'right',
    'priority' => 150,
    'plugin'   => 'categories',
    'action'   => array(
        'index',
        'filter',
        'blogs',
        'blog' => array('{topics}', '{topic}', '{blog}'),
        'tag',
        'category',
    ),
    'params' => array(
        'simple' => true, // Simple list of blogs in category
    ),
);

return $config;

// EOF