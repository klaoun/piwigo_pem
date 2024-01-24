<section id="addRevision-popin">
  <div>
    <div class="modal fade" id="addRevisionModal" tabindex="-1" aria-labelledby="generalInfoModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="generalInfoModalLabel"><i class="icon-code-commit"></i>Add a revision</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form method="POST" action="{$f_action}" enctype="multipart/form-data">

            <div class="modal-body">

              {* Revision version*}
              <div class="mb-3 form-group">
                <label for="revision_version" class="form-label w-100 ">Version</label>
{if $IN_EDIT}
                <input type="hidden" name="revision_version" value="{$name}">{$name}
{else}
                <input class="form-control" type="text" name="revision_version" size="10" maxlength="10" {if $translator}disabled="disabled"{/if}/>
{/if}
              </div>

{if $file_needed}
              {* File *}
              <div class="mb-3 form-group">
                <label class="form-label w-100 ">File</label>
                <div>

  {if in_array('upload', $upload_methods)}

                  <div class="form-check d-inline-block">
                    <label class="form-check-label" for="file_type">
                      <input class="form-check-input" type="radio" name="file_type" value="upload" onClick="javascript:showOnlyThisChild('upload_types', 'upload_type');" {if $FILE_TYPE=='upload'}checked{/if}>
                    Upload a file</label>
                  </div>
  {/if}
  {if in_array('svn', $upload_methods)}
                <div class="form-check d-inline-block ms-3">
                  <label class="form-check-label" for="file_type">
                    <input class="form-check-input" type="radio" name="file_type" value="svn" onClick="javascript:showOnlyThisChild('upload_types', 'svn_type');" {if $FILE_TYPE=='svn'}checked{/if}>
                  SVN</label>
                  </div>
  {/if}
  {if in_array('git', $upload_methods)}
                <div class="form-check d-inline-block ms-3">
                  <label class="form-check-label" for="file_type">
                    <input class="form-check-input" type="radio" name="file_type" value="git" onClick="javascript:showOnlyThisChild('upload_types', 'git_type');" {if $FILE_TYPE=='git'}checked{/if}>
                  Git</label>
                </div>
  {/if}
  {if in_array('url', $upload_methods)}
                <div class="form-check d-inline-block ms-3">
                  <label class="form-check-label" for="file_type">
                    <input class="form-check-input" type="radio" name="file_type" value="url" onClick="javascript:showOnlyThisChild('upload_types', 'url_type');" {if $FILE_TYPE=='url'}checked{/if}>
                  Download from URL</label>
                </div>
  {/if}
                </div>

                <div id="upload_types" class="col-12 mt-2">
  {if in_array('upload', $upload_methods)}
                  <div id="upload_type"{if $FILE_TYPE ne 'upload'} style="display: none;"{/if}>
                    <input class="form-control" type="file" name="revision_file" size="35">
                  </div>
  {/if}
      
  {if in_array('svn', $upload_methods)}
                  <div id="svn_type"{if $FILE_TYPE ne 'svn'} style="display: none;"{/if}>
                    <label class="form-check-label">URL</label>
                    <input class="form-control" type="text" name="svn_url" value="{$SVN_URL}" size="65">
                    
                    <label class="form-check-label">Revision</label>
                    <input class="form-control" type="text" name="svn_revision" value="{$SVN_REVISION}" size="5">
                  </div>
  {/if}
      
  {if in_array('git', $upload_methods)}
                  <div id="git_type"{if $FILE_TYPE ne 'git'} style="display: none;"{/if}>
                    <label class="form-check-label">URL</label>
                    <input class="form-control" type="text" name="git_url" value="{$GIT_URL}" size="65">
                    <label class="form-check-label">Branch</label>
                    <input class="form-control" type="text" name="git_branch" value="{$GIT_BRANCH}" size="10">
                  </div>
  {/if}
      
  {if in_array('url', $upload_methods)}
                  <div id="url_type"{if $FILE_TYPE ne 'url'} style="display: none;"{/if}>
                    URL <input class="form-control" type="text" name="download_url" value="{$DOWNLOAD_URL}" size="65">
                  </div>
  {/if}
                </div>
  
              </div>
{/if}

              {* Compatibility *}
              <div class="mb-3 form-group">
                <div class="form-group">
                  <label for="revision_compatible_versions" class=" pb-2">Compatibility</label>
                  <select class="revision_compatible_versions" data-selectize="authors"
                    data-create="true" name="compatible_versions[]" multiple="multiple" data-placeholder="{'Choose compatible versions...'|@translate}">
{foreach from=$versions item=version}
                    <option value="{$version.id_version}" {$version.selected}>{$version.name}</option>
{/foreach}
                  </select>
                </div>
              </div>

              {* Authors *}
{if $authors > 1}
              <div class="mb-3 form-group">
              <label class="form-label w-100 ">Author</label>
                <div>
  {foreach from=$authors item=author}
                  <div class="form-check d-inline-block me-3">
                    <label class="form-check-label">
                      <input class="form-check-input" type="radio" name="author" value="{$author}" {if $author == $selected_author}checked="checked"{/if} {if $translator}disabled="disabled"{/if}>
                    {$author}</label>
                  </div>
  {/foreach}
                </div>
              </div>
{/if}

              {* revision description, at the moment only text area is avalaible,
                TODO add language select, and move description to seperate modal  *}

                <div class="mb-3 form-group">
                <div>
                  <label for="revision_lang_desc_select" class="col-12">Description language</label>
                  <select name="revision_lang_desc_select" class="form-select w-100">
                    <option value="en_UK" id="opt_en_UK" selected>English [UK]</option>
                  </select>
                </div>
                <div class="pt-3">
                  <input type="hidden" name="default_description" value="5" checked="checked">
                  <label for="revision_descriptions">Description</label>
                  <textarea class="form-control" name="revision_descriptions[5]" id="desc_5" 
                  {if $translator and !$language.id|@in_array:$translator_languages}disabled="disabled"{/if} required></textarea>
                </div>
              </div>
                  
              {* revision languages *}


{if !empty($extensions_languages)}
              <div class="mb-3 form-group">
                <label>Available language</label>
                <div>
                  <a id="detectLang" href="#"{if not $IN_EDIT} style="display:none"{/if}>{'Detect languages'|@translate}</a> 
                    {* <img id="detectLangLoad" src="template/images/ajax-loader.gif" style="display:none"> *}
                  <select name="extensions_languages[]" id="extensions_languages" data-placeholder="{'Choose available languages...'|@translate}" multiple="multiple" {if $translator}disabled="disabled"{/if}>
{foreach from=$extensions_languages item=lang}
                    <option value="{$lang.id}" {$lang.selected}>[{$lang.code}] {$lang.name}</option>
{/foreach}
                  </select>
                </div>
              </div>
{/if}

{if $use_agreement}
  <tr>
    <th>{'Agreement'|@translate}</th>
    <td>
      <label><input type="checkbox" name="accept_agreement" {$accept_agreement_checked} {if $translator}disabled="disabled"{/if}>{$agreement_description}</label>
    </td>
  </tr>
{/if}

            </div>

            <input type="hidden" name="pem_action" value="add_revision">

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <input type="submit" class="btn btn-primary" value="Submit" name="submit" />
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</section>