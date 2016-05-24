{extends file='_index.tpl'}

{block name="content-bar"}
    <div class="btn-group">
        <a href="{router page='admin'}content-categories/add/" class="btn btn-primary tip-top"
           title="{$aLang.plugin.categories.add}"><i class="icon icon-plus"></i></a>
    </div>
{/block}

{block name="content-body"}
    <div class="span12">

        <div class="b-wbox">
            <div class="b-wbox-content nopadding">

                <table class="table table-striped table-condensed pages-list">
                    <thead>
                    <tr>
                        <th class="span1">ID</th>
                        <th class="span2">{$aLang.plugin.categories.category_avatar}</th>
                        <th class="span4">{$aLang.plugin.categories.category_title}</th>
                        <th>{$aLang.plugin.categories.category_url}</th>
                        <th>{$aLang.plugin.categories.blogs}</th>
                        <th class="span2">{$aLang.plugin.categories.actions}</th>
                    </tr>
                    </thead>

                    <tbody class="content js-sortable">
                    {if count($aCategories)>0}
                        {foreach from=$aCategories item=oCategory}
                            <tr id="{$oCategory->getCategoryId()}" class="cursor-x">
                                <td class="number">
                                    {$oCategory->getId()}
                                </td>
                                <td class="center">
                                    {if $oCategory->getAvatar()}
                                        <img src="{$oCategory->getAvatarUrl(64)}" class="i-framed" />
                                    {/if}
                                </td>
                                <td>
                                    {$oCategory->getCategoryTitle()|escape:'html'}<br/>
                                    {foreach $aLangList as $sLang}
                                        [ <strong>{$sLang}</strong> ] {$oCategory->getTitle($sLang)|escape:'html'}<br/>
                                    {/foreach}
                                </td>
                                <td>
                                    <strong>{$oCategory->getCategoryUrl()|escape:'html'}</strong>
                                </td>
                                <td>
                                    {$aBlogs = $oCategory->getBlogs()}
                                    {if $aBlogs}
                                        <ul>
                                            {foreach $aBlogs as $oBlog}
                                                <li>{$oBlog->getTitle()|escape:'html'}</li>
                                            {/foreach}
                                        </ul>
                                    {/if}
                                </td>
                                <td class="center">
                                    <a href="{router page='admin'}content-categories/edit/{$oCategory->getCategoryId()}/">
                                        <i class="icon icon-note tip-top" title="{$aLang.plugin.categories.edit}"></i></a>

                                    <a onclick="return confirm('{$aLang.plugin.categories.delete_confirm}')"
                                       href="{router page='admin'}content-categories/delete/{$oCategory->getCategoryId()}/?security_key={$ALTO_SECURITY_KEY}">
                                        <i class="icon icon-trash tip-top"
                                           title="{$aLang.plugin.categories.delete}"></i></a>

                                </td>
                            </tr>
                        {/foreach}
                    {else}
                        <tr><td colspan="4" class="center">{$aLang.action.admin.no_data}</td></tr>
                    {/if}
                    </tbody>
                </table>
            </div>
        </div>

    </div>

{/block}