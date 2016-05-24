<?php
/*---------------------------------------------------------------------------
* @Project: Alto CMS
* @Project URI: http://altocms.com
* @Description: Advanced Community Engine
* @Copyright: Alto CMS Team
* @License: GNU GPL v2 & MIT
*----------------------------------------------------------------------------
*/

class PluginCategories_ModuleMenu extends PluginCategories_Inherits_ModuleMenu {

    protected function _fillItems($sFillFrom, $aFillSet, $iLimit, $aParams = array()) {

        $aItems = parent::_fillItems($sFillFrom, $aFillSet, $iLimit, $aParams);
        if ($sFillFrom == 'categories') {
            $aItems = array_merge($aItems, $this->_fillItemsFromCategories($aFillSet, $iLimit, $aParams));
        }
        return $aItems;
    }

    /**
     * @param array  $aFillSet
     * @param int    $iLimit
     * @param array  $aParams
     *
     * @return array
     */
    protected function _fillItemsFromCategories($aFillSet, $iLimit, $aParams = array()) {

        $aItems = array();
        $aCategories = array();
        if ($aFillSet) {
            $aCategories = E::Module('Category')->GetCategoriesByUrl($aFillSet);
        } else {
            $aCategories = E::Module('Category')->GetItemsByFilter(array(), 'Category');
        }
        if ($aCategories) {
            foreach($aCategories as $oCategory) {
                $aItems[$oCategory->getUrl()] = array(
                    'text' => $oCategory->getTitle(),
                    'url' => $oCategory->getLink(),
                );
            }
        }

        return $aItems;
    }

}

// EOF