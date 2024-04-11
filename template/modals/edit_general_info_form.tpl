<section id="generalInfo-popin">
  <div>
    <div class="modal fade" id="generalInfoModal" tabindex="-1" aria-labelledby="generalInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="generalInfoModalLabel"><i class="icon-circle-info"></i>{'General information'|translate}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form method="POST" enctype="multipart/form-data" autocomplete="off" id="generalInfoForm">

            <div class="modal-body">
              {* Extension name *}
              <div class="mb-3 form-group">
                <label for="extension_name" class="form-label w-100 ">{'Name'|translate}</label>
                <input type="text" name="extension_name" size="35" maxlength="255" value="{if isset($extension_name)}{$extension_name}{/if}" class="w-100 form-control" required/>
              </div>

              {* Extension category *}
              <div class="mb-3 form-group">
                <label for="extension_category_select" class="col-12">{'Category'|translate}</label>
        
                <select class="form-select w-100" id="extension_category_select" name="extension_category[]">
{foreach from=$CATEGORIES_INFO item=category}
                  <option value="{$category.cid}" {if $category.cid == $extension_categories.id_category}selected{/if}>{$category.name|translate}</option>
{/foreach}
                </select>
              </div>

              {* Extension Tags *}
              <div class="mb-3 form-group">
                <label for="extension_tag_select" class="col-12">{'Tags'|translate}</label>
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

{* Description block *}
              <div class="mb-3 form-group">
                <div>
                  <label for="extension_lang_desc_select" class="col-12">{'Description language'|translate}</label>
                  <select name="extension_lang_desc_select" id="lang_desc_select" class="form-select w-100">
{foreach from=$languages item=language}
                    <option value="{$language.id}" id="opt_{$language.id}" {if $default_language == $language.code}selected{/if}>{$language.name}</option>
{/foreach}
                  </select>
                </div>
                <div class="pt-3">
{foreach from=$languages item=language}

                  <div id="desc_block_{$language.id}" class="desc" style="display: none;">
                    <input type="radio"  name="default_description" value="{$language.id}" {if $default_language == $language.code}checked{/if} hidden>
  {strip}                 
                    <textarea class="form-control" name="extension_descriptions[{$language.id}]" id="desc_{$language.id}" {if $language.code eq 'en_UK'} class="desc_{$language.code}"{/if} {if $default_language == 5}required{/if}>
    {foreach from=$descriptions item=description}
      {if $language.id == $description.id_lang}
                      {$description.description|stripslashes}
      {/if}
    {/foreach}
                    </textarea>
  {/strip}
                    {if $default_language == $language.code}<p>{'Default description'|translate}</p>{/if}
                  </div>

{/foreach}

                </div>
              </div>
            </div>

            <input type="hidden" name="pem_action" value="edit_general_info">

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{'Close'|translate}</button>
              <div class="spinner-border d-none" role="status">
                <span class="visually-hidden">{'Loading...'|translate}</span>
              </div>
              <input type="submit" class="btn btn-primary" value="{'Save Changes'|translate}" name="submit" onclick="onSubmitDisplaySpinner('generalInfoForm');"/>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</section>