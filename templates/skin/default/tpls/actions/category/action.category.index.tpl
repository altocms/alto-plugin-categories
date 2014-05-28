{extends file="_index.tpl"}

{block name="layout_vars"}
    {$menu="topics"}
{/block}

{block name="layout_content"}

    <div class="category">
        <header class="category-header">
            <h1 class="category-title"></h1>
        </header>
    </div>
{foreach from=$aCategories item=oCategory}

<section class="category-home">
    <header class="category-home-header">
        <h3><a href="{$oCategory->getUrl()}">{$oCategory->getTitle()|escape:'html'}</a></h3>
    </header>

    <div class="row">
        <div class="col-sm-8">
            <div class="row">
            {$aTopics = $oCategory->getTopTopics()}
            {foreach $aTopics as $oTopic}
                <div class="col-sm-6 category-home-topic">
                <div class="panel panel-default category-home-topic-body">
                    <header class="topic-header">
                        {if $oTopic->getPreviewImage()}
                            <img src="{$oTopic->getPreviewImageWebPath('229crop')}" alt="image" />
                        {/if}
                        <h1 class="category-topic-title">
                            <a title="{$oTopic->getTitle()|escape:'html'}" href="{$oTopic->getUrl()}">{$oTopic->getTitle()|escape:'html'}</a>
                        </h1>
                    </header>
                    {$oTopic->getText()|strip_tags|trim|truncate:100:'...'}
                </div>
                </div>

            {/foreach}
            </div>
        </div>
        <div class="col-sm-4 panel panel-default">
            <h4>{$aLang.plugin.categories.new}</h4>

            <ul class="list-unstyled ">
                {$aTopics = $oCategory->getNewTopics()}
                {foreach $aTopics as $oTopic}
                    <li class="category-home-item">
                        <a href="{$oTopic->getUrl()}" class="top-topic-link">{$oTopic->getTitle()|escape:'html'}</a>
                        <time class="category-home-topic-date text-muted" datetime="{date_format date=$oTopic->getDateAdd() format='c'}" title="{date_format date=$oTopic->getDateAdd() format='j/m, H:i'}">{date_format date=$oTopic->getDateAdd() format="j.m, H:i"}</time>
                    </li>
                {/foreach}
            </ul>
        </div>
        <div class="clearfix"></div>
    </div>
</section>

{/foreach}

{/block}