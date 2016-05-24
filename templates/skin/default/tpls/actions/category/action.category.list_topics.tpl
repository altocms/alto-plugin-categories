{extends file="_index.tpl"}

{block name="layout_vars"}
    {$menu="topics"}
{/block}

{block name="layout_content"}
    <div class="blog">
        <header class="blog-header">
            {if $oCategory->getAvatar()}
                <img src="{$oCategory->getAvatarUrl()}" class="avatar"/>
            {/if}
            <h1 class="header">
                {$oCategory->getCategoryTitle()|escape:'html'}
            </h1>
        </header>
    </div>
    <div>
        &nbsp;
    </div>
    {include file='topics/topic.list.tpl'}
{/block}