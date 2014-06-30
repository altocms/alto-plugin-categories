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
                Config::Get('plugin.mainpreview.size_images_preview'),
                Config::Get('plugin.categories.size_images_preview')
            )
        );

        Config::Set('plugin.mainpreview.preview_minimal_size_width', Config::Get('plugin.categories.preview_size_w'));
        Config::Set('plugin.mainpreview.preview_minimal_size_height', Config::Get('plugin.categories.preview_size_h'));
    }

    /**
     * @param $aFile
     *
     * @return bool
     */
    public function UploadCategoryAvatar($aFile) {

        $sFileTmp = $this->Uploader_UploadLocal($aFile);
        if ($sFileTmp && ($oImg = $this->Img_CropSquare($sFileTmp))) {
            $sFile = $this->Uploader_Uniqname($this->Uploader_GetUserImageDir(), strtolower(pathinfo($sFileTmp, PATHINFO_EXTENSION)));
            if ($oImg->Save($sFile)) {
                return $this->Uploader_Dir2Url($sFile);
            }
            F::File_Delete($sFile);
        }

        // * В случае ошибки, возвращаем false
        return false;
    }

    /**
     * @param $oCategory
     *
     * @return bool
     */
    public function DeleteCategoryAvatar($oCategory) {

        // * Если аватар есть, удаляем его и его рейсайзы
        if ($sUrl = $oCategory->getAvatar()) {
            return $this->Img_Delete($this->Uploader_Url2Dir($sUrl));
        }
    }

    /**
     * @param $aBlogId
     *
     * @return mixed
     */
    public function GetCategoriesByBlogId($aBlogId) {

        $aBlogId = $this->_entitiesId($aBlogId);
        return $this->oMapper->GetCategoriesByBlogId($aBlogId);
    }

    /**
     * @param array $aFilter
     * @param bool  $bIdOnly
     *
     * @return array
     */
    public function GetTopTopics($aFilter = array(), $bIdOnly = false) {

        if (!isset($aFilter['blog_type'])) {
            $aFilter['blog_type'] = $this->Blog_GetOpenBlogTypes();
        }
        if (!isset($aFilter['period'])) {
            $aFilter['period'] = intval(Config::Get('plugin.categories.topic_top_period'));
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
            $aFilter['blog_type'] = $this->Blog_GetOpenBlogTypes();
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
            $aTopics = $this->Topic_GetTopicsAdditionalData($aTopicsId, array('user' => array(), 'blog' => array()));
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