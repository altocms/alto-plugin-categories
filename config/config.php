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

$config['multicategory'] = true;   // один блог может быть включен в несколько категорий

/*
 * Параметры для формирования главной плагина Категории
 */
// Топ-статьи
$config['topic_top_period'] = 30; // количество дней, если 0 или закомментировано, то за весь период
$config['topic_top_number'] = 4;  // Количество топ-статей
$config['topic_top_rating'] = 1;  // Минимальный рейтинг статьи для попадания в топ (если не нужно, то закомментируйте строку)

$config['topic_new_number'] = 6;  // Количество новых статей
$config['topic_new_rating'] = 0;  // Минимальный рейтинг статьи для попадания в новые (если не нужно, то закомментируйте строку)

$config['preview_size_w']       = 229;  // Ширина
$config['preview_size_h']       = 116;  // Высота, при crop=false используется как минимально возможная высота
$config['preview_crop']         = true; // Делать из картинки кроп? false - если не нужно обрезать картинки по высоте
$config['preview_big_size_w']   = 354;  // Ширина большого варианта
$config['preview_big_size_h']   = 186;  // Высота большого варианта, при crop=false используется как минимально возможная высота
$config['preview_big_crop']     = true; // Делать из картинки кроп для большого варианта? false - если не нужно обрезать картинки по высоте

$config['size_images_preview'] = array(
    array(
        'w'    => $config['preview_size_w'],
        'h'    => $config['preview_crop'] ? $config['preview_size_h'] : null,
        'crop' => $config['preview_crop'],
    ),
    array(
        'w'    => $config['preview_big_size_w'],
        'h'    => $config['preview_big_crop'] ? $config['preview_big_size_h'] : null,
        'crop' => $config['preview_big_crop'],
    )
);

// Прямой эфир
$config['widgets'][] = array(
    'name'     => 'stream', // исполняемый виджет Stream
    'group'    => 'right', // группа, куда нужно добавить виджет
    'priority' => 100, // приоритет
    'action'   => array(
        'index',
        'filter',
        'blogs',
        'blog' => array('{topics}', '{topic}', '{blog}'),
        'tag',
        'category',
    ),
    'params' => array(
        'items' => array(
            'comments' => array('text' => 'widget_stream_comments', 'type'=>'comment'),
            'topics' => array('text' => 'widget_stream_topics', 'type'=>'topic'),
        ),
    ),
);

// Теги
$config['widgets'][] = array(
    'name'     => 'tags',
    'group'    => 'right',
    'priority' => 50,
    'action'   => array(
        'index',
        'filter',
        'blog' => array('{topics}', '{topic}', '{blog}'),
        'tag',
        'category',
    ),
);

// Блоги
$config['widgets'][] = array(
    'name'     => 'blogs',
    'group'    => 'right',
    'priority' => 1,
    'action'   => array(
        'index',
        'filter',
        'blog' => array('{topics}', '{topic}', '{blog}')
    ),
);

// Категории
$config['widgets'][] = array(
    'name'     => 'categories',
    'group'    => 'right',
    'priority' => 150,
    'plugin'   => 'categories',
    'action'   => array(
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