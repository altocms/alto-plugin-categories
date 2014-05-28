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

class PluginCategories_ModuleBlog extends PluginCategories_Inherits_ModuleBlog {

    public function Init() {

        $xResult = parent::Init();
        $this->aAdditionalData[] = 'relation_category';
        return $xResult;
    }

    public function GetBlogsAdditionalData($aBlogId, $aAllowData = null, $aOrder = null) {

        $aBlogs = parent::GetBlogsAdditionalData($aBlogId, $aAllowData, $aOrder);
        if ($aBlogs && isset($this->aAdditionalData)) {
            $aCategories = $this->Category_GetCategoriesByBlogId($aBlogId);
            foreach ($aCategories as $iBlogId => $aBlogCategories) {
                if (isset($aBlogs[$iBlogId])) {
                    $aBlogs[$iBlogId]->setCategories($aBlogCategories);
                }
            }
        }
        return $aBlogs;
    }

}

// EOF