<section id="generalInfo-popin">
  <div>
    <div class="modal fade" id="generalInfoModal" tabindex="-1" aria-labelledby="generalInfoModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="generalInfoModalLabel"><i class="icon-circle-info"></i>General information</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form method="post" action="{$f_action}" enctype="multipart/form-data">

            <div class="modal-body">
              {* Extension name *}
              <div class="mb-3 form-group">
                <label for="extension_name" class="form-label w-100 ">Name</label>
                <input type="text" name="extension_name" size="35" maxlength="255" value="{$extension_name}" class="w-100 form-control" required {if $translator}disabled="disabled"{/if}/>
              </div>

              {* Extension Authors *}
              <div class="mb-3 form-group">
                <label for="extension_author_select" class="col-12">Authors</label>
                <select class="extension_author_select" data-selectize="authors" data-value="{$author_selection|@json_encode|escape:html}"
                  placeholder="{'Type in a search term'|translate}"
                  data-create="true" name="authors[]" multiple>
{foreach from=$ALL_AUTHORS item=author}
                  <option value="{$author.uid}" {if $author.uid|array_key_exists:$authors}selected{/if}>{$author.username}</option>
{/foreach}
                </select>
              </div>

              {* Extension category *}
              <div class="mb-3 form-group">
                <label for="extension_category_select" class="col-12">Category</label>
        
                <select class="form-select w-100" id="extension_category_select">
{foreach from=$CATEGORIES_INFO item=category}
                  <option value="{$category.cid}" {if $category.cid == $categories_of_extension.id_category}selected{/if}>{$category.name}</option>
{/foreach}
                </select>
              </div>

              {* Extension Tags *}
              <div class="mb-3 form-group">
                <label for="extension_tag_select" class="col-12">Tags</label>
                <select class="extension_tag_select" data-selectize="tags" data-value="{$tags_selection|@json_encode|escape:html}"
                  placeholder="{'Type in a search term'|translate}"
                  data-create="true" name="tags[]" multiple >
{foreach from=$ALL_TAGS item=tag}
                  <option value="{$tag.tid}" {if $tag.tid|in_array:$extension_tag_ids}selected{/if}>
                    {if $tag.bname}{$tag.name}{else}{$tag.default_name}{/if}
                  </option>
{/foreach}
                </select>
              </div>


              {* Extension description, at the moment only text area is avalaible,
                TODO add language select, and move description to seperate modal  *}

              <div class="mb-3 form-group">
                <div>
                  <label for="extension_lang_desc_select" class="col-12">Description language</label>
                  <select name="extension_lang_desc_select" class="form-select w-100">
                    <option value="en_UK" id="opt_en_UK" selected>English [UK]</option>
                  {* {foreach from=$languages item=language} *}
                    {* <option value="{$language.code}" id="opt_{$language.code}" {if $default_language == $language.id}selected="selected"{/if}>  *}
                      {* {if empty($descriptions[$language.id])}&#x2718;{else}&#x2714;{/if} &nbsp;{$language.name} *}
                        {* {$language.name} *}
                      {* </option> *}
                  {* {/foreach} *}
                  </select>
                </div>
                <div class="pt-3">
                {*{foreach from=$languages item=language}
                  <span id="span_{$language.id}" class="desc" style="display: none;">
                    <label><input type="radio" name="default_description" value="{$language.id}" {if $default_language == $language.id}checked="checked"{/if} {if $translator}disabled="disabled"{/if}> {'Default description'|@translate}</label>
                    <br> *}
                  <input type="radio" name="default_description" value="{$language.id}" hidden {if $default_language == $language.id}checked="checked"{/if} {if $translator}disabled="disabled"{/if}>
                  <label for="extension_descriptions">Description</label>
                  <textarea class="form-control" name="extension_descriptions[{$language.id}]" id="desc_{$language.id}" 
                  {if $translator and !$language.id|@in_array:$translator_languages}disabled="disabled"{/if} required>{$description}</textarea>
                  {* </span>
                {/foreach}
                <p class="default_description"></p> *}
                </div>
              </div>

            </div>

            <input type="hidden" name="pem_action" value="edit_general_info">

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <input type="submit" class="btn btn-primary" value="{'Save Changes'|@translate}" name="submit" />
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</section>