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

class PluginCategories_ModuleBlog_EntityBlog extends PluginCategories_Inherits_ModuleBlog_EntityBlog {

    /**
     * Returns required category of blog or the first category
     *
     * @param int|object $xCategory
     *
     * @return object|null
     */
    public function getCategory($xCategory = null) {

        if ($xCategory) {
            if (is_object($xCategory)) {
                $iCategoryId = $xCategory->getId();
            } else {
                $iCategoryId = intval($xCategory);
            }
        } else {
            $iCategoryId = 0;
        }
        $xResult = $this->getProp('category_' . $iCategoryId);
        if (is_null($xResult)) {
            $xResult = null;
            $aCategories = $this->getCategories();
            if (is_array($aCategories) && count($aCategories)) {
                if ($iCategoryId) {
                    if (isset($aCategories[$iCategoryId])) {
                        $xResult = $aCategories[$iCategoryId];
                    }
                } else {
                    $xResult = reset($aCategories);
                }
            }
            $this->setProp('category_' . $iCategoryId, $xResult ? $xResult : false);
        }
        return $xResult ? $xResult : null;
    }

}

// EOF