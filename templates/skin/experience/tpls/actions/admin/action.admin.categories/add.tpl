{extends file='_index.tpl'}

{block name="content-bar"}
    <div class="btn-group">
        <a href="{router page='admin'}content-categories/" class="btn"><i class="icon-chevron-left"></i></a>
    </div>
{/block}

{block name="content-body"}
    <div class="span12">

        <form method="POST" name="typeadd" enctype="multipart/form-data" class="form-horizontal uniform">
            <input type="hidden" name="security_key" value="{$ALTO_SECURITY_KEY}"/>

            <div class="b-wbox">
                <div class="b-wbox-header">
                    {if $sEvent=='categoriesadd'}
                        <div class="b-wbox-header-title">{$aLang.plugin.categories.add_title}</div>
                    {elseif $sEvent=='categoriesedit'}
                        <div class="b-wbox-header-title">{$aLang.plugin.categories.edit_title}
                            : {$oCategory->getCategoryTitle()|escape:'html'}</div>
                    {/if}
                </div>
                <div class="b-wbox-content nopadding">

                    <div class="control-group">
                        <label for="category_title" class="control-label">
                            {$aLang.plugin.categories.category_title}:
                        </label>

                        <div class="controls">
                            <input type="text" id="category_title" name="category_title"
                                   value="{$_aRequest.category_title}"
                                   class="input-text"/>
                            <span class="help-block">{$aLang.plugin.categories.category_title_notice}</span>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="category_url" class="control-label">
                            {$aLang.plugin.categories.category_url}:
                        </label>

                        <div class="controls">
                            <input type="text"
                                   id="category_url" name="category_url" value="{$_aRequest.category_url}"
                                   class="input-text"/>
                            <span class="help-block">{$aLang.plugin.categories.category_url_notice}</span>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="category_avatar" class="control-label">
                            {$aLang.plugin.categories.category_avatar}:
                        </label>

                        <div class="controls">
                            <input type="file"
                                   id="category_avatar" name="category_avatar" value="{$_aRequest.category_avatar}"
                                   class="input-file"/>
                            <span class="help-block">{$aLang.plugin.categories.category_avatar_notice}</span>
                            {if $_aRequest.category_avatar AND $oCategory->getAvatarUrl(64)}
                                <img src="{$oCategory->getAvatarUrl(64)}" class="i-framed"/><br/>
                                <label><input type="checkbox" name="category_avatar_delete"/>{$aLang.plugin.categories.category_avatar_delete}</label>
                            {/if}
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="blogs" class="control-label">
                            {$aLang.plugin.categories.blogs}:
                        </label>

                        <div class="controls">
                            {if $aBlogs}
                                {foreach $aBlogs AS $oBlog}
                                    {$iBlogId = $oBlog->getId()}
                                    {$aBlogCategories = $oBlog->getCategories()}
                                    {$bDisabled = $aBlogCategories AND !Config::Get('plugin.categories.multicategory') AND (!$oCategory OR !$oBlog->getCategory($oCategory))}
                                    <label {if $bDisabled}class="i-disabled"{/if}>
                                        <input type="checkbox" name="blog[{$iBlogId}]" value="1"
                                                {if $_aRequest.blog.$iBlogId}checked{/if}
                                                {if $bDisabled}disabled="disabled" {/if}
                                                class="input-text"/>
                                        -
                                        {if $aBlogCategories}<strong>{/if}
                                        {$oBlog->getTitle()|escape}
                                        {if $aBlogCategories}
                                            ({foreach $aBlogCategories AS $oBlogCategory}
                                                {$oBlogCategory->getTitle()}{if !$oBlogCategory@last},{/if}
                                            {/foreach})
                                        {/if}
                                        {if $aBlogCategories}</strong>{/if}
                                    </label>
                                {/foreach}
                            {else}
                                <span class="help-block">{$aLang.plugin.categories.no_blogs}</span>
                            {/if}
                        </div>
                    </div>

                </div><!-- b-wbox-content -->

                <div class="b-wbox-content nopadding">
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" name="submit_category_add">
                            {$aLang.plugin.categories.category_submit}
                        </button>
                    </div>
                </div>
            </div>
        </form>

    </div>
{/block}