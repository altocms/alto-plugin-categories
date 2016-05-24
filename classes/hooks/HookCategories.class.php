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

        if (!C::Get('plugin.categories.multicategory')) {
            if (C::Get('plugin.categories.select_category') || C::Get('plugin.categories.change_category')) {
                // Создание блога
                $this->AddHook('template_form_add_blog_begin', 'TplFormAddBlogBegin');
            }
        }
    }


    /**
     * @return string
     */
    public function TplAdminSelectHomepage() {

        $sHomePageSelect = C::Get('router.config.homepage_select');
        E::Module('Viewer')->Assign('sHomePageSelect', $sHomePageSelect);

        return E::Module('Viewer')->Fetch(Plugin::GetTemplateDir(__CLASS__) . 'tpls/hook.admin_select_homepage.tpl');
    }

    /**
     * @return string
     */
    public function TplAdminMenuContent() {

        return E::Module('Viewer')->Fetch(Plugin::GetTemplateDir(__CLASS__) . 'tpls/hook.admin_menu_content.tpl');
    }

    /**
     * @return string
     */
    public function TplFormAddBlogBegin() {

        $aCategories = E::Module('Category')->GetItemsByFilter(array(), 'Category');
        if (Router::GetActionEvent() === 'edit') {
            $iBlogId = (int)$_REQUEST['blog_id'];
            $iBlogCategoryId = 0;
            if ($iBlogId) {
                $aRelations = E::Module('Category')->GetItemsByFilter(array('blog_id' => $iBlogId), 'Category_CategoryRel');
                if ($aRelations) {
                    $oRel = reset($aRelations);
                    $iBlogCategoryId = $oRel->getCategoryId();
                    E::Module('Viewer')->Assign('iBlogCategoryId', $iBlogCategoryId);
                }
            }
            if (!C::Get('plugin.categories.change_category')) {
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
        E::Module('Viewer')->Assign('aCategories', $aCategories);
        return E::Module('Viewer')->Fetch(Plugin::GetTemplateDir(__CLASS__) . 'tpls/hook.form_add_blog_begin.tpl');
    }
}

// EOF