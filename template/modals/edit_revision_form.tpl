<section id="revisionInfo-popin">
    <div>
      <div class="modal fade" id="revisionInfoModal" tabindex="-1" aria-labelledby="revisionInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="revisionInfoModalLabel"><i class="icon-code-commit rotate-90"></i> {'Edit Revision'|translate}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" enctype="multipart/form-data" autocomplete="off" id="revisionInfoForm">

              <div class="modal-body">

              {* Revision Id*}
              <input type="hidden" name="rid" id="rid" value="">

              {* Revision version*}
              <div class="mb-3 form-group">
                <label for="revision_version" class="form-label w-100 ">{'Version'|translate}</label>
                  <input class="form-control revision_version" type="text" value="" required disabled>
                  <input type="hidden" class="revision_version" name="revision_version" value="">
                </div>


              {* Compatibility *}
              <div class="mb-3 form-group">
                <div class="form-group">
                  <label for="revision_compatible_versions" class=" pb-2">{'Compatibility'|translate}</label>
                  <select class="revision_compatible_versions" required data-selectize="authors"
                    data-create="true" name="compatible_versions[]" multiple="multiple" data-placeholder="{'Choose compatible versions...'|translate}">
{foreach from=$VERSIONS_PWG item=version}
                    <option value="{$version.id_version}">{$version.version}</option>
{/foreach}
                  </select>
                </div>
              </div>

                {*  Description *}
                <div class="mb-3 form-group">
                  <div class="form-group">
                    <label for="revision_lang_desc_select" id="revision_lang_desc_select" class="col-12">{'Description language'|translate}</label>
                    <select name="lang_desc_select" id="lang_desc_select" class="form-select w-100">
{foreach from=$languages item=language}
                      <option value="{$language.id}" id="opt_{$language.id}" {if $default_language == $language.code}selected{/if}>{$language.name}</option>
{/foreach}
                    </select>
                  </div>

                  <div class="pt-3">
{foreach from=$languages item=language}
                    <div id="desc_block_{$language.id}" class="desc" style="display: none;">
                      <input type="radio"  name="default_description" value="{$language.id}" {if $default_language == $language.code}checked{/if} hidden>
                      <textarea class="form-control" name="revision_descriptions[{$language.id}]" id="desc_{$language.id}" {if $language.code eq 'en_UK'} class="desc_{$language.code}" required{/if}></textarea>
                      {if $default_language == $language.code}<p>{'Default description'|translate}</p>{/if}
                    </div>
{/foreach}
                  </div>
                </div>

              {* revision authors *}
              <div class="mb-3 form-group">
                <label class="col-12">{'Author'|translate}</label>
                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
{foreach from=$authors item=author}
                  <input type="radio" name="author" value="{$author.uid}" id="author_{$author.uid}" class="btn-check" autocomplete="off">
                  <label class="btn btn-outline-primary" for="author_{$author.uid}">{$author.username}</label>
{/foreach}  
{if $add_current_user == true}
                  <input type="radio" name="author" value="{$current_user_id}" id="author_{$current_user_id}" class="btn-check" autocomplete="off">
                  <label class="btn btn-outline-primary" for="author_{$current_user_id}">{$current_user_name}</label>
{/if}
                </div>
              </div>             

          {* Revision languages *}
{if !empty($languages)}
              <div class="mb-3 form-group">
                <label for="revison_languages" class="col-12">{'Available language'|translate}</label>
                <div>
                  <span class="detectLang link link-primary" id="detectLangRid" ><i class="icon-language"></i> {'Detect languages'|translate}</span>
                  <div class="d-none spinner my-2">
                    <div class="spinner-border" role="status"></div>
                  </div>
                </div>
                <div>
                  <select class="revison_languages" data-selectize="authors"
                  placeholder="{'Choose available languages'|translate}"
                  data-create="true" name="revision_languages[]" multiple style="width:calc(100% + 2px);">
  {foreach from=$languages item=lang}
                    <option value="{$lang.id}">{$lang.name}</option>
  {/foreach}
                  </select>
                </div>
              </div>
{/if}

            </div>

            <input type="hidden" name="pem_action" value="edit_revision">

            <div class="modal-footer">
              <button type="button" class="btn btn-tertiary small-btn" data-bs-dismiss="modal">{'Close'|translate}</button>
              <div class="spinner-border d-none" role="status">
                <span class="visually-hidden">{'Loading...'|translate}</span>
              </div>
              <input type="submit" class="btn btn-primary small-btn" value="{'Save Changes'|translate}" name="submit" onclick="onSubmitDisplaySpinner('revisionInfoForm');"/>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>