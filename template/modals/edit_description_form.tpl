<section id="Description-popin">
    <div>
      <div class="modal fade" id="DescriptionModal" tabindex="-1" aria-labelledby="DescriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="DescriptionModalLabel"></h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
{if isset($u_translator) && $u_translator == true}
            <form method="POST" enctype="multipart/form-data" autocomplete="off" id="descriptionForm">
              <div class="modal-body">

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
                      {if !in_array($language.id,json_decode($translator_lang_ids))}<p class="red-font">{'You don\'t have permission to edit this translation'|translate}</p>{/if}

                      <textarea class="form-control" name="descriptions[{$language.id}]" id="desc_{$language.id}" {if $default_language == $language.id}required{/if}>
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

              <input type="hidden" name="pem_action" value="">

              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{'Close'|translate}</button>
                <div class="spinner-border d-none" role="status">
                  <span class="visually-hidden">{'Loading...'|translate}</span>
                </div>
                <input type="submit" class="btn btn-primary" value="{'Save Changes'|translate}" name="submit" onclick="onSubmitDisplaySpinner('descriptionForm');"/>
              </div>

            </form>
{/if}

          </div>
        </div>
      </div>
    </div>
  </section>