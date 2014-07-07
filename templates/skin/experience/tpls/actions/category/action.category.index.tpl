{extends file="_index.tpl"}

{block name="layout_vars"}
    {$menu="topics"}
{/block}

{block name="layout_content"}

{foreach from=$aCategories item=oCategory}

    {$aTopics = $oCategory->getTopTopics()}
        {foreach from=$aTopics item=oTopic name=foo}
        {$oBlog = $oTopic->getBlog()}
        {$oUser=$oTopic->getUser()}
        {$oBlogType=$oBlog->getBlogType()}

        {if $smarty.foreach.foo.first}
            <!-- Блок новости -->
        <div class="panel panel-default topic topic-news raised">
            <div class="panel-body row">

                <!-- ЛЕВАЯ КОЛОНКА -->
                <div class="col-md-12 left-column">
                    <!-- Превью -->
                    {assign var ="sMainPhoto" value=$oTopic->getPreviewImageUrl('x229')}
                    {if $sMainPhoto}
                        <a href="{$oTopic->getUrl()}" class="topic-preview"><img src="{$sMainPhoto}" alt="preview" class="" /></a>
                    {/if}
                    <!-- Заголовок -->
                    <h3 class="topic-title accent">
                        <a class="link link-header link-lead" href="{$oTopic->getUrl()}">
                            {$oTopic->getTitle()|escape:'html'}
                        </a>
                    </h3>
                    <!-- Инфо новостьи -->
                    <div class="topic-info">
                                    <span class="topic-blog">
                                        <a class="link link-lead link-blue" href="{$oTopic->getBlog()->getUrlFull()}">
                                            {$oBlog->getTitle()|escape:'html'}
                                        </a>
                                    </span>
                            <span class="topic-date-block">
                                <span class="topic-date">{$oTopic->getDate()|date_format:'d.m.Y'}</span>
                                <span class="topic-time">{$oTopic->getDate()|date_format:"H:i"}</span>
                            </span>
                    </div>
                    <!-- Контент -->
                    <div class="topic-text">{$oTopic->getIntroText()}</div>
                    {* Подвал топика *}
                    <div class="topic-footer">
                        <ul>
                            <li class="topic-user">
                                                    <img src="{$oUser->getAvatarUrl(24)}" alt="{$oUser->getDisplayName()}"/>
                                <a class="userlogo link link-dual link-lead link-clear js-popup-{$oUser->getId()}" href="{$oUser->getProfileUrl()}">
                                    {$oUser->getDisplayName()}
                                </a>
                            </li>
                            <li class="topic-favourite">
                                <a class="link link-dark link-lead link-clear {if E::IsUser() AND $oTopic->getIsFavourite()}active{/if}"
                                   onclick="return ls.favourite.toggle({$oTopic->getId()},this,'topic');"
                                   href="#">
                                    <i class="fa fa-star"></i>
                                    <span class="favourite-count" id="fav_count_topic_{$oTopic->getId()}">{$oTopic->getCountFavourite()}</span>
                                </a>
                            </li>
                            <li class="topic-comments">
                                <a href="{$oTopic->getUrl()}#comments" title="{$aLang.topic_comment_read}" class="link link-dark link-lead link-clear">
                                    <i class="fa fa-comment"></i>
                                    <span>{$oTopic->getCountComment()}</span>
                                    {if $oTopic->getCountCommentNew()}<span class="green">+{$oTopic->getCountCommentNew()}</span>{/if}
                                </a>
                            </li>
                            {$sVoteClass = ""}
                            {if $oVote OR E::UserId()==$oTopic->getUserId() OR strtotime($oTopic->getDateAdd())<$smarty.now-Config::Get('acl.vote.topic.limit_time')}
                                {if $oTopic->getRating() > 0}
                                    {$sVoteClass = "$sVoteClass vote-count-positive"}
                                {elseif $oTopic->getRating() < 0}
                                    {$sVoteClass = "$sVoteClass vote-count-negative"}
                                {/if}
                            {/if}
                            {if $oVote}
                                {$sVoteClass = "$sVoteClass voted"}
                                {if $oVote->getDirection() > 0}
                                    {$sVoteClass = "$sVoteClass voted-up"}
                                {elseif $oVote->getDirection() < 0}
                                    {$sVoteClass = "$sVoteClass voted-down"}
                                {/if}
                            {/if}
                            {if $oTopic->isVoteInfoShow()}
                                {$bVoteInfoShow=true}
                            {/if}

                            <li class="pull-right topic-rating js-vote end" data-target-type="topic" data-target-id="{$oTopic->getId()}">
                                <a href="#" onclick="return false;" class="{$sVoteClass} vote-down link link-gray link-clear js-vote-down"><i class="fa fa-thumbs-o-down"></i></a>
                                {if $bVoteInfoShow}
                                    <span data-placement="top"
                                          data-original-title='
                                            <div id="vote-info-topic-{$oTopic->getId()}">
                                                <ul class="vote-topic-info list-unstyled mal0">
                                                    <li><i class="fa fa-thumbs-o-up"></i><span>{$oTopic->getCountVoteUp()}</span>
                                                    <li><i class="fa fa-thumbs-o-down"></i><span>{$oTopic->getCountVoteDown()}</span>
                                                    <li><i class="fa fa-eye"></i><span>{$oTopic->getCountVoteAbstain()}</span>
                                                    {hook run='topic_show_vote_stats' topic=$oTopic}
                                                </ul>
                                            </div>'
                                          data-html="true"
                                          class="vote-tooltip vote-total js-vote-rating {$sVoteClass}">
                                        {if $oTopic->getRating() > 0}+{/if}{$oTopic->getRating()}
                                    </span>
                                {else}
                                     &nbsp;<a href="#"
                                              data-placement="top"
                                              data-original-title='
                                                <div id="vote-info-topic-{$oTopic->getId()}">
                                                    <ul class="vote-topic-info list-unstyled mal0">
                                                        <li><i class="fa fa-thumbs-o-up"></i><span>{$oTopic->getCountVoteUp()}</span>
                                                        <li><i class="fa fa-thumbs-o-down"></i><span>{$oTopic->getCountVoteDown()}</span>
                                                        <li><i class="fa fa-eye"></i><span>{$oTopic->getCountVoteAbstain()}</span>
                                                        {hook run='topic_show_vote_stats' topic=$oTopic}
                                                    </ul>
                                                </div>'
                                              data-html="true"
                                              class="vote-tooltip vote-down link link-gray js-vote-rating link-clear"
                                              onclick="return ls.vote.vote({$oTopic->getId()},this,0,'topic');">?</a>&nbsp;
                                {/if}
                                <a href="#" onclick="return false;" class="{$sVoteClass} vote-up link link link-gray link-clear js-vote-up"><i class="fa fa-thumbs-o-up"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>


                <!-- ПРАВАЯ КОЛОНКА -->
                <div class="col-md-12 right-column">
                {if count($aTopics) > 1}{continue}{/if}
        {/if}
        {if count($aTopics) > 1}
                    {* Один топик из правой колонки *}
                    <div class="topic-list-container">
                        <table class="topic-list">
                            <tr>
                                <td class="topic-list-title-container">

                                    <h5 class="topic-list-title accent">
                                        <a href="{$oTopic->getUrl()}" class="link link-header link-lead link-dual">
                                            {$oTopic->getTitle()|escape:'html'}
                                        </a>
                                    </h5>
                                    <span
                                            class="label label-{if $oTopic->getRating() > 0}success{else}danger{/if}">{if $oTopic->getRating() > 0}+{/if}{$oTopic->getRating()}</span>
                                </td>
                                {assign var ="sMainPhoto" value=$oTopic->getPreviewImageUrl('74crop')}
                                {if $sMainPhoto}
                                            <td rowspan="2" class="topic-list-preview-container">
                                        <a href="{$oTopic->getUrl()}" class="topic-list-preview">
                                            <img src="{$sMainPhoto}" alt="preview" class="" />
                                                </a>
                                            </td>
                                        {/if}
                            </tr>
                            <tr>
                                <td class="topic-list-info">
                                                <span class="topic-user">
                                {$oNewUser = $oTopic->getUser()}
                                                    <img src="{$oNewUser->getAvatarUrl(24)}" alt="{$oNewUser->getDisplayName()}"/>
                                <a class="userlogo link link-dual link-lead link-clear js-popup-{$oNewUser->getId()}" href="{$oNewUser->getProfileUrl()}">
                                    {$oNewUser->getDisplayName()}
                                </a>
                                                </span>
                           <span class="topic-date-block">
                                <span class="topic-date">{$oTopic->getDate()|date_format:'d.m.Y'}</span>
                                <span class="topic-time">{$oTopic->getDate()|date_format:"H:i"}</span>
                            </span>
                                </td>
                            </tr>
                        </table>
                    </div>
        {/if}
        {if $smarty.foreach.foo.last}
                </div>
            </div>

            <!-- подвал новости -->
            <div class="topic-footer">
                <ul>
                    <li class="topic-blog">
                                <a class="link link-dual link-lead link-clear" href="{$oCategory->getLink()}">
                            {$oCategory->getTitle()|escape:'html'}
                        </a>
                    </li>
                    <li class="topic-blog-subscribe">
                        {if (E::UserId() != $oBlog->getOwnerId()) && $oBlogType->GetMembership(ModuleBlog::BLOG_USER_JOIN_FREE)}
                        <a href="#"  onclick="ls.blog.toggleJoin(this, {$oBlog->getId()}); return false;"
                           class="link link-dark link-lead">
                            {if $oBlog->getUserIsJoin()}
                                {$aLang.blog_leave}
                            {else}
                                {$aLang.blog_join}
                            {/if}
                        </a>
                        {/if}
                    </li>
                </ul>
            </div>
        </div>
        {/if}
    {/foreach}



{/foreach}

{/block}
