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
        $this->AddHook('template_admin_menu_content', 'TplAdminMenuContent', null, 10);

        if (!Config::Get('plugin.categories.multicategory')) {
            if (Config::Get('plugin.categories.select_category') || Config::Get('plugin.categories.change_category')) {
                // Создание блога
                $this->AddHook('template_form_add_blog_begin', 'TplFormAddBlogBegin');
            }
        }
    }


    /**
     * @return string
     */
    public function TplAdminSelectHomepage() {

        $sHomePageSelect = Config::Get('router.config.homepage_select');
        $this->Viewer_Assign('sHomePageSelect', $sHomePageSelect);

        return $this->Viewer_Fetch(Plugin::GetTemplateDir(__CLASS__) . 'tpls/hook.admin_select_homepage.tpl');
    }

    /**
     * @return string
     */
    public function TplAdminMenuContent() {

        return $this->Viewer_Fetch(Plugin::GetTemplateDir(__CLASS__) . 'tpls/hook.admin_menu_content.tpl');
    }

    /**
     * @return string
     */
    public function TplFormAddBlogBegin() {

        $aCategories = $this->Category_GetItemsByFilter(array(), 'Category');
        if (Router::GetActionEvent() === 'edit') {
            $iBlogId = (int)$_REQUEST['blog_id'];
            $iBlogCategoryId = 0;
            if ($iBlogId) {
                $aRelations = $this->Category_GetItemsByFilter(array('blog_id' => $iBlogId), 'Category_CategoryRel');
                if ($aRelations) {
                    $oRel = reset($aRelations);
                    $iBlogCategoryId = $oRel->getCategoryId();
                    $this->Viewer_Assign('iBlogCategoryId', $iBlogCategoryId);
                }
            }
            if (!Config::Get('plugin.categories.change_category')) {
                if ($iBlogCategoryId) {
                    foreach($aCategories as $iKey => $oCategory) {
                        if ($oCategory->getCategoryId() != $iBlogCategoryId) {
                            unset($aCategories[$iKey]);
                        }
                    }
                } else {
                    $aCategories = array();
                }
            }
        }
        $this->Viewer_Assign('aCategories', $aCategories);
        return $this->Viewer_Fetch(Plugin::GetTemplateDir(__CLASS__) . 'tpls/hook.form_add_blog_begin.tpl');
    }
}

// EOF