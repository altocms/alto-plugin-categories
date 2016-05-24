{if $oCurrentCategory}
    {$sCategoryUrl=$oCurrentCategory->getCategoryUrl()}
{/if}

<div class="panel panel-default sidebar raised widget-type-categories">
    <div class="panel-body" style="padding-bottom: 14px;">

            <div class="panel-group nav-accordion" id="widget-category-list">
                {foreach $aCategories as $oCategory}
                <h4 class="panel-header">
                    <a data-toggle="collapse" data-parent="#widget-category-list" href="#category-blogs-{$oCategory->getId()}">
                        <i class="fa fa-folder-open-o"></i>{$oCategory->getTitle()|escape:'html'}
                    </a>
                </h4>

                <div id="category-blogs-{$oCategory->getId()}" class="panel-collapse collapse {if $sCategoryUrl==$oCategory->getCategoryUrl()}in{/if}">
                    <div class="panel-content">
                    {$aBlogs=$oCategory->getBlogs()}
                    {if $aBlogs}
                        {if $aWidgetParams.simple}
                            <ul class="blogs-list" style="margin-bottom: 12px;">
                                {foreach $aBlogs as $oBlog}
                                <li style="border-bottom: 0">
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
                                </li>
                                {/foreach}
                            </ul>
                        {else}
                            {include file="widgets/widget.blogs_top.tpl"}
                        {/if}
                    {/if}
                    </div>
                </div>
                {/foreach}
            </div>

    </div>
    <div class="panel-footer">
        <ul>
            <li>&nbsp;</li>
        </ul>
    </div>
</div>
