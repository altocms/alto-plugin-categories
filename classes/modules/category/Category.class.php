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

class PluginCategories_ModuleCategory extends ModuleORM {

    /** @var  PluginCategories_ModuleCategory_MapperCategory */
    protected $oMapper;

    public function Init() {

        $this->oMapper = Engine::GetMapper(__CLASS__);
    }

    public function InitConfigMainPreview() {

        Config::Set(
            'plugin.mainpreview.size_images_preview', array_merge(
                C::Get('plugin.mainpreview.size_images_preview'),
                C::Get('plugin.categories.size_images_preview')
            )
        );

        Config::Set('plugin.mainpreview.preview_minimal_size_width', C::Get('plugin.categories.preview_size_w'));
        Config::Set('plugin.mainpreview.preview_minimal_size_height', C::Get('plugin.categories.preview_size_h'));
    }

    /**
     * @param array $aFile
     *
     * @return bool
     */
    public function UploadCategoryAvatar($aFile) {

        $sFileTmp = E::Module('Uploader')->UploadLocal($aFile);
        if ($sFileTmp && ($oImg = E::Module('Img')->CropSquare($sFileTmp))) {
            $sFile = E::Module('Uploader')->Uniqname(E::Module('Uploader')->GetUserImageDir(), strtolower(pathinfo($sFileTmp, PATHINFO_EXTENSION)));
            if ($oImg->Save($sFile)) {
                return E::Module('Uploader')->Dir2Url($sFile);
            }
            F::File_Delete($sFile);
        }

        // * В случае ошибки, возвращаем false
        return false;
    }

    /**
     * @param object $oCategory
     *
     * @return bool
     */
    public function DeleteCategoryAvatar($oCategory) {

        // * Если аватар есть, удаляем его и его рейсайзы
        if ($sUrl = $oCategory->getAvatar()) {
            return E::Module('Img')->Delete(E::Module('Uploader')->Url2Dir($sUrl));
        }
        return true;
    }

    /**
     * @param array $aBlogsId
     *
     * @return array
     */
    public function GetCategoriesByBlogId($aBlogsId) {

        $aBlogsId = $this->_entitiesId($aBlogsId);
        if ($aBlogsId) {
            $aResult = $this->oMapper->GetCategoriesByBlogId($aBlogsId);
        } else {
            $aResult = array();
        }
        return $aResult;
    }

    /**
     * @param array $aUrls
     *
     * @return array
     */
    public function GetCategoriesByUrl($aUrls) {

        $sCacheKey = 'categories_by_url_' . serialize($aUrls);
        if (false === ($aCategories = E::Module('Cache')->Get($sCacheKey))) {
            $aCategories = $this->GetCategoryItemsByCategoryUrlIn($aUrls);
            $aOrders = array_flip($aUrls);
            foreach($aCategories as $oCategory) {
                $oCategory->setProp('_order', $aOrders[$oCategory->getUrl()]);
            }
            $aCategories = F::Array_SortEntities($aCategories, '_order');
            E::Module('Cache')->Set($aCategories, $sCacheKey, 'category_update', 'P30D');
        }
        return $aCategories;
    }

    /**
     * @param $oEntity1
     * @param $oEntity2
     *
     * @return int
     */
    public function _sortByTmpOrders($oEntity1, $oEntity2) {

        if ($oEntity1->getProp('_order') == $oEntity2->getProp('_order')) {
            return 0;
        }
        return ($oEntity1->getProp('_order') < $oEntity2->getProp('_order')) ? -1 : 1;
    }

    /**
     * @param array $aFilter
     * @param bool  $bIdOnly
     *
     * @return array
     */
    public function GetTopTopics($aFilter = array(), $bIdOnly = false) {

        if (!isset($aFilter['blog_type'])) {
            $aFilter['blog_type'] = E::Module('Blog')->GetOpenBlogTypes();
        }
        if (!isset($aFilter['period'])) {
            $aFilter['period'] = intval(C::Get('plugin.categories.topic_top_period'));
        }
        $aCategoryTopicsId = $this->oMapper->GetCategoryTopicsId('top', $aFilter);
        if ($bIdOnly) {
            return $aCategoryTopicsId;
        }
        $aResult = $this->_getTopicsData(array('top' => $aCategoryTopicsId));
        return isset($aResult['top']) ? $aResult['top'] : array();
    }

    /**
     * @param array $aFilter
     * @param bool  $bIdOnly
     *
     * @return array
     */
    public function GetNewTopics($aFilter = array(), $bIdOnly = false) {

        if (!isset($aFilter['blog_type'])) {
            $aFilter['blog_type'] = E::Module('Blog')->GetOpenBlogTypes();
        }
        $aCategoryTopicsId = $this->oMapper->GetCategoryTopicsId('new', $aFilter);
        if ($bIdOnly) {
            return $aCategoryTopicsId;
        }
        $aResult = $this->_getTopicsData(array('new' => $aCategoryTopicsId));
        return isset($aResult['new']) ? $aResult['new'] : array();
    }

    /**
     * @param array $aFilters
     *
     * @return array
     */
    public function GetHomeTopics($aFilters = array()) {

        if (!isset($aFilters['top'])) {
            $aFilters['top'] = array();
        }
        if (!isset($aFilters['new'])) {
            $aFilters['new'] = array();
        }
        $aCategoryTopicsId = array(
            'top' => $this->GetTopTopics($aFilters['top'], true),
            'new' => $this->GetNewTopics($aFilters['new'], true),
        );
        $aResult = $this->_getTopicsData($aCategoryTopicsId);
        return $aResult;
    }

    /**
     * @param array $aCategoryTopicsId
     *
     * @return array
     */
    protected function _getTopicsData($aCategoryTopicsId) {

        $aResult = array();
        $aTopicsId = array();
        foreach ($aCategoryTopicsId as $aCatTypeTopicsId) {
            foreach ($aCatTypeTopicsId as $aCatTopicsId) {
                $aTopicsId = array_merge($aTopicsId, $aCatTopicsId);
            }
        }
        if ($aTopicsId) {
            $aTopics = E::Module('Topic')->GetTopicsAdditionalData($aTopicsId, array('user' => array(), 'blog' => array()));
            foreach ($aCategoryTopicsId as $sType => $aCatTypeTopicsId) {
                foreach ($aCatTypeTopicsId as $iCategoryId => $aCatTopicsId) {
                    foreach ($aCatTopicsId as $iTopicId) {
                        if (isset($aTopics[$iTopicId])) {
                            $aResult[$sType][$iCategoryId][$iTopicId] = $aTopics[$iTopicId];
                        }
                    }
                }
            }
        }
        return $aResult;
    }
}

// EOF