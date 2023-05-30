<div id="single_view" class="container">

  <section class="section-fluid">
    <a href="{$PEM_ROOT_URL}index.php?cId={$extension_categories.id_category}&page=1" class="orange-link">
      <i class="icon-chevron-left"></i>Back to {$extension_categories.name}s
    </a>
  </section>

  {if $rev.can_modify = true}
  <section  class="mt-4 section-fluid">
    <div class="d-flex justify-content-end">
      <div class="form-check form-switch ">
        <input class="form-check-input" type="checkbox" role="switch" id="edit_mode" >
        <label class="form-check-label" for="edit_mode">Edit mode</label>
      </div>
      <div class="ms-4">
        <a><i class="icon-trash"></i>Delete extension</a>
      </div>
    </div>
  </section>
  {/if}

  <section class="mt-4 section-fluid">
  <div class="row">
    <div class="col-md-6 position-relative">

      <i class="icon-pencil circle-icon edit_mode position-absolute top-0 start-100 translate-middle"></i>

      <div>
        <h2>{$extension_name}</h2>
      </div>

      <div>
{foreach from=$authors item=author}
        <h4 class="author d-inline">{$author}{if !$author@last}, {/if}</h4>
{/foreach}
      </div> 

      <div class="mt-5">
          <h3 class="category ">
            <a href="{$PEM_ROOT_URL}index.php?cId={$extension_categories.id_category}&page=1" class="orange-link">
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
        <a href="{$download_last_url}"><button class="btn btn-primary d-inline">Download</button></a>
        <p class="revision-version d-inline ml-3">Revision {$revisions[0]['version']}</p>
        <span class='badge blue-badge d-inline'>{$last_date_formatted_since}</span>
      </div>

    </div>
    <div class="col-md-6 text-center position-relative">
      <i class="icon-pencil circle-icon edit_mode position-absolute top-0 start-100 translate-middle"></i>

{if $screenshot}
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
      <div class="p-3 border-right d-inline-block">
        <span><i class="icon-download"></i>{$extension_downloads}</span>
      </div>
{if $rate_summary.rating_score}
      <div class="p-3 border-right d-inline-block">
        {$rate_summary.rating_score}
      </div>
{/if}
      <div class="p-3 border-right d-inline-block">
        <span><i class="icon-check"></i>Compatible with Piwigo {$latest_compatible_version}</span>
      </div>

      <div class="p-3 d-inline-block">
        <span><i class="icon-rocket"></i>{$first_date}</span>
        <span class='badge blue-badge d-inline'>{$first_date_formatted_since}</span>
      </div>
    </div>
    
  </section>

{*Description block *}
  <section class="mt-5 pt-3 section-fluid position-relative">
    <i class="icon-pencil circle-icon edit_mode position-absolute top-0 start-100 translate-middle"></i>

    <div>
      <p class="extension_description">{$description}</p>
    </div>
  </section>

{* Links block *}
  <section class="mt-5 pt-3 section-fluid position-relative edit_mode">
    <div class="edit_links">
      <h3 class="mb-3">Related links</h3>

      <div class="my-3">
        <button class="btn btn-tertiary">
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
                {if $link.name|strstr:"Github page" || $link.name|strstr:"Issues"}<i class="icon-github-brands"></i>{/if}
                {$link.name}
              </a>
            </td>
            <td>
              <span class="ms-0 badge blue-badge d-inline">{$link.language}</span>
            </td>
            <td>
              <span class="circle-icon"><i class="icon-pencil translate-middle"></i>Edit</span>
              <span class="circle-icon secondary_action"><i class="icon-trash translate-middle"></i>Delete</span>
            </td>
          </tr>
        {/foreach}

        </tbody>
      </table>
    </div>
  </section>

{if count($links) > 0}
  <section class="mt-5 pt-3 section-fluid related_links">

    <h3 class="mb-3">Related links</h3>
    <div class="d-flex justify-content-start">
  {foreach from=$links item=link}
      <a class="orange-link p-3" href="{$link.url}">
        {if $link.name|strstr:"Github page" || $link.name|strstr:"Issues"}<i class="icon-github-brands"></i>{/if}
        {$link.name}
      </a>
  {/foreach}
    </div>

  </section>
{/if}

{*Revision block *}
  <section class="mt-5 pt-3 section-fluid">
    <h3 class="mb-3">Revisions</h3>

    <div class="edit_mode mt-3 mb-4">
      <button class="btn btn-tertiary">
        <i class="icon-git-alt"></i> SVN & Git configuration
      </button>
      <button class="btn btn-tertiary ms-3">
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
  
        <div
          id="rev{$rev.id}_header" {if $rev.expanded} class="changelogRevisionHeaderExpanded pb-4" {else} class="changelogRevisionHeaderCollapsed pb-0"{/if}
          onclick="revToggleDisplay('rev{$rev.id}_header', 'rev{$rev.id}_content')"
        >
            <div class="revision_title_container d-flex justify-content-between">
              <h4 class="revisionTitle">{'Revision'|@translate} {$rev.version}</h4>
              <div class="">
                <div class="d-inline-block ">
                  <span class="edit_mode circle-icon main_action"><i class="icon-pencil"></i></span>
                  <span class="edit_mode circle-icon secondary_action"><i class="icon-trash"></i></span>
                </div>
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

      {if $rev.can_modify}
          <ul class="revActionLinks">
            <li><a href="{$rev.u_modify}" title="{'Modify revision'|@translate}"><img src="template/images/modify.png" alt="{'Modify revision'|@translate}"></a></li>
            {if !$translator}
            <li><a href="{$rev.u_delete}" onclick="return confirm('{'Are you sure you want to delete this item?'|@translate|escape:javascript}');" title="{'Delete revision'|@translate}">
            <i class="icon-trash"></i>Delete revision</a></li>
            {/if}
          </ul>
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

</div>

<script src="{$PEM_ROOT_URL_PLUGINS}template/js/single_view.js" require="jquery"></script>