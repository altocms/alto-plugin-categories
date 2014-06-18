<label {if $sHomePageSelect == 'category_homepage'}class="checked"{/if}>
    <input type="radio" name="homepage" value="category_homepage" {if $sHomePageSelect == 'category_homepage'}checked{/if}/>
    {$aLang.plugin.categories.set_links_homepage_categories}
</label>
