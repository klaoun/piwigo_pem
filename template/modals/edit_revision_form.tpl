<section id="revisionInfo-popin">
    <div>
      <div class="modal fade" id="revisionInfoModal" tabindex="-1" aria-labelledby="revisionInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="revisionInfoModalLabel"><i class="icon-code-commit rotate-90"></i> Edit Revision</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

            {* Revision version*}
            <input type="hidden" name="revision_version" id="revision_id" value="">

{if $file_needed}
            {* File *}
            <div class="mb-3 form-group">
              <label class="form-label w-100 ">{'File'|translate}</label>
              <div>

{if in_array('upload', $upload_methods)}

                <div class="form-check d-inline-block">
                    <input class="form-check-input" type="radio" name="file_type" id="file_type_upload" value="upload" {if $FILE_TYPE=='upload'}checked{/if}/>
                    <label class="form-check-label" for="file_type_upload">
                    {'Upload a file'|translate}</label>
                </div>
{/if}
{if in_array('svn', $upload_methods)}
                <div class="form-check d-inline-block ms-3">
                  <input class="form-check-input" type="radio" name="file_type" id="file_type_svn" value="svn" {if $FILE_TYPE=='svn'}checked{/if}/>
                  <label class="form-check-label" for="file_type_svn">{'SVN'|translate}</label>
                </div>
{/if}
{if in_array('git', $upload_methods)}
              <div class="form-check d-inline-block ms-3">
                <input class="form-check-input" type="radio" name="file_type" id="file_type_git" value="git" {if $FILE_TYPE=='git'}checked{/if}/>
                <label class="form-check-label" for="file_type_git">{'Git'|translate}</label>
              </div>
{/if}
{if in_array('url', $upload_methods)}
              <div class="form-check d-inline-block ms-3">
                  <input class="form-check-input" type="radio" name="file_type" id="file_type_url" value="url" {if $FILE_TYPE=='url'}checked{/if}/>
                  <label class="form-check-label" for="file_type_url">{'Download from URL'|translate}</label>
              </div>
{/if}
              </div>

              <div id="upload_types" class="col-12 mt-2">
{if in_array('upload', $upload_methods)}
                <div id="upload_type"{if $FILE_TYPE ne 'upload'} class="d-none"{/if}>
                  <input class="form-control" type="file" name="revision_file" size="35">
                </div>
{/if}
    
{if in_array('svn', $upload_methods)}
                <div id="svn_type"{if $FILE_TYPE ne 'svn'} class="d-none"{/if}>
                  <label class="form-check-label">{'URL'|translate}</label>
                  <input class="form-control" type="text" name="svn_url" {if isset($SVN_URL)}value="{$SVN_URL}" readonly="readonly" {/if} size="65" >
                  
                  <label class="form-check-label">{'Revision'|translate}</label>
                <input class="form-control" type="text" name="svn_revision" {if isset($SVN_REVISION)}value="{$SVN_REVISION}"{/if} size="5">
                </div>
{/if}
    
{if in_array('git', $upload_methods)}
              <div id="git_type"{if $FILE_TYPE ne 'git'} class="d-none"{/if}>
                <label class="form-check-label">{'URL'|translate}</label>
                <input class="form-control" type="text" name="git_url" {if isset($GIT_URL)}value="{$GIT_URL}" readonly="readonly" {/if} size="65">
                <label class="form-check-label">{'Branch'|translate}</label>
                <input class="form-control" type="text" name="git_branch" {if isset($GIT_BRANCH)}value="{$GIT_BRANCH}"{/if} size="10">
              </div>
{/if}
    
{if in_array('url', $upload_methods)}
                <div id="url_type"{if $FILE_TYPE ne 'url'} class="d-none"{/if}>
                {'URL'|translate} <input class="form-control" type="text" name="download_url" {if isset($DOWNLOAD_URL)}value="{$DOWNLOAD_URL}"{/if} size="65">
                </div>
{/if}
              </div>

            </div>
{/if}

            {* Compatibility *}
            <div class="mb-3 form-group">
              <div class="form-group">
                <label for="revision_compatible_versions" class=" pb-2">{'Compatibility'|translate}</label>
                <select class="revision_compatible_versions" data-selectize="authors"
                  data-create="true" name="compatible_versions[]" multiple="multiple" data-placeholder="{'Choose compatible versions...'|translate}">
{foreach from=$versions item=version}
                  <option value="{$version.id_version}" {$version.selected}>{$version.name}</option>
{/foreach}
                </select>
              </div>
            </div>

            {*  Description *}
            <div class="mb-3 form-group">
              <div>
                <label for="revision_lang_desc_select" class="col-12">{'Description language'|translate}</label>
                <select name="revision_lang_desc_select" class="form-select w-100">
                  <option value="en_UK" id="opt_en_UK" selected>English [UK]</option>
                </select>
              </div>
              <div class="pt-3">
                <input type="hidden" name="default_description" value="5" checked="checked">
                <label for="revision_descriptions">{'Description'|translate}</label>
                <textarea class="form-control" name="revision_descriptions" id="revision_descriptions" required>
                </textarea>
              </div>
            </div>
                

          {* Revision languages *}
{if !empty($languages)}
            <div class="mb-3 form-group">
              <label for="revison_languages" class="col-12">{'Available language'|translate}</label>
              <div>
                <span class="detectLang link link-primary" onclick="detectLang()"><i class="icon-language"></i> {'Detect languages'|translate}</span> 
                <img id="detectLangLoad" src="template/images/ajax-loader.gif" style="display:none">
              </div>
              <div>
                <select class="revison_languages" data-selectize="authors"
                placeholder="{'Choose available languages'|translate}"
                data-create="true" name="revision_languages[]" multiple style="width:calc(100% + 2px);">
  {foreach from=$languages item=lang}
                  <option value="{$lang.id}" >{$lang.name}</option>
  {/foreach}
                </select>
              </div>
            </div>
{/if}

{if $use_agreement}
<tr>
  <th>{'Agreement'|translate}</th>
  <td>
    <label><input type="checkbox" name="accept_agreement" {$accept_agreement_checked}>{$agreement_description}</label>
  </td>
</tr>
{/if}

          </div>

            <input type="hidden" name="pem_action" value="edit_revision">

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary">Save changes</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>