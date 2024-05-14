<link rel="stylesheet" type="text/css" href="{$PEM_ROOT_URL_PLUGINS}styles/css/single_view.css">

{*Start of single_view tpl*}
<div id="single_view" class="container">
  <section class="section-fluid">
    <a href="{$PEM_ROOT_URL}index.php?cid={$extension_categories.id_category}&page=1" class="orange-link">
      <i class="icon-chevron-left"></i>{'Back to %s'|translate:$extension_categories.plural_name}
    </a>
  </section>

{if isset($MESSAGE)}
  <div class="alert {if $MESSAGE_TYPE == "success"}alert-success{else if $MESSAGE_TYPE == "error"}alert-danger{/if} mt-3" role="alert">
    <span>{$MESSAGE}</span><br>
  </div>
{/if}
  


  <section  class="mt-4 section-fluid">
    <div class="d-flex justify-content-end">
{if isset($can_modify)}
  {if $can_modify == false && isset($u_translator) && $u_translator == true }
        <div class="form-check form-switch ">
          <input class="form-check-input" type="checkbox" role="switch" id="translation_mode">
          <label class="form-check-label" for="translator_mode">{'Translator mode'|translate}</label>
        </div>
  {else if $can_modify == true}
      <div class="form-check form-switch ">
        <input class="form-check-input" type="checkbox" role="switch" id="edit_mode">
        <label class="form-check-label" for="edit_mode">{'Edit mode'|translate}</label>
      </div>
    {if isset($u_owner) && $u_owner == true || isset($admin) && $admin == true}
      <div class="ms-4">
        <span class="link-secondary link" data-bs-toggle="modal" data-bs-target="#deleteExtensionModal">
          <i class="icon-trash"></i>{'Delete extension'|translate}
        </span>
      </div>
    {/if}
  {/if}
{/if}
    </div>
  </section>

  <section class="mt-4 section-fluid">
  <div class="row">
    <div class="col-md-6 position-relative" id="info-container">
{if isset($can_modify) && $can_modify == true}
      <span class="circle-icon edit_mode position-absolute top-0 end-0 translate-middle" data-bs-toggle="modal" data-bs-target="#generalInfoModal">
        <i class="icon-pencil"></i>
      </span>
{/if}

      <div class="position-relative">
        <h2>{if isset($extension_name)}{$extension_name}{/if}</h2>
      </div>

      <div class="mt-3 position-relative">
        <h4 class="category ">
          <a href="{$PEM_ROOT_URL}index.php?cid={$extension_categories.id_category}&page=1" class="orange-link">
            <i class="{$CATEGORIES_INFO[$extension_categories.id_category].icon_class} me-1"></i>{$extension_categories.name}
          </a>
        </h4>
      </div>

      <div class="mt-5">
        <div class="position-relative mb-1">
{foreach from=$authors item=author}
          <a href="{$PEM_ROOT_URL}index.php?uid={$author.uid}" class="badge green-badge me-2 mb-3 hide-text-overflow-150 ms-0">
            <i class="{if count($authors) >1 && $u_owner_id == $author.uid}icon-crown{else}icon-user{/if}"></i>{$author.username}
          </a>
{/foreach}
{if isset($can_modify) && $can_modify == true}
          <span class="circle-icon secondary_action edit_mode position-absolute" data-bs-toggle="modal" data-bs-target="#authorsModal">
            <i class="icon-pencil"></i>
          </span>
{/if}
        </div> 

{if isset($extension_tags)}
        <div class="position-relative mb-3">
  {foreach from=$extension_tags item=tag}
    {if isset($tag.name)}
          <span class="badge orange-badge mb-2 ms-0"><i class="icon-tag"></i>{$tag.name}</span>
    {/if}
  {/foreach}
        </div>
{/if}

{if !empty($ext_languages)}
        <div class="position-relative mb-3">
          <span class="link badge purple-badge ms-0"  data-bs-toggle="modal" data-bs-target="#displayLanguagesModal"
          {if isset($ext_languages)}data-bs-rev-languages='{json_encode($ext_languages)}'{/if}>
            <i class="icon-language"></i> {'%s Available languages'|translate:{$ext_languages|@count}}
          </span>
        </div>  
{/if}
      </div>

      <div class="mt-5">
{if isset($download_last_url)}
        <a href="{$download_last_url}" rel="nofollow" target="_blank"><button class="btn btn-primary d-inline">{'Download'|translate}</button></a>
{else}
        <button class="btn btn-secondary d-inline disabled" disabled>{'Download'|translate}</button>
{/if}

{if isset($revisions)}
        <p class="revision-version d-inline"><i class="icon-code-branch"></i>{'Revision %s'|translate:$revisions[0]['version']}</p>
{/if}

{if isset($last_date_formatted_since)}
        <span class='badge blue-badge d-inline ms-2'>{$last_date_formatted_since}</span>
{/if}
      </div>
    </div>

    <div class="col-md-6 text-center position-relative" id="image-container">
{if isset($can_modify) && $can_modify == true}
      <span class="circle-icon edit_mode position-absolute top-0 end-0 translate-middle" data-bs-toggle="modal" data-bs-target="#ImageModal">
        <i class="icon-pencil"></i>
      </span>
{/if}

      <div class="image-background w-100 h-100 position-relative">
{if isset($screenshot)}
        <img class="img-fluid extension_image position-absolute vertical-horizontal-align" src="{$screenshot}">
{else}
        <i class="icon-image position-absolute vertical-horizontal-align"></i>
{/if}
      </div>
    </div>
  </div>

  </section>

{*info numbers block *}
  <section class="mt-5 pt-3 section-fluid">
    <div class="text-center">
{if isset($extension_downloads)}
      <div class="px-3 py-1 d-inline-block">
        <span><i class="icon-download me-1"></i>{$extension_downloads}</span>
      </div>
{/if}
{if isset($rate_summary.rating_score)}
      <div class="px-3 py-1 border-left d-inline-block">
        {$rate_summary.rating_score}
      </div>
{/if}
      <div class="px-3 py-1 border-left d-inline-block">
{if isset($latest_compatible_version) && $latest_compatible_version == $pwg_latest_version.version} 
        <span><i class="icon-check green-font me-1"></i>{'Compatible with the latest version of Piwigo'|translate}</span>
{else}
        <span><i class="icon-cross red-font me-1"></i>{'Not compatible with the latest version of Piwigo'|translate}</span>
{/if}
      </div>
{if isset($first_date)}
      <div class="px-3 py-1 border-left d-inline-block">
        <span><i class="icon-rocket me-1"></i>{$first_date}</span>
  {if isset($first_date_formatted_since)}
        <span class='badge blue-badge d-inline ms-2'>{$first_date_formatted_since}</span>
  {/if}
      </div>
{/if}

    </div>
    
  </section>

{*Description block *}
  <section class="mt-5 pt-3 section-fluid position-relative">

{* only for to translators *}
{if isset($u_translator) && $u_translator == true}
  <span class="circle-icon translation_mode position-absolute top-0 end-0 translate-middle" 
    data-bs-toggle="modal" 
    data-bs-target="#DescriptionModal"
    data-bs-modal_title ="{'Translate extension description'|translate}"
    data-bs-pem_action ="edit_extension_translation"
    data-bs-lang_ids = "{json_encode($translator_lang_ids)}"
    data-bs-descriptions = '{$json_descriptions}'
  >
    <i class="icon-edit-language"></i>
  </span>
{/if}

{* Display description depending on interface language, else display default language *}
{if isset($descriptions)}
  {foreach from=$descriptions item=description}
    {if $CURRENT_LANG == $description.id_lang && !is_null($description.description)}
    <div>
      <p class="extension_description">{$description.description|stripslashes|nl2br}</p>
    </div>
      {assign var="default" value=false}
      {break}
    {else}
     {assign var="default" value=true}
    {/if}
  {/foreach}
  {if true == $default}
    <div>
      <p class="extension_description">{$default_description|stripslashes|nl2br}</p>
    </div>
  {/if}
{/if}
  </section>

{* Links block edit mode *}
{if isset($can_modify) && $can_modify == true}
  <section class="mt-5 pt-3 section-fluid position-relative edit_mode">
    <div class="edit_links">
      <h3 class="mb-3">{'Related links'|translate}</h3>

      <div class="my-3">
        <button class="btn btn-tertiary" data-bs-toggle="modal" data-bs-target="#addLinkModal">
          <i class="icon-link me-2"></i>{'Add a link'|translate}
        </button>
      </div>

      <table class="table">
        <thead>
          <tr>
            <th scope="col">{'Links'|translate}</th>
            <th scope="col">{'Language'|translate}</th>
            <th scope="col">{'Action'|translate}</th>
          </tr>
        </thead>
        <tbody>

{foreach from=$links item=link}
          <tr>
            <td>
              <a class="link-primary orange-link my-3" href="{$link.url}" target="blank">
                {if $link.name|stristr:"git"}<i class="icon-github"></i>
                {else if $link.name|stristr:l10n("issues") || $link.name|stristr:"bug" }<i class="icon-bug"></i>
                {else if $link.name|stristr:l10n("coffee")}<i class="icon-gift"></i>
                {else if $link.name|stristr:l10n("forum")}<i class="icon-message"></i>
                {else if $link.name|stristr:l10n("demo")}<i class="icon-piwigo"></i>
                {else}<i class="icon-link"></i>
                {/if}
                {$link.name}
              </a>
            </td>
            <td>
  {if isset($link.language)}
                  <span class="ms-0 badge purple-badge d-inline">{$link.language}</span>
  {/if}
            </td>
            <td>
  {if isset($can_modify) && $can_modify == true}
    {if $link.id_link !== "git" && $link.id_link !== "svn" }
              <span class="circle-icon edit_mode me-2" data-bs-toggle="modal" data-bs-target="#editLinkModal" 
                {if isset($link.id_link)}data-bs-link-id="{$link.id_link}"{/if}
                {if isset($link.name)} data-bs-link-name="{$link.name}"{/if}
                {if isset($link.url)} data-bs-link-url="{$link.url}" {/if}
                {if isset($link.id_lang)} data-bs-link-lang="{$link.id_lang}"{/if}
              >
                <i class="icon-pencil"></i>Edit
              </span>

              <span class="edit_mode circle-icon secondary_action " 
                  data-bs-toggle="modal" 
                  data-bs-target="#deleteLinkModal"
                  data-bs-link_id="{$link.id_link}"
                  data-bs-ext_id="{$extension_id}"
                  data-bs-root_url="{$PEM_ROOT_URL}"
                  >
                  <i class="icon-trash translate-middle"></i>
                </span>
    {/if}
  {/if}
            </td>
          </tr>
{/foreach}

        </tbody>
      </table>
    </div>
  </section>
{/if}

{* Links block non edit mode *}
  <section class="mt-5 pt-3 section-fluid related_links">
    <h3 class="mb-3">{'Related links'|translate}</h3>
{if !empty($links)}
    <div class="d-flex justify-content-start flex-wrap">
  {foreach from=$links item=link}
      <a class="orange-link py-3 pe-3" href="{$link.url}" target="_blank">
        {if $link.name|stristr:"git"}<i class="icon-github"></i>
        {else if $link.name|stristr:l10n("issues") || $link.name|stristr:"bug" }<i class="icon-bug"></i>
        {else if $link.name|stristr:l10n("coffee")}<i class="icon-gift"></i>
        {else if $link.name|stristr:l10n("forum")}<i class="icon-message"></i>
        {else if $link.name|stristr:l10n("demo")}<i class="icon-piwigo"></i>
        {else}<i class="icon-link"></i>
        {/if}
        {$link.name}
      </a>
  {/foreach}
    </div>
{else}
      <p>{'No links are available for this extension.'|translate}</p>
{/if}
  </section>

{*Revision block *}
  <section class="mt-5 pt-3 section-fluid">
    <h3 class="mb-3">{'Revisions'|translate} {if isset($count_rev)}<span class="badge blue-badge d-inline align-middle">{$count_rev}</span>{/if}</h3>
{if isset($can_modify) && $can_modify == true}
    <div class="edit_mode mt-3 mb-4">
      <button class="btn btn-tertiary" data-bs-toggle="modal" data-bs-target="#editSvnGitModal">
        <i class="icon-git-alt"></i> {'SVN & Git configuration'|translate}
      </button>
      <button class="btn btn-tertiary ms-3" data-bs-toggle="modal" data-bs-target="#addRevisionModal">
        <i class="icon-circle-plus"></i> {'Add a revision'|translate}
      </button>
    </div>
{/if}

{if isset($revisions)}
    <div id="changelog" class="position-relative">
  {foreach from=$revisions item=rev}
    <!-- rev{$rev.id} -->
    {if $rev@iteration == 1}
      <div id="rev{$rev.id}" class="changelogRevision card latest_rev mt-0 position-relative">
    {else}
      <div id="rev{$rev.id}" class="changelogRevision card position-relative">
    {/if}
      <div class="card-body">
      
        <div id="rev{$rev.id}_header" {if $rev.expanded} class="changelogRevisionHeaderExpanded pb-4" {else} class="changelogRevisionHeaderCollapsed pb-0"{/if}>
            <div class="revision_title_container d-flex justify-content-between">
              <h4 class="revisionTitle cursor-pointer" onclick="revToggleDisplay('rev{$rev.id}_header', 'rev{$rev.id}_content')">{'Revision'|translate} {$rev.version}</h4>
              <div class="d-flex justify-content-end align-items-center">
                <div class="d-flex justify-content-start">
                  <p class="me-4"><i class="icon-rocket me-1"></i>{'Released on %s'|translate:$rev.date}</p>
                  <p class="me-4"><i class="icon-download me-1"></i>{$rev.downloads}</p>
                </div>
    {if isset($u_translator) && $u_translator == true}
                <span class="circle-icon translation_mode main_action me-2" 
                data-bs-toggle="modal" 
                data-bs-target="#DescriptionModal"
                data-bs-pem_action ="edit_revision_translation"
                data-bs-modal_title="{'Translate revision description'|translate}"
                data-bs-rev_id="{$rev.id}" 
                data-bs-lang_ids = '{json_encode($translator_lang_ids)}'
                data-bs-descriptions= '{$rev.rev_json_descriptions}'
                >
                  <i class="icon-edit-language"></i>
                </span>
    {/if}

    {if isset($can_modify) && $can_modify == true}
                <span class="circle-icon edit_mode main_action me-2" 
                  data-bs-toggle="modal" data-bs-target="#revisionInfoModal"
                  data-bs-rev_id="{$rev.id}" 
                  data-bs-rev_version_name="{$rev.version}" 
                  data-bs-rev_versions_compatible="{$rev.ids_versions_compatible}"
                  data-bs-descriptions = '{$rev.rev_json_descriptions}'
                  data-bs-rev_author="{$rev.author_id}"
                >
                  <i class="icon-pencil"></i>
                </span>
                <span class="edit_mode circle-icon secondary_action me-2" 
                  data-bs-toggle="modal" 
                  data-bs-target="#deleteRevisionModal"
                  data-bs-rev_id="{$rev.id}"
                  data-bs-ext_id="{$extension_id}"
                  data-bs-root_url="{$PEM_ROOT_URL}"
                  >
                  <i class="icon-trash translate-middle"></i>
                </span>
    {/if}
                <span class="cursor-pointer" onclick="revToggleDisplay('rev{$rev.id}_header', 'rev{$rev.id}_content')"><i {if $rev.expanded}class="icon-chevron-down"{else}class="icon-chevron-right"{/if}></i>
              </div>
            </div>

            
        </div>
    
        <div id="rev{$rev.id}_content" class="changelogRevisionContent pt-4" {if !$rev.expanded} style="display:none" {/if}>
          
          <div class="d-flex justify-content-start">
            <p class="me-3"><i class="icon-piwigo me-1"></i>{'Compatible with Piwigo %s'|translate:$rev.versions_compatible}</p>
            <span class="badge blue-badge ms-0 me-3"><i class="icon-clock me-1"></i>{$rev.age}</span>
    {if !empty($rev.author)}
              <span>
              <a href="{$PEM_ROOT_URL}index.php?uid={$rev.author_id}" class="badge green-badge me-3 ms-0 hide-text-overflow-150">
                <i class="icon-user me-1"></i>{$rev.author}
              </a>
              </span>
    {/if}
    {if !empty($rev.languages)}
              <span class="link badge purple-badge ms-0" data-bs-toggle="modal" data-bs-target="#displayLanguagesModal"
                {if isset($rev.languages)}data-bs-rev-languages='{json_encode($rev.languages)}'{/if}
                {if isset($rev.languages_diff)}data-bs-new-languages='{json_encode($rev.languages_diff)}'{/if}
              >
      {strip}
                <i class="icon-language me-1"></i>
        {if !empty($rev.languages_diff)}
          
                {'%s Available languages'|translate: ($rev.languages|@count)}<span class="ms-2 inner-badge purple-badge">+ {$rev.languages_diff|@count}</span>
        {else}
                  {* We assume there will always be more than one language *}
                  {'%s Available languages'|translate:{$rev.languages|@count}}
        {/if}
      {/strip}
              </span>
    {/if}
          </div>

          <div class="mt-4">
      {* We have an array of all revsions and all descriptions in all languages *}

    {if isset($rev.rev_descriptions)}
      {foreach from=$rev.rev_descriptions item=rev_desc}
        {if $CURRENT_LANG == $rev_desc.id_lang && !is_null($rev_desc.description)}
          
            <p class="revision_description">{$rev_desc.description|stripslashes|nl2br}</p>
          
            {assign var="default" value=false}
            {break}
          {else}
           {assign var="default" value=true}
          {/if}
      {/foreach}
      {if true == $default}
          
            <p class="revision_description">{$rev.rev_default_description|stripslashes|nl2br}</p>
          
      {/if}
    {/if}
          </div>

          <div class="row mt-4">
            <div class="d-flex justify-content-end">
              <a href="{$rev.u_download}" title="{'Download revision'|translate} {$rev.version}" rel="nofollow">
                <button class="btn btn-tertiary">
                  <i class="icon-download"></i>{'Download this revision'|translate}
                </button>
              </a>
            </div>
          </div>

        </div>
        </div>
      </div> <!-- rev{$rev.id} -->
  {/foreach}
    </div> <!-- changelog -->
{else}
    <p>{'No revisions are available for this extension.'|translate}</p>
{/if}
    
  </section>

{$PEM_EDIT_GENERAL_INFO_FORM}
{$PEM_EDIT_REVISION_FORM}
{$PEM_EDIT_IMAGE_FORM}
{$PEM_EDIT_AUTHORS_FORM}
{$PEM_ADD_LINK_FORM}
{$PEM_EDIT_RELATED_LINK_FORM}
{$PEM_EDIT_SVN_GIT_FORM}
{$PEM_ADD_REVISION_FORM}
{$PEM_DELETE_EXTENSION}
{$PEM_DELETE_REVISION}
{$PEM_DELETE_LINK}
{$PEM_DISPLAY_LANGUAGES}
{$PEM_EDIT_DESCRIPTION_FORM}


</div>

<script>
  var pwg_token = `{$PWG_TOKEN}`;
  var all_revision_languages = {if isset($all_rev_languages_of_ids)}{$all_rev_languages_of_ids}{else}null{/if};
  const VERSIONS_PWG =  {if isset($VERSIONS_PWG)}{json_encode($VERSIONS_PWG)}{else}null{/if};
  const ALL_LANGUAGES = {if isset($all_languages)}{json_encode($all_languages)}{else}null{/if};
  const extensions_languages_ids = {if isset($extensions_languages_ids)}{json_encode($extensions_languages_ids)}{else}null{/if};
</script>

<script src="{$PEM_ROOT_URL_PLUGINS}template/js/single_view.js" require="jquery"></script>

