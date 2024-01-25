<link rel="stylesheet" type="text/css" href="{$PEM_ROOT_URL_PLUGINS}styles/css/single_view.css">

{*Start of single_view tpl*}
<div id="single_view" class="container">
  <section class="section-fluid">
    <a href="{$PEM_ROOT_URL}index.php?cid={$extension_categories.id_category}&page=1" class="orange-link">
      <i class="icon-chevron-left"></i>Back to {$extension_categories.name}s
    </a>
  </section>

{if isset($MESSAGE)}
  <div class="alert {if $MESSAGE_TYPE == "success"}alert-success{/if} mt-3" role="alert">
    <span>{$MESSAGE}</span><br>
  </div>
{/if}
  

{if isset($can_modify) && $can_modify == true}
  <section  class="mt-4 section-fluid">
    <div class="d-flex justify-content-end">
      <div class="form-check form-switch ">
        <input class="form-check-input" type="checkbox" role="switch" id="edit_mode">
        <label class="form-check-label" for="edit_mode">Edit mode</label>
      </div>
  {if isset($u_owner) && $u_owner == true && $current_user_id}
      <div class="ms-4">
        <span class="link-secondary link" data-bs-toggle="modal" data-bs-target="#deleteExtensionModal">
          <i class="icon-trash"></i>Delete extension
        </span>
      </div>
  {/if}
    </div>
  </section>
{/if}


  <section class="mt-4 section-fluid">
  <div class="row">
    <div class="col-md-6 position-relative" id="info-container">
{if isset($can_modify) && $can_modify == true}
      <span class="circle-icon edit_mode position-absolute top-0 end-0 translate-middle" data-bs-toggle="modal" data-bs-target="#generalInfoModal">
        <i class="icon-pencil"></i>
      </span>
{/if}

      <div>
        <h2>{if isset($extension_name)}{$extension_name}{/if}</h2>
      </div>

      <div>
{foreach from=$authors item=author}
        <a href = "{$PEM_ROOT_URL}index.php?uid={$author.uid}"><h4 class="author d-inline link">{$author.username}{if !$author@last}, {/if}</h4></a>
{/foreach}
{if isset($can_modify) && $can_modify == true}
  <span class="edit_mode secondary_icon" data-bs-toggle="modal" data-bs-target="#authorsModal">
    <i class="icon-pencil"></i>
  </span>
{/if}
      </div> 

      <div class="mt-5">
          <h3 class="category ">
            <a href="{$PEM_ROOT_URL}index.php?cid={$extension_categories.id_category}&page=1" class="orange-link">
              {$extension_categories.name}
            </a>
          </h3>
        </a>
      </div>

{if $extension_tags != null}
      <div>
  {foreach from=$extension_tags item=tag}
        <h5 class="tag d-inline">{$tag.name}{if !$tag@last}, {/if}</h5>
  {/foreach}
      </div>
{/if}

      <div class="mt-5">
{if isset($download_last_url)}
        <a href="{$download_last_url}" rel="nofollow" target="_blank"><button class="btn btn-primary d-inline">Download</button></a>
{else}
        <button class="btn btn-secondary d-inline disabled" disabled>Download</button>
{/if}

{if isset($revisions)}
        <p class="revision-version d-inline ml-3">Revision {$revisions[0]['version']}</p>
{/if}

{if isset($last_date_formatted_since)}
        <span class='badge blue-badge d-inline'>{$last_date_formatted_since}</span>
{/if}
      </div>
    </div>

    <div class="col-md-6 text-center position-relative" id="image-container">
{if isset($can_modify) && $can_modify == true}
      <span class="circle-icon edit_mode position-absolute top-0 end-0 translate-middle" data-bs-toggle="modal" data-bs-target="#ImageModal">
        <i class="icon-pencil" ></i>
      </span>
{/if}

{if isset($screenshot)}
      <img class="img-fluid screenshot_image" src="{$screenshot}">
{else}
      <img class="img-fluid placeholder_image" src="{$PEM_ROOT_URL_PLUGINS}images/image-solid.svg"> 
{/if}
    </div>
  </div>

  </section>

{*info numbers block *}
  <section class="mt-5 pt-3 section-fluid">
    <div class="text-center">
{if isset($extension_downloads)}
      <div class="px-3 py-2 border-right d-inline-block">
        <span><i class="icon-download"></i>{$extension_downloads}</span>
      </div>
{/if}
{if isset($rate_summary.rating_score)}
      <div class="px-3 py-2 border-right d-inline-block">
        {$rate_summary.rating_score}
      </div>
{/if}
{if isset($latest_compatible_version) && $latest_compatible_version != null}
      <div class="px-3 py-2 border-right d-inline-block">
        <span><i class="icon-check"></i>Compatible with Piwigo {$latest_compatible_version}</span>
      </div>
{/if}
{if isset($first_date)}
      <div class="px-3 py-2 d-inline-block">
        <span><i class="icon-rocket"></i>{$first_date}</span>
  {if isset($first_date_formatted_since)}
        <span class='badge blue-badge d-inline'>{$first_date_formatted_since}</span>
  {/if}
      </div>
{/if}
{if !empty($ext_languages)}
  <div class="px-3 py-2 border-left d-inline-block">
    <span class="link" data-bs-toggle="modal" data-bs-target="#displayLanguagesModal"><i class="icon-language"></i> {'%s Available languages'|translate:{$ext_languages|@count}}</span>
  </div>
{/if}
    </div>
    
  </section>

{*Description block *}
  <section class="mt-5 pt-3 section-fluid position-relative">

{* Reactivate this span once the description is in a seperate modal *}
{* {if $can_modify == true}
    <span class="circle-icon edit_mode position-absolute top-0 end-0 translate-middle" data-bs-toggle="modal" data-bs-target="#DescriptionModal">
      <i class="icon-pencil" ></i>
    </span>
{/if} *}

{if isset($description)}
    <div>
      <p class="extension_description">{$description}</p>
    </div>
{/if}
  </section>

{* Links block edit mode *}
  <section class="mt-5 pt-3 section-fluid position-relative edit_mode">
    <div class="edit_links">
      <h3 class="mb-3">Related links</h3>

      <div class="my-3">
        <button class="btn btn-tertiary" data-bs-toggle="modal" data-bs-target="#addLinkModal">
          <i class="icon-link"></i>Add a link
        </button>
      </div>

      <table class="table">
        <thead>
          <tr>
            <th scope="col">Links</th>
            <th scope="col">Language</th>
            <th scope="col">Action</th>
          </tr>
        </thead>
        <tbody>

{foreach from=$all_extension_links item=link}
          <tr>
            <td>
              <a class="orange-link my-3" href="{$link.url}">
                {if $link.name|stristr:"Github page"}<i class="icon-github"></i>{/if}
                {if $link.name|stristr:"Issues"}<i class="icon-bug"></i>{/if}
                {if $link.name|stristr:"Buy me a coffee"}<i class="icon-gift"></i>{/if}
                {if $link.name|stristr:"Forum topic"}<i class="icon-message"></i>{/if}
                {if $link.name|stristr:"Demo"}<i class="icon-message"></i>{/if}
                {$link.name}
              </a>
            </td>
            <td>
  {if isset($link.lang)}
                  <span class="ms-0 badge blue-badge d-inline">{$link.lang}</span>
  {else}
                  <span class="ms-0 badge purple-badge d-inline">All languages</span>
  {/if}
            </td>
            <td>
  {if $can_modify == true}
              <span class="circle-icon edit_mode me-2" data-bs-toggle="modal" data-bs-target="#RelatedLinkModal">
  {if isset($can_modify) && $can_modify == true}
              <span class="circle-icon edit_mode me-2" data-bs-toggle="modal" data-bs-target="#editLinkModal" 
                {if isset($link.id_link)}data-bs-link-id="{$link.id_link}"{/if}
                {if isset($link.name)} data-bs-link-name="{$link.name}"{/if}
                {if isset($link.url)} data-bs-link-url="{$link.url}" {/if}
                {if isset($link.id_lang)} data-bs-link-lang="{$link.id_lang}"{/if}
              >
                <i class="icon-pencil"></i>Edit
              </span>
              <span class="circle-icon secondary_action">
                <i class="icon-trash translate-middle"></i>Delete
              </span>
  {/if}
            </td>
          </tr>
{/foreach}

        </tbody>
      </table>
    </div>
  </section>

{* Links block non edit mode *}
  <section class="mt-5 pt-3 section-fluid related_links">

    <h3 class="mb-3">Related links</h3>
    <div class="d-flex justify-content-start flex-wrap">
  {foreach from=$links item=link}
        {if $link.name|stristr:"Forum topic"}<i class="icon-message"></i>{/if}
        {$link.name}
      </a>
  {/foreach}
    </div>

  </section>
{* {/if} *}

{*Revision block *}
  <section class="mt-5 pt-3 section-fluid">
    <h3 class="mb-3">Revisions</h3>

    <div class="edit_mode mt-3 mb-4">
      <button class="btn btn-tertiary" data-bs-toggle="modal" data-bs-target="#editSvnGitModal">
        <i class="icon-git-alt"></i> SVN & Git configuration
      </button>
      <button class="btn btn-tertiary ms-3" data-bs-toggle="modal" data-bs-target="#addRevisionModal">
        <i class="icon-circle-plus"></i> Add a revision
      </button>
    </div>

{if isset($revisions)}
    <div id="changelog" class="position-relative">
  {foreach from=$revisions item=rev}

    {if $rev@iteration == 1}
      <div id="rev{$rev.id}" class="changelogRevision card latest_rev mt-0 position-relative">
    {else}
      <div id="rev{$rev.id}" class="changelogRevision card position-relative">
    {/if}

  {if isset($can_modify) && $can_modify == true}
      <div class="position-absolute end-0 me-5">
        <span class="circle-icon edit_mode main_action z-index me-2 pe-0" data-bs-toggle="modal" data-bs-target="#revsionInfoModal" onclick="popinToggleDisplay('generalInfo')">
          <i class="icon-pencil" ></i>
        </span>
        <span class="edit_mode circle-icon secondary_action"><i class="icon-trash"></i></span>
      </div>
  {/if}
       
  
        <div id="rev{$rev.id}_header" {if $rev.expanded} class="changelogRevisionHeaderExpanded pb-4" {else} class="changelogRevisionHeaderCollapsed pb-0"{/if} onclick="revToggleDisplay('rev{$rev.id}_header', 'rev{$rev.id}_content')">
            <div class="revision_title_container d-flex justify-content-between">
              <h4 class="revisionTitle">{'Revision'|@translate} {$rev.version}</h4>
              <div class="">
 
                <span><i {if $rev.expanded}class="icon-chevron-down"{else}class="icon-chevron-right"{/if}></i>
              </div>
            </div>
            {* <span class="revisionDate"> {$rev.downloads} {'Downloads'|@translate}, {'Released on'|@translate} {$rev.date}</span> *}
        </div>
    
        <div id="rev{$rev.id}_content" class="changelogRevisionContent pt-4" {if !$rev.expanded} style="display:none" {/if}>
          
          <div class="d-flex justify-content-start">
            <p class="me-4">Released on {$rev.date}</p>
            <p class="me-4"><i class="icon-check"></i>Compatible with Piwigo {$rev.versions_compatible}</p>
            <p class="me-4"><i class="icon-download"></i>{$rev.downloads}</p>
          </div>

          {if !empty($rev.author)}
            <div class="mt-4">
              <p>{'Added by'|@translate}: {$rev.author}</p>
            </div>
          {/if}

          <div class="mt-4">
            <h5>Description</h5>
            <p>{$rev.description}</p>
          </div>
  
      {if !empty($rev.languages)}
        {if !empty($rev.languages_diff)}
          <div class="mt-4">
            <h5>{'New languages'|@translate}:</h5>
            {foreach from=$rev.languages_diff item=language name=flag}{strip}
              <span class="langflag-{$language.code}" title="{$language.name}">{$language.name}</span>
            {/strip}{/foreach}
              {* <a href="#flags-{$rev.id}" class="flags-popup">{'Total :'|translate} {$rev.languages|@count}</a> *}
            </p>
          </div>
        {/if}
          <div class="mt-4"> 
            <h5>{'Available languages'|@translate}:</h5>
        
            <div class="d-flex justify-content-start flex-wrap" >
              {foreach from=$rev.languages item=language name=langs}{strip}
                <p class="me-3">{$language.name}</p>
              {/foreach}
            </div>
          </div>
      {/if}

          <div class="row mt-4">
            <a href="{$rev.u_download}" title="{'Download revision'|@translate} {$rev.version}" rel="nofollow">
              <button class="btn btn-tertiary">
                <i class="icon-download"></i>Download this revision
              </button>
            </a>
          </div>

        </div>

      </div> <!-- rev{$rev.id} -->
  {/foreach}
    </div> <!-- changelog -->
{else}
    <p>{'No revision available for this extension.'|@translate}</p>
{/if}
    
  </section>
  
  {$PEM_EDIT_GENERAL_INFO_FORM}
  {$PEM_EDIT_REVISION_FORM}
  {$PEM_EDIT_IMAGE_FORM}
  {* TODO seperate the description into a seperate modal *}
  {* {$PEM_EDIT_DESCRIPTION_FORM} *}
  {$PEM_EDIT_RELATED_LINK_FORM}
  {$PEM_EDIT_AUTHORS_FORM}
  {$PEM_EDIT_SVN_GIT_FORM}
  {$PEM_ADD_REVISION_FORM}
  {$PEM_DELETE_EXTENSION}
  {$PEM_DISPLAY_LANGUAGES}

</div>

<script src="{$PEM_ROOT_URL_PLUGINS}template/js/single_view.js" require="jquery"></script>

<script>
  //allows any filters set in list view to be cleared 
  sessionStorage.clear()
</script>
