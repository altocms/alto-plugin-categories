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

    public function getCategory() {

        $xResult = $this->getProp('category');
        if (is_null($xResult)) {
            $aCategories = $this->getCategories();
            if (is_array($aCategories) && count($aCategories)) {
                $xResult = array_shift($aCategories);
            }
        }
        return $xResult;
    }

}

// EOF