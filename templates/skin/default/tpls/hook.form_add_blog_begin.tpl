{if $aCategories}
    <div class="form-group">
        {if $sEvent=='add'}
            {* create blog *}
            <label for="">{$aLang.plugin.categories.new_blog_category}</label>
            <select name="category_id" id="category_id" class="form-control">
                {foreach $aCategories as $oCategory}
                    <option value="{$oCategory->getId()}">
                        {$oCategory->getTitle()|escape:'html'}
                    </option>
                {/foreach}
            </select>
            <p class="help-block">
                <small>{$aLang.plugin.categories.new_blog_category_notice}</small>
            </p>
        {else}
            {if $_aRequest.category_id}
                {$iBlogCategoryId = $_aRequest.category_id}
            {/if}
            {* edit blog *}
            <label for="">{$aLang.plugin.categories.edit_blog_category}</label>
            <select name="category_id" id="category_id" class="form-control">
                {foreach $aCategories as $oCategory}
                    <option value="{$oCategory->getId()}" {if $iBlogCategoryId==$oCategory->getId()}selected{/if}>
                        {$oCategory->getTitle()|escape:'html'}
                    </option>
                {/foreach}
            </select>
            <p class="help-block">
                <small>{$aLang.plugin.categories.edit_blog_category_notice}</small>
            </p>
        {/if}
    </div>
{else}

{/if}
