<section id="addRevision-popin">
  <div>
    <div class="modal fade" id="addRevisionModal" tabindex="-1" aria-labelledby="generalInfoModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="generalInfoModalLabel"><i class="icon-code-commit"></i> {'Add a revision'|translate}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form method="POST" enctype="multipart/form-data" autocomplete="off" id="addRevisionForm">

            <div class="modal-body">

              {* Revision version*}
              <div class="mb-3 form-group">
                <label for="revision_version" class="form-label w-100 ">{'Version'|translate}</label>
                <input class="form-control" type="text" name="revision_version" size="10" maxlength="10" required/>
              </div>

{if isset($file_needed)}
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
                    <textarea class="form-control" name="revision_descriptions[{$language.id}]" id="desc_{$language.id}" {if $language.code eq 'en_UK'} class="desc_{$language.code}"{/if} {if $default_language == 5}required{/if}></textarea>
                    {if $default_language == $language.code}<p><small>{'Default description'|translate}</small></p>{/if}
                  </div>
{/foreach}
                </div>
              </div>
                  
              {* revision authors *}
              <div class="mb-3 form-group">
                <label class="col-12">{'Author'|translate}</label>
                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
{foreach from=$authors item=author}
                  <input type="radio" name="author" value="{$author.uid}" id="author_{$author.uid}" class="btn-check" autocomplete="off" {if $current_user_id == $author.uid}checked{/if}>
                  <label class="btn btn-outline-primary" for="author_{$author.uid}">{$author.username}</label>
{/foreach}  
{if $add_current_user == true}
                  <input type="radio" name="author" value="{$current_user_id}" id="author_{$current_user_id}" class="btn-check" autocomplete="off" checked>
                  <label class="btn btn-outline-primary" for="author_{$current_user_id}">{$current_user_name}</label>
{/if}
                </div>
            </div>
            
          {* Revision languages *}
{if !empty($languages)}
              <div class="mb-3 form-group">
                <label for="revison_languages" class="col-12">{'Available language'|translate}</label>
                <div>
                  <span class="detectLang link link-primary" onclick="detectLang()"><i class="icon-language"></i> {'Detect languages'|translate}</span> 
                </div>
                <div>
                  <select class="revison_languages" data-selectize="authors"
                  placeholder="{'Choose available languages'|translate}"
                  data-create="true" name="extensions_languages[]" multiple style="width:calc(100% + 2px);">
  {foreach from=$languages item=lang}
                    <option value="{$lang.id}" {if in_array($lang.id,$extensions_languages_ids)}selected{/if}>{$lang.name}</option>
  {/foreach}
                  </select>
                </div>
              </div>
{/if}

{if $use_agreement}
  <tr>
    <th>{'Agreement'|translate}</th>
    <td>
      <label><input type="checkbox" name="accept_agreement" {$accept_agreement_checked} >{$agreement_description}</label>
    </td>
  </tr>
{/if}
            </div>

            <input type="hidden" name="pem_action" value="add_revision">

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{'Close'|translate}</button>
              <div class="spinner-border d-none" role="status">
                <span class="visually-hidden">{'Loading...'|translate}</span>
              </div>
              <input type="submit" class="btn btn-primary" value="Submit" name="submit" onclick="onSubmitDisplaySpinner('addRevisionForm');"/>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<script>

var languages = new Array();
var filled = new Array;

{foreach from=$languages item=language}
languages[{$language.id}] = "{$language.name}";
if ($('textarea[id=desc_{$language.id}]').val() != '')
  filled.push({$language.id});
{/foreach}


$(document).ready(function() {
  $('input[name="default_description"]').click(function () {
    set_default_description(this.value);
  });

  // Hide all description blocks, display the one linked to the selected language
  $(".desc").hide();
  var selected_desc_lang = jQuery('#lang_desc_select').val();
  jQuery('#desc_block_'+selected_desc_lang).show()

  jQuery('input[name="lang_desc_select"]').on('change', function() {
    $(".desc").hide();
    var selected_desc_lang = jQuery('#lang_desc_select').val();
    console.log(selected_desc_lang)
    jQuery('#desc_block_'+selected_desc_lang).show()
  });



  // $('textarea[name^="revision_descriptions"]').keyup(function () {
  //   arr = $(this).attr("id").split("desc_");
  //   id = arr[1];
  //   opt = $('select[name="lang_desc_select"] option[id="opt_'+id+'"]');
  //   if (this.value != '') {
  //     opt.html(opt.html().replace("\u2718", "\u2714"));
  //     add = true;
  //     for (i in filled) {
  //       if (filled[i] == id) add = false;
  //     }
  //     if (add) {
  //       if (!filled.length) {
  //         $('#span_'+id+' input[name="default_description"]').attr("checked", "checked");
  //         set_default_description(id);
  //       }
  //       filled.push(id);
  //     }
  //   }
  //   else {
  //     for (i in filled) {
  //       if (filled[i] == id) filled.splice(i, 1);
  //     }
  //     opt.html(opt.html().replace("\u2714", "\u2718"));
  //   }
  // });


});

// function set_default_description (id) {
//   $(".default_description").html("{'Default description'|@translate}: "+languages[id]);
// }

// $("#span_{$default_language}").show();
// set_default_description("{$default_language}");

</script>