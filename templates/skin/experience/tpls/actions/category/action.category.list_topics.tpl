{extends file="_index.tpl"}

{block name="layout_vars"}
    {$menu="topics"}
{/block}

{block name="layout_content"}
    <div class="panel panel-default user-info raised">
        <div class="panel-body">
            <div class="row user-info-block">
                <div class="col-lg-20">
                    <img class="user-logo" src="{$oCategory->getAvatarUrl()}" alt="avatar"/>
                    <div class="user-name">
                        <div class="user-login-block">
                            <span class="user-login">
                                {$oCategory->getCategoryTitle()|escape:'html'}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {include file='topics/topic.list.tpl'}
{/block}