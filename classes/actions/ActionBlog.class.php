<?php
/*---------------------------------------------------------------------------
 * @Project: Alto CMS
 * @Project URI: http://altocms.com
 * @Description: Advanced Community Engine
 * @Copyright: Alto CMS Team
 * @License: GNU GPL v2 & MIT
 *----------------------------------------------------------------------------
 */

class PluginCategories_ActionBlog extends PluginCategories_Inherits_ActionBlog {

    protected function EventShowBlog() {

        $xResult = parent::EventShowBlog();
        $aCategories = E::Module('Category')->GetItemsByFilter(array(), 'Category');
        E::Module('Viewer')->Assign('aCategories', $aCategories);

        $sBlogUrl = $this->sCurrentEvent;
        if ($sBlogUrl && ($oBlog = E::Module('Blog')->GetBlogByUrl($sBlogUrl)) && ($oCategory = $oBlog->getCategory())) {
            E::Module('Viewer')->Assign('oCurrentCategory', $oCategory);
        }
        return $xResult;
    }

    /**
     * @param $oBlog
     * 
     * @return mixed
     */
    protected function _addBlog($oBlog) {

        $xResult = parent::_addBlog($oBlog);
        $iCategoryId = (int)F::GetRequest('category_id');

        if ($xResult && !C::Get('plugin.categories.multicategory')  && C::Get('plugin.categories.select_category')) {
            if ($iCategoryId && E::Category_GetByFilter(array('category_id' => $iCategoryId), 'Category')) {
                $oRel = Engine::GetEntity('Category_CategoryRel');
                $oRel->setCategoryId($iCategoryId);
                $oRel->setBlogId($oBlog->getId());
                $oRel->Add();
            }
        }

        return $xResult;
    }

    /**
     * @param $oBlog
     * 
     * @return mixed
     */
    protected function _updateBlog($oBlog) {

        $xResult = parent::_updateBlog($oBlog);
        $iCategoryId = (int)F::GetRequest('category_id');

        if ($xResult && $iCategoryId && !C::Get('plugin.categories.multicategory')  && C::Get('plugin.categories.change_category')) {
            $aRelations = E::Module('Category')->GetItemsByFilter(array('blog_id' => $oBlog->getId()), 'Category_CategoryRel');
            if ($aRelations) {
                $oRel = reset($aRelations);
                if ($oRel->getCategoryId() != $iCategoryId) {
                    // category was changed - delete relation
                    $oRel->delete();
                    // create new relation
                    $oRel = Engine::GetEntity('Category_CategoryRel');
                    $oRel->setCategoryId($iCategoryId);
                    $oRel->setBlogId($oBlog->getId());
                    $oRel->Add();
                }
            }
        }

        return $xResult;
    }
    
}

// EOF