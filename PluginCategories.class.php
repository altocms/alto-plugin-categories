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
 * Запрещаем напрямую через браузер обращение к этому файлу.
 */
if (!class_exists('Plugin')) {
    die('Hacking attempt!');
}

class PluginCategories extends Plugin {

    protected $aInherits
        = array(
            'action' => array(
                'ActionAdmin',
                'ActionBlog',
            ),
            'module' => array(
                'ModuleCategory',
                'ModuleBlog',
            ),
            'mapper' => array(
                'ModuleCategory_MapperCategory',
            ),
            'entity' => array(
                'ModuleBlog_EntityBlog',
                'ModuleCategory_EntityCategory',
                'ModuleCategory_EntityCategoryRel',
            ),
        );


    /**
     * Активация плагина
     */
    public function Activate() {

        $this->ExportSQL(__DIR__ . '/dump.sql');
        return true;
    }

    /**
     * Инициализация плагина
     */
    public function Init() {

        $this->Viewer_AppendStyle(Plugin::GetTemplateDir(__CLASS__) . 'assets/css/style.css');
    }

}

// EOF