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

class PluginCategories_ActionCategory extends ActionPlugin {

    public function Init() {

        $this->SetDefaultEvent('index');
    }

    /**
     * Регистрация евентов
     */
    protected function RegisterEvent() {

        $this->AddEvent('index', 'EventIndex');
        $this->AddEventPreg('/^[\w\-\_]+$/i', '/^(page([1-9]\d{0,5}))?$/i', array('EventCategoryList', 'list'));
    }

    /**
     * Action for homepage
     */
    protected function EventIndex() {

        $this->SetTemplateAction('index');

        $aCategories = E::Module('Category')->GetItemsByFilter(array(), 'Category');
        $aCategoriesId = array();
        if ($aCategories) {
            foreach($aCategories as $oCategory) {
                $aCategoriesId[] = $oCategory->getId();
            }
            $aFilter = array(
                'top' => array(
                    'category_id' => $aCategoriesId,
                    'limit' => C::Get('plugin.categories.topic_top_number'),
                ),
                'new' => array(
                    'category_id' => $aCategoriesId,
                    'limit' => C::Get('plugin.categories.topic_new_number'),
                ),
            );
            if (!is_null($iRating = C::Get('plugin.categories.topic_top_rating')) && is_numeric($iRating)) {
                $aFilter['top']['rating'] = $iRating;
            }
            if (!is_null($iRating = C::Get('plugin.categories.topic_new_rating')) && is_numeric($iRating)) {
                $aFilter['new']['rating'] = $iRating;
            }

            $aCategoryHomeTopics = E::Module('Category')->GetHomeTopics($aFilter);
            $aCategoryTopTopics = (isset($aCategoryHomeTopics['top']) ? $aCategoryHomeTopics['top'] : array());
            foreach($aCategories as $oCategory) {
                if (isset($aCategoryTopTopics[$oCategory->getId()])) {
                    $oCategory->setTopTopics($aCategoryTopTopics[$oCategory->getId()]);
                } else {
                    $oCategory->setTopTopics(array());
                }
            }

            $aCategoryNewTopics = (isset($aCategoryHomeTopics['new']) ? $aCategoryHomeTopics['new'] : array());
            foreach($aCategories as $oCategory) {
                if (isset($aCategoryNewTopics[$oCategory->getId()])) {
                    $oCategory->setNewTopics($aCategoryNewTopics[$oCategory->getId()]);
                } else {
                    $oCategory->setNewTopics(array());
                }
            }

        }
        Config::Set(
            'plugin.topicintro.preview.size.category-home',
            C::Get('plugin.categories.preview.size.category-home')
        );
        E::Module('Viewer')->Assign('aCategories', $aCategories);
    }

    /**
     * Action for categories list
     *
     * @return string
     */
    protected function EventCategoryList() {

        $this->SetTemplateAction('list');

        $sCatUrl = $this->sCurrentEvent;

        // * Проверяем есть ли категория с таким URL
        $oCategory = E::Module('Category')->GetByFilter(array('category_url' => $sCatUrl), 'Category');
        if (!$oCategory) {
            return parent::EventNotFound();
        }

        // * Есть ли подключенные к категории блоги
        if (!($aIds = $oCategory->getBlogIds())) {
            return parent::EventNotFound();
        }

        // * Передан ли номер страницы
        $iPage = $this->GetParamEventMatch(0, 2) ? $this->GetParamEventMatch(0, 2) : 1;

        // * Устанавливаем основной URL для поисковиков
        if ($iPage == 1 && C::Get('router.config.homepage') == 'category') {
            E::Module('Viewer')->SetHtmlCanonical(C::Get('path.root.url') . '/');
        }

        $aFilter = array(
            'blog_type' => E::Module('Blog')->GetOpenBlogTypes(),
            'topic_publish' => 1,
            'blog_id' => $aIds
        );

        // * Получаем список топиков
        $aResult = E::Module('Topic')->GetTopicsByFilter($aFilter, $iPage, C::Get('module.topic.per_page'));

        $aTopics = $aResult['collection'];

        // * Формируем постраничность
        $aPaging = E::Module('Viewer')->MakePaging(
            $aResult['count'], $iPage, C::Get('module.topic.per_page'), C::Get('pagination.pages.count'),
            rtrim($oCategory->getLink(), '/')
        );

        // * Загружаем переменные в шаблон
        E::Module('Viewer')->Assign('aPaging', $aPaging);
        E::Module('Viewer')->Assign('aTopics', $aTopics);
        E::Module('Viewer')->Assign('oCategory', $oCategory);
    }

    public function EventShutdown() {
        E::Module('Viewer')->Assign(
            'iCountTopicsNew', 
            E::Module('Topic')->GetCountTopicsCollectiveNew() + 
            E::Module('Topic')->GetCountTopicsPersonalNew()
        );
    }


}

// EOF
