 {* Тема оформления Experience RC1 build 1  для CMS Alto версии 1.0      *}
 {* @licence     Dual licensed under the MIT or GPL Version 2 licenses   *}

{extends file="_index.tpl"}

{block name="layout_vars"}
    {$menu="topics"}
{/block}

{block name="layout_content"}

{foreach from=$aCategories item=oCategory}
    {$aTopics = $oCategory->getTopTopics()}
    {foreach $aTopics as $oTopic}
        {$oBlog = $oTopic->getBlog()}
        {$oUser=$oTopic->getUser()}
        {$oBlogType=$oBlog->getBlogType()}

    <!-- Блок новости -->
    <div class="panel panel-default topic topic-news raised">
    <div class="panel-body row">
    <!-- Левая колонка -->
    <div class="col-md-12 left-column">
        <!-- Превью -->
        {$iMainPhotoId = $oTopic->getPhotosetMainPhotoId()}
        {if $iMainPhotoId}
            {$aPhotos = $oTopic->getPhotosetPhotos()}
            {foreach $aPhotos as $oPhoto}
                {if $oPhoto->getId() == $iMainPhotoId}
                    <a href="{$oTopic->getUrl()}" class="topic-preview">
                        <img src="{$oPhoto->getUrl('x229')}" alt="{$oPhoto->getDescription()}" class="" />
                    </a>
                    {break}
                {/if}
            {/foreach}
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
        <div class="topic-text">
            {$oTopic->getText()|strip_tags|trim|truncate:200:'...'}
        </div>
        <div class="topic-footer">
            <ul>
                <li class="topic-user">
                    <img src="{$oUser->getAvatarUrl(16)}" alt="{$oUser->getDisplayName()}"/>
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
                    <a href="#" onclick="return false;" class="vote-down link link-gray link-clear js-vote-down"><i class="fa fa-thumbs-o-down"></i></a>
                    {if $bVoteInfoShow}
                        <span class="vote-total js-vote-rating {$sVoteClass}">{if $oTopic->getRating() > 0}+{/if}{$oTopic->getRating()}</span>
                    {else}
                        <a href="#" class="vote-down link link-gray link-clear" onclick="return ls.vote.vote({$oTopic->getId()},this,0,'topic');">?</a>
                    {/if}
                    <a href="#" onclick="return false;" class="vote-up link link link-gray link-clear js-vote-up"><i class="fa fa-thumbs-o-up"></i></a>
                </li>
            </ul>
        </div>
    </div>
    <!-- Правая колонка -->
    <div class="col-md-12 right-column">
    {$aTopics = $oCategory->getNewTopics()}
    {foreach $aTopics as $oNewTopic}
        <div class="topic-list-container">
            <table class="topic-list">
                <tr>
                    <td class="topic-list-rating-container">
                        <span class="topic-list-rating">{if $oNewTopic->getRating() > 0}+{/if}{$oNewTopic->getRating()}</span>
                    </td>
                    <td class="topic-list-title-container">
                        <h5 class="topic-list-title accent">
                            <a href="{$oNewTopic->getUrl()}" class="link link-header link-lead link-dual">
                                {$oNewTopic->getTitle()|escape:'html'}
                            </a>
                        </h5>
                    </td>
                    {$iMainPhotoId = $oNewTopic->getPhotosetMainPhotoId()}
                    {if $iMainPhotoId}
                        {$aPhotos = $oNewTopic->getPhotosetPhotos()}
                        {foreach $aPhotos as $oPhoto}
                            {if $oPhoto->getId() == $iMainPhotoId}
                                <td rowspan="2" class="topic-list-preview-container">
                                    <a href="{$oNewTopic->getUrl()}" class="topic-list-preview">
                                        <img src="{$oPhoto->getUrl('74crop')}" alt="{$oPhoto->getDescription()}">
                                    </a>
                                </td>
                                {break}
                            {/if}
                        {/foreach}
                    {/if}
                </tr>
                <tr>
                    <td colspan="2" class="topic-list-info">
                                    <span class="topic-user">
                    {$oNewUser = $oNewTopic->getUser()}
                    <img src="{$oNewUser->getAvatarUrl(16)}" alt="{$oNewUser->getDisplayName()}"/>
                    <a class="userlogo link link-dual link-lead link-clear js-popup-{$oNewUser->getId()}" href="{$oNewUser->getProfileUrl()}">
                        {$oNewUser->getDisplayName()}
                    </a>
                                    </span>
               <span class="topic-date-block">
                    <span class="topic-date">{$oNewTopic->getDate()|date_format:'d.m.Y'}</span>
                    <span class="topic-time">{$oNewTopic->getDate()|date_format:"H:i"}</span>
                </span>
                    </td>
                </tr>
            </table>
        </div>
    {/foreach}

    </div>


    </div>
        <!-- подвал новости -->
        <div class="topic-footer">
            <ul>
                <li class="topic-blog">
                    <a class="link link-dual link-lead link-clear" href="{$oCategory->getUrl()}">
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
    {/foreach}



{/foreach}

{/block}