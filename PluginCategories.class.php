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
                'ModuleMenu',
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

        $this->ExportSQL(__DIR__ . '/install/db/dump.sql');
        return true;
    }

    /**
     * Инициализация плагина
     */
    public function Init() {

        E::Module('Viewer')->AppendStyle(Plugin::GetTemplateDir(__CLASS__) . 'assets/css/style.css');
    }

}

// EOF