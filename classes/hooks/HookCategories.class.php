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
class PluginCategories_HookCategories extends Hook {

    public function RegisterHook() {

        // Хук для админки для добавления опции выбора главной
        $this->AddHook('template_admin_select_homepage', 'TplAdminSelectHomepage');

        // Пункт меню админки
        $this->AddHook('template_admin_menu_content', 'TplAdminMenuContent');

        //подключаем размеры mainpreview
        $this->AddHook('init_action', 'InitAction');
    }

    public function InitAction() {

        if (in_array('mainpreview', $this->Plugin_GetActivePlugins())) {
            $this->Category_InitConfigMainPreview();
        }
    }

    public function TplAdminSelectHomepage() {

        $sHomePageSelect = Config::Get('router.config.homepage_select');
        $this->Viewer_Assign('sHomePageSelect', $sHomePageSelect);
        return $this->Viewer_Fetch(Plugin::GetTemplateDir(__CLASS__) . 'tpls/hook.admin_select_homepage.tpl');
    }

    public function TplAdminMenuContent() {

        return $this->Viewer_Fetch(Plugin::GetTemplateDir(__CLASS__) . 'tpls/hook.admin_menu_content.tpl');
    }
}

// EOF