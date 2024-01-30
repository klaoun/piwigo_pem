<section id="generalInfo-popin">
  <div>
    <div class="modal fade" id="generalInfoModal" tabindex="-1" aria-labelledby="generalInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="generalInfoModalLabel"><i class="icon-circle-info"></i>General information</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form method="POST">

            <div class="modal-body">
              {* Extension name *}
              <div class="mb-3 form-group">
                <label for="extension_name" class="form-label w-100 ">Name</label>
                <input type="text" name="extension_name" size="35" maxlength="255" value="{if isset($extension_name)}{$extension_name}{/if}" class="w-100 form-control" required/>
              </div>

              {* Extension category *}
              <div class="mb-3 form-group">
                <label for="extension_category_select" class="col-12">Category</label>
        
                <select class="form-select w-100" id="extension_category_select" name="extension_category[]">
{foreach from=$CATEGORIES_INFO item=category}
                  <option value="{$category.cid}" {if $category.cid == $extension_categories.id_category}selected{/if}>{$category.name}</option>
{/foreach}
                </select>
              </div>

              {* Extension Tags *}
              <div class="mb-3 form-group">
                <label for="extension_tag_select" class="col-12">Tags</label>
                <select class="extension_tag_select" data-selectize="tags"
                placeholder="{'Select tags'|translate}"
                data-create="true" name="tags[]" multiple style="width:calc(100% + 2px);">
    {foreach from=$ALL_TAGS item=tag}
                  <option value="{$tag.tid}" {if in_array($tag.tid,$extension_tag_ids)}selected{/if}>
                    {if isset($tag.name)}{$tag.name}{else}{$tag.default_name}{/if}
                  </option>
    {/foreach}
                </select>
              </div>

              {* Extension description, at the moment only text area is avalaible,
                TODO add language select, and move description to seperate modal *}

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
                  <input type="hidden" name="default_description" value="5" checked="checked">
                  <label for="extension_descriptions">Description</label>
                  <textarea class="form-control" name="extension_descriptions[5]" id="desc_5"  required>{$description}</textarea>
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