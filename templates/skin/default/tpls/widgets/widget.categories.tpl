{if $oBlog AND $oBlog->getCategory()}
    {$sCategoryUrl=$oBlog->getCategory()->getCategoryUrl()}
{else}
{/if}
<section class="panel panel-default widget widget-type-categories">
    <div class="panel-body">

        <header class="widget-header">
            <h3 class="widget-title">{$aLang.plugin.categories.categories}</h3>
        </header>

        <div class="panel-group nav-accordion" id="category-list">
            {foreach from=$aCategories item=oCategory}
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#category-list" href="#category-blogs-{$oCategory->getId()}">
                            {$oCategory->getTitle()|escape:'html'}
                        </a>
                    </h4>
                </div>
                <div id="category-blogs-{$oCategory->getId()}" class="panel-collapse collapse {if $sCategoryUrl==$oCategory->getCategoryUrl()}in{/if}">
                    <div class="panel-body">
                        {$aBlogs=$oCategory->getBlogs()}
                        {if $aBlogs}
                            {if $aWidgetParams.simple}
                                <ul class="category-blogs-simple">
                                    {foreach $aBlogs as $oBlog}
                                        <li>
                                            <a href="{$oBlog->getUrlFull()}">{$oBlog->getTitle()|escape:'html'}</a>
                                        </li>
                                    {/foreach}
                                </ul>
                            {else}
                                {include file="widgets/widget.blogs_top.tpl"}
                            {/if}
                        {/if}
                    </div>
                </div>
            </div>
            {/foreach}
        </div>

    </div>
</section>

