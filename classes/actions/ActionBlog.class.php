<?php

class PluginCategories_ActionBlog extends PluginCategories_Inherits_ActionBlog {

    protected function EventShowBlog() {

        $xResult = parent::EventShowBlog();

        $sBlogUrl = $this->sCurrentEvent;
        if ($sBlogUrl && ($oBlog = $this->Blog_GetBlogByUrl($sBlogUrl)) && ($oCategory = $oBlog->getCategory())) {
            $this->Viewer_Assign('oCurrentCategory', $oCategory);
        }
        return $xResult;
    }

}

// EOF