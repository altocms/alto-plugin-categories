 {* Тема оформления Experience RC1 build 1  для CMS Alto версии 1.0      *}
 {* @licence     Dual licensed under the MIT or GPL Version 2 licenses   *}

{if $oBlog AND $oBlog->getCategory()}
    {$sCategoryUrl=$oBlog->getCategory()->getCategoryUrl()}
{else}
{/if}
<div class="panel panel-default sidebar raised">
    <div class="panel-body">
        <div class="panel-content js-widget-blogs-content">

            {foreach from=$aCategories item=oCategory}
                <h4 class="panel-header">
                    <i class="fa fa-suitcase"></i>{$oCategory->getTitle()|escape:'html'}
                </h4>



                    <div id="category-blogs-{$oCategory->getId()}" class="panel-content collapse {if $sCategoryUrl==$oCategory->getCategoryUrl()}in{/if}">
                            {$aBlogs=$oCategory->getBlogs()}
                            {if $aBlogs}
                                {if $aWidgetParams.simple}
                                    <ul class="blogs-list">
                                        <li>
                                        {foreach $aBlogs as $oBlog}
                                            <a href="{$oBlog->getUrlFull()}" class="blog-name link link-dual link-lead link-clear">
                                                {$sPath = $oBlog->getAvatarPath(24)}
                                                {if $sPath}
                                                    <img src="{$oBlog->getAvatarPath(24)}" width="24" height="24" class="avatar uppercase"/>
                                                {else}
                                                    <i class="fa fa-folder"></i>
                                                {/if}

                                                {$oBlog->getTitle()|escape:'html'}
                                                <span class="topic-count">{$oBlog->getRating()}</span>
                                            </a>
                                        {/foreach}
                                        </li>
                                    </ul>
                                {else}
                                    {include file="widgets/widget.blogs_top.tpl"}
                                {/if}
                            {/if}
                    </div>

            {/foreach}



        </div>
    </div>
    <div class="panel-footer">
        <ul>
            <li></li>
        </ul>
    </div>
</div>