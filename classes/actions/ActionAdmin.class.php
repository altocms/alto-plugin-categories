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

    protected function RegisterEvent() {

        parent::RegisterEvent();
        $this->AddEventPreg('/^content-categories$/i', '/^add$/', 'EventCategoriesAdd');
        $this->AddEventPreg('/^content-categories$/i', '/^edit$/', 'EventCategoriesEdit');
        $this->AddEventPreg('/^content-categories$/i', '/^delete$/', 'EventCategoriesDelete');

        $this->AddEventPreg('/^content-categories$/i', 'EventCategories');
    }

    // Установка собственного обработчика главной страницы
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

    protected function EventCategories() {

        $this->_setTitle($this->Lang_Get('plugin.categories.menu_content_categories'));
        $this->SetTemplateAction('categories/list');

        // * Получаем список
        $aFilter = array();
        $aCategories = $this->Category_GetItemsByFilter($aFilter, 'Category');
        $this->Viewer_Assign('aCategories', $aCategories);

        $aLangList = $this->Lang_GetLangList();
        $this->Viewer_Assign('aLangList', $aLangList);

        if (F::GetRequest('add')) {
            $this->Message_AddNoticeSingle($this->Lang_Get('plugin.categories.add_success'));
        }

        if (F::GetRequest('edit')) {
            $this->Message_AddNoticeSingle($this->Lang_Get('plugin.categories.edit_success'));
        }

        if (F::GetRequest('delete')) {
            $this->Message_AddNoticeSingle($this->Lang_Get('plugin.categories.delete_success'));
        }
    }

    protected function EventCategoriesAdd() {

        $this->_setTitle($this->Lang_Get('plugin.categories.add_title'));
        $this->SetTemplateAction('categories/add');

        // * Вызов хуков
        $this->Hook_Run('admin_categories_add_show');

        // * Загружаем переменные в шаблон
        $this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.categories.add_title'));

        $aResult = $this->Blog_GetBlogsByFilter(
            array('type' => 'open'), array('blog_rating' => 'desc'), 1, PHP_INT_MAX
        );
        $aBlogs = $aResult['collection'];
        $this->Viewer_Assign('aBlogs', $aBlogs);

        $aLangList = $this->Lang_GetLangList();
        $this->Viewer_Assign('aLangList', $aLangList);

        if (F::isPost('submit_category_add')) {
            // * Обрабатываем отправку формы
            return $this->SubmitCategoriesAdd();
        }
    }

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
            if ($sPath = $this->Category_UploadCategoryAvatar($aUploadedFile)) {
                $oCategory->setAvatar($sPath);
            } else {
                $this->Message_AddError($this->Lang_Get('blog_create_avatar_error'), $this->Lang_Get('error'));
                return false;
            }
        }

        if ($oCategory->Add()) {
            $this->AddRelations($oCategory);
            Router::Location('admin/content-categories/?add=success');
        }
    }

    protected function EventCategoriesEdit() {

        // * Получаем категорию
        $iCategoryId = $this->GetParam(1);
        if (!$iCategoryId
            || !($oCategory = $this->Category_GetByFilter(array('category_id' => $iCategoryId), 'Category'))
        ) {
            return parent::EventNotFound();
        }
        $this->Viewer_Assign('oCategory', $oCategory);

        // * Устанавливаем шаблон вывода
        $this->_setTitle($this->Lang_Get('plugin.categories.edit_title'));
        $this->SetTemplateAction('categories/add');

        $aResult = $this->Blog_GetBlogsByFilter(
            array('type' => 'open'), array('blog_rating' => 'desc'), 1, PHP_INT_MAX
        );
        $aBlogs = $aResult['collection'];
        $this->Viewer_Assign('aBlogs', $aBlogs);

        // * Проверяем отправлена ли форма с данными
        if (F::isPost('submit_category_add')) {
            // * Обрабатываем отправку формы
            return $this->SubmitCategoriesEdit($oCategory);
        } else {
            $_REQUEST['category_id'] = $oCategory->getCategoryId();
            $_REQUEST['category_title'] = $oCategory->getCategoryTitle();
            $_REQUEST['category_url'] = $oCategory->getCategoryUrl();
            $_REQUEST['category_avatar'] = $oCategory->getCategoryAvatar();

            $aRelations = $this->Category_GetItemsByFilter(array('category_id' => $iCategoryId), 'Category_CategoryRel');
            foreach ($aRelations as $oRel) {
                $_REQUEST['blog'][$oRel->getBlogId()] = $oRel->getBlogId();
            }
        }
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
            if ($sPath = $this->Category_UploadCategoryAvatar($aUploadedFile)) {
                $oCategory->setAvatar($sPath);
            } else {
                $this->Message_AddError($this->Lang_Get('blog_create_avatar_error'), $this->Lang_Get('error'));
                return false;
            }
        }

        // Удалить аватар
        if (F::isPost('category_avatar_delete')) {
            $this->Category_DeleteCategoryAvatar($oCategory);
            $oCategory->setAvatar(null);
        }

        if ($oCategory->Update()) {
            $this->AddRelations($oCategory);

            Router::Location('admin/content-categories/?edit=success');
        }
    }

    protected function EventCategoriesDelete() {

        $this->SetTemplate(false);

        $this->Security_ValidateSendForm();
        $iCategoryId = $this->GetParam(1);
        if (!$iCategoryId || !$oCategory = $this->Category_GetByFilter(array('category_id' => $iCategoryId), 'Category')) {
            return parent::EventNotFound();
        }

        if ($oCategory->Delete()) {
            // * Удаляем связи
            if ($aRelations = $this->Category_GetItemsByFilter(array('category_id' => $iCategoryId), 'Category_CategoryRel')) {
                foreach ($aRelations as $oRel) {
                    $oRel->Delete();
                }
            }
            Router::Location('admin/content-categories/?delete=success');
        }
    }

    protected function AddRelations($oCategory) {

        // * Чистим связи и ставим новые
        $aRelations = $this->Category_GetItemsByFilter(
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

    protected function CheckCategoryFields() {

        $this->Security_ValidateSendForm();

        $bOk = true;

        if (!F::CheckVal(F::GetRequest('category_title', null, 'post'), 'text', 2, 200)) {
            $this->Message_AddError(
                $this->Lang_Get('plugin.categories.category_title_error'), $this->Lang_Get('error')
            );
            $bOk = false;
        }
        if (!F::CheckVal(F::GetRequest('category_url', null, 'post'), 'login', 2, 50)) {
            $this->Message_AddError($this->Lang_Get('plugin.categories.category_url_error'), $this->Lang_Get('error'));
            $bOk = false;
        }

        return $bOk;
    }

}

// EOF