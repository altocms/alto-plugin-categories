<?php

class PluginCategories_ActionBlog extends PluginCategories_Inherits_ActionBlog {

    protected function EventShowBlog() {

        $xResult = parent::EventShowBlog();
        $aCategories = $this->Category_GetItemsByFilter(array(), 'Category');
        $this->Viewer_Assign('aCategories', $aCategories);

        $sBlogUrl = $this->sCurrentEvent;
        if ($sBlogUrl && ($oBlog = $this->Blog_GetBlogByUrl($sBlogUrl)) && ($oCategory = $oBlog->getCategory())) {
            $this->Viewer_Assign('oCurrentCategory', $oCategory);
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

        if ($xResult && !Config::Get('plugin.categories.multicategory')  && Config::Get('plugin.categories.select_category')) {
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

        if ($xResult && $iCategoryId && !Config::Get('plugin.categories.multicategory')  && Config::Get('plugin.categories.change_category')) {
            $aRelations = $this->Category_GetItemsByFilter(array('blog_id' => $oBlog->getId()), 'Category_CategoryRel');
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