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

        $aCategories = $this->Category_GetItemsByFilter(array(), 'Category');
        $aCategoriesId = array();
        if ($aCategories) {
            foreach($aCategories as $oCategory) {
                $aCategoriesId[] = $oCategory->getId();
            }
            $aFilter = array(
                'top' => array(
                    'category_id' => $aCategoriesId,
                    'limit' => Config::Get('plugin.categories.topic_top_number'),
                ),
                'new' => array(
                    'category_id' => $aCategoriesId,
                    'limit' => Config::Get('plugin.categories.topic_new_number'),
                ),
            );
            if (!is_null($iRating = Config::Get('plugin.categories.topic_top_rating')) && is_numeric($iRating)) {
                $aFilter['top']['rating'] = $iRating;
            }
            if (!is_null($iRating = Config::Get('plugin.categories.topic_new_rating')) && is_numeric($iRating)) {
                $aFilter['new']['rating'] = $iRating;
            }

            $aCategoryHomeTopics = $this->Category_GetHomeTopics($aFilter);
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
        $this->Viewer_Assign('aCategories', $aCategories);
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
        $oCategory = $this->Category_GetByFilter(array('category_url' => $sCatUrl), 'Category');
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
        if ($iPage == 1 && Config::Get('router.config.homepage') == 'category') {
            $this->Viewer_SetHtmlCanonical(Config::Get('path.root.url') . '/');
        }

        $aFilter = array(
            'blog_type' => $this->Blog_GetOpenBlogTypes(),
            'topic_publish' => 1,
            'blog_id' => $aIds
        );

        // * Получаем список топиков
        $aResult = $this->Topic_GetTopicsByFilter($aFilter, $iPage, Config::Get('module.topic.per_page'));

        $aTopics = $aResult['collection'];

        // * Формируем постраничность
        $aPaging = $this->Viewer_MakePaging(
            $aResult['count'], $iPage, Config::Get('module.topic.per_page'), Config::Get('pagination.pages.count'),
            rtrim($oCategory->getUrl(), '/')
        );

        // * Загружаем переменные в шаблон
        $this->Viewer_Assign('aPaging', $aPaging);
        $this->Viewer_Assign('aTopics', $aTopics);
        $this->Viewer_Assign('oCategory', $oCategory);
    }

    public function EventShutdown() {
        $this->Viewer_Assign(
            'iCountTopicsNew', 
            $this->Topic_GetCountTopicsCollectiveNew() + 
            $this->Topic_GetCountTopicsPersonalNew()
        );
    }


}

// EOF
