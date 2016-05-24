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

class PluginCategories_ActionAdmin extends PluginCategories_Inherits_ActionAdmin {

    /**
     * Registers events
     */
    protected function RegisterEvent() {

        parent::RegisterEvent();
        $this->AddEventPreg('/^content-categories$/i', '/^add$/', 'EventCategoriesAdd');
        $this->AddEventPreg('/^content-categories$/i', '/^edit$/', 'EventCategoriesEdit');
        $this->AddEventPreg('/^content-categories$/i', '/^delete$/', 'EventCategoriesDelete');

        $this->AddEventPreg('/^content-categories$/i', 'EventCategories');
    }

    protected function _getBlogs() {

        $aResult = E::Module('Blog')->GetBlogsByFilter(
            array('exclude_type' => 'personal'), array('blog_rating' => 'desc'), 1, PHP_INT_MAX
        );
        return $aResult['collection'];
    }

    /**
     * Установка собственного обработчика главной страницы
     *
     * @return mixed
     */
    protected function _eventConfigLinks() {

        if (($sHomePage = $this->GetPost('homepage')) && ($sHomePage == 'category_homepage')) {
            $aConfig = array(
                'router.config.action_default' => 'homepage',
                'router.config.homepage' => 'category/index',
                'router.config.homepage_select' => 'category_homepage',
            );
            Config::WriteCustomConfig($aConfig);
            Router::Location('admin/settings-site/links');
            exit;
        }
        return parent::_eventConfigLinks();
    }

    /**
     * Main event to manage categories
     */
    protected function EventCategories() {

        $this->sMainMenuItem = 'content';

        $this->_setTitle(E::Module('Lang')->Get('plugin.categories.menu_content_categories'));
        $this->SetTemplateAction('categories/list');

        // * Получаем список
        $aFilter = array();
        $aCategories = E::Module('Category')->GetItemsByFilter($aFilter, 'Category');
        E::Module('Viewer')->Assign('aCategories', $aCategories);

        $aLangList = E::Module('Lang')->GetLangList();
        E::Module('Viewer')->Assign('aLangList', $aLangList);

        if (F::GetRequest('add')) {
            E::Module('Message')->AddNoticeSingle(E::Module('Lang')->Get('plugin.categories.add_success'));
        }

        if (F::GetRequest('edit')) {
            E::Module('Message')->AddNoticeSingle(E::Module('Lang')->Get('plugin.categories.edit_success'));
        }

        if (F::GetRequest('delete')) {
            E::Module('Message')->AddNoticeSingle(E::Module('Lang')->Get('plugin.categories.delete_success'));
        }
    }

    /**
     * Adds the category
     *
     * @return bool
     */
    protected function EventCategoriesAdd() {

        $this->sMainMenuItem = 'content';

        $this->_setTitle(E::Module('Lang')->Get('plugin.categories.add_title'));
        $this->SetTemplateAction('categories/add');

        // * Вызов хуков
        E::Module('Hook')->Run('admin_categories_add_show');

        // * Загружаем переменные в шаблон
        E::Module('Viewer')->AddHtmlTitle(E::Module('Lang')->Get('plugin.categories.add_title'));

        $aBlogs = $this->_getBlogs();
        E::Module('Viewer')->Assign('aBlogs', $aBlogs);

        $aLangList = E::Module('Lang')->GetLangList();
        E::Module('Viewer')->Assign('aLangList', $aLangList);

        if (F::isPost('submit_category_add')) {
            // * Обрабатываем отправку формы
            return $this->SubmitCategoriesAdd();
        }
        if (!C::Get('plugin.categories.multicategory')) {
            foreach ($aBlogs as $oBlog) {
                if ($oBlog->getCategories()) {
                    $_REQUEST['blog'][$oBlog->getId()] = $oBlog->getId();
                }
            }
        }
        return null;
    }

    /**
     * @return bool
     */
    protected function SubmitCategoriesAdd() {

        // * Проверка корректности полей формы
        if (!$this->CheckCategoryFields()) {
            return false;
        }

        $oCategory = Engine::GetEntity('Category');
        $oCategory->setCategoryTitle(F::GetRequest('category_title'));
        $oCategory->setCategoryUrl(F::GetRequest('category_url'));

        // * Загрузка аватара категории
        if ($aUploadedFile = $this->GetUploadedFile('category_avatar')) {
            if ($sPath = E::Module('Category')->UploadCategoryAvatar($aUploadedFile)) {
                $oCategory->setAvatar($sPath);
            } else {
                E::Module('Message')->AddError(E::Module('Lang')->Get('blog_create_avatar_error'), E::Module('Lang')->Get('error'));
                return false;
            }
        }

        if ($oCategory->Add()) {
            $this->AddRelations($oCategory);
            Router::Location('admin/content-categories/?add=success');
        }
        return true;
    }

    /**
     * Edits the category
     *
     * @return bool
     */
    protected function EventCategoriesEdit() {

        $this->sMainMenuItem = 'content';

        // * Получаем категорию
        $iCategoryId = $this->GetParam(1);
        if (!$iCategoryId
            || !($oCategory = E::Module('Category')->GetByFilter(array('category_id' => $iCategoryId), 'Category'))
        ) {
            return parent::EventNotFound();
        }
        E::Module('Viewer')->Assign('oCategory', $oCategory);

        // * Устанавливаем шаблон вывода
        $this->_setTitle(E::Module('Lang')->Get('plugin.categories.edit_title'));
        $this->SetTemplateAction('categories/add');

        $aBlogs = $this->_getBlogs();
        E::Module('Viewer')->Assign('aBlogs', $aBlogs);

        // * Проверяем отправлена ли форма с данными
        if (F::isPost('submit_category_add')) {
            // * Обрабатываем отправку формы
            return $this->SubmitCategoriesEdit($oCategory);
        } else {
            $_REQUEST['category_id'] = $oCategory->getCategoryId();
            $_REQUEST['category_title'] = $oCategory->getCategoryTitle();
            $_REQUEST['category_url'] = $oCategory->getCategoryUrl();
            $_REQUEST['category_avatar'] = $oCategory->getCategoryAvatar();

            if (!C::Get('plugin.categories.multicategory')) {
                foreach ($aBlogs as $oBlog) {
                    if ($oBlog->getCategories()) {
                        $_REQUEST['blog'][$oBlog->getId()] = $oBlog->getId();
                    }
                }
            } else {
                $aRelations = E::Module('Category')->GetItemsByFilter(array('category_id' => $iCategoryId), 'Category_CategoryRel');
                foreach ($aRelations as $oRel) {
                    $_REQUEST['blog'][$oRel->getBlogId()] = $oRel->getBlogId();
                }
            }
        }
        return null;
    }

    protected function SubmitCategoriesEdit($oCategory) {

        // * Проверка корректности полей формы
        if (!$this->CheckCategoryFields()) {
            return false;
        }

        // * Обновляем данные
        $oCategory->setCategoryTitle(F::GetRequest('category_title'));
        $oCategory->setCategoryUrl(F::GetRequest('category_url'));

        // * Загрузка аватара категории
        if ($aUploadedFile = $this->GetUploadedFile('category_avatar')) {
            if ($sPath = E::Module('Category')->UploadCategoryAvatar($aUploadedFile)) {
                $oCategory->setAvatar($sPath);
            } else {
                E::Module('Message')->AddError(E::Module('Lang')->Get('blog_create_avatar_error'), E::Module('Lang')->Get('error'));
                return false;
            }
        }

        // Удалить аватар
        if (F::isPost('category_avatar_delete')) {
            E::Module('Category')->DeleteCategoryAvatar($oCategory);
            $oCategory->setAvatar(null);
        }

        if ($oCategory->Update()) {
            $this->AddRelations($oCategory);

            Router::Location('admin/content-categories/?edit=success');
        }
    }

    /**
     * Deletes the category
     *
     * @return mixed
     */
    protected function EventCategoriesDelete() {

        $this->sMainMenuItem = 'content';

        $this->SetTemplate(false);

        E::Module('Security')->ValidateSendForm();
        $iCategoryId = $this->GetParam(1);
        if (!$iCategoryId || !$oCategory = E::Module('Category')->GetByFilter(array('category_id' => $iCategoryId), 'Category')) {
            return parent::EventNotFound();
        }

        if ($oCategory->Delete()) {
            // * Удаляем связи
            if ($aRelations = E::Module('Category')->GetItemsByFilter(array('category_id' => $iCategoryId), 'Category_CategoryRel')) {
                foreach ($aRelations as $oRel) {
                    $oRel->Delete();
                }
            }
            Router::Location('admin/content-categories/?delete=success');
        }
    }

    /**
     * Relations between the category and blogs
     *
     * @param $oCategory
     */
    protected function AddRelations($oCategory) {

        // * Чистим связи и ставим новые
        $aRelations = E::Module('Category')->GetItemsByFilter(
            array('category_id' => $oCategory->getCategoryId()), 'ModuleCategory_EntityCategoryRel'
        );
        foreach ($aRelations as $oRel) {
            $oRel->Delete();
        }
        $aBlogsRel = F::GetRequest('blog');
        if (is_array($aBlogsRel)) {
            foreach ($aBlogsRel as $k => $v) {
                if (F::CheckVal($k, 'id')) {
                    $oRel = Engine::GetEntity('Category_CategoryRel');
                    $oRel->setCategoryId($oCategory->getCategoryId());
                    $oRel->setBlogId($k);
                    $oRel->Add();
                }
            }
        }
    }

    /**
     * @return bool
     */
    protected function CheckCategoryFields() {

        E::Module('Security')->ValidateSendForm();

        $bOk = true;

        if (!F::CheckVal(F::GetRequest('category_title', null, 'post'), 'text', 2, 200)) {
            E::Module('Message')->AddError(
                E::Module('Lang')->Get('plugin.categories.category_title_error'), E::Module('Lang')->Get('error')
            );
            $bOk = false;
        }
        if (!F::CheckVal(F::GetRequest('category_url', null, 'post'), 'login', 2, 50)) {
            E::Module('Message')->AddError(E::Module('Lang')->Get('plugin.categories.category_url_error'), E::Module('Lang')->Get('error'));
            $bOk = false;
        }

        return $bOk;
    }

}

// EOF