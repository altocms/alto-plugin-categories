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

class PluginCategories_ModuleCategory_EntityCategory extends EntityORM {

    protected $sPrimaryKey = 'category_id';
    protected $aBlogRels = null;

    protected $aRelations = array();

    /**
     * @return string
     */
    public function getLink() {

        return Router::GetPath('category') . $this->getCategoryUrl() . '/';
    }

    /**
     * @param null|string $sLang
     *
     * @return string
     */
    public function getTitle($sLang = null) {

        return $this->getLangTextProp('category_title', $sLang);
    }

    /**
     *
     */
    protected function _loadRelations() {

        $aRelations = $this->Category_GetItemsByFilter(
            array('category_id' => $this->getCategoryId()), 'Category_CategoryRel'
        );
        $this->aBlogRels = array();
        foreach ($aRelations as $oRel) {
            $this->aBlogRels[] = $oRel->getBlogId();
        }
    }

    /**
     * @return array
     */
    public function getBlogIds() {

        if (is_null($this->aBlogRels)) {
            $this->_loadRelations();
        }
        return $this->aBlogRels;
    }

    /**
     * @return array
     */
    public function getBlogs() {

        $aResult = $this->getProp('_blogs');
        if (is_null($aResult)) {
            $aBlogIds = $this->getBlogIds();
            if ($aBlogIds) {
                $aResult = $this->Blog_GetBlogsAdditionalData($aBlogIds);
            } else {
                $aResult = array();
            }
        }
        return $aResult;
    }

    /**
     * @param string $sType
     * @param int    $iPage
     * @param int    $iPerPage
     *
     * @return array
     */
    public function getTopics($sType = 'new', $iPage = 1, $iPerPage = 3) {

        $aBlogIds = $this->getBlogIds();

        if (is_array($aBlogIds) && count($aBlogIds)) {

            $aFilter = array(
                'blog_type'     => $this->Blog_GetOpenBlogTypes(),
                'topic_publish' => 1,
                'blog_id'       => $aBlogIds
            );

            if ($sType == 'top') {
                $aFilter['topic_rating'] = array(
                    'value'         => Config::Get('module.blog.index_good'),
                    'type'          => 'top',
                    'publish_index' => 1,
                );
            }
            $aReturn = $this->Topic_GetTopicsByFilter($aFilter, $iPage, $iPerPage, array('user', 'blog'));
            return $aReturn['collection'];

        }

        return array();
    }

    /**
     * @param $sAvatar
     */
    public function setAvatar($sAvatar) {

        $this->setProp('category_avatar', $sAvatar);
    }

    /**
     * @return string
     */
    public function getAavatar() {

        return $this->getProp('category_avatar');
    }

    /**
     * @param int $iSize
     *
     * @return string
     */
    public function getAvatarUrl($iSize = 64) {

        if ($sUrl = $this->getAvatar()) {
            if (!$iSize) {
                return $sUrl;
            } else {
                $sUrl .= '-' . $iSize . 'x' . $iSize . '.' . pathinfo($sUrl, PATHINFO_EXTENSION);
                if (Config::Get('module.image.autoresize')) {
                    $sFile = $this->Uploader_Url2Dir($sUrl);
                    if (!F::File_Exists($sFile)) {
                        $this->Img_Duplicate($sFile);
                    }
                }
                return $sUrl;
            }
        } else {
            $sPath = $this->Uploader_GetUserImageDir(0) . 'avatar_category_' . Config::Get('view.skin') . '.png';
            if ($iSize) {
                $sPath .= '-' . $iSize . 'x' . $iSize . '.' . pathinfo($sPath, PATHINFO_EXTENSION);
            }
            if (Config::Get('module.image.autoresize') && !F::File_Exists($sPath)) {
                $this->Img_AutoresizeSkinImage($sPath, 'avatar_category', $iSize ? $iSize : null);
            }
            return $this->Uploader_Dir2Url($sPath);
        }
        return $this->getProp('category_avatar');
    }
}

// EOF