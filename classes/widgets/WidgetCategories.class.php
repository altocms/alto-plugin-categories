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

/**
 * Регистрация хука
 *
 */
class PluginCategories_WidgetCategories extends Widget {

    public function Exec() {

        // * Получаем категории
        $aCategories = E::Module('Category')->GetItemsByFilter(array(), 'Category');
        E::Module('Viewer')->Assign('aCategories', $aCategories);
    }

}

// EOF