<div id="single_view" class="container">
  <section class="section-fluid">
    <a href="{$PEM_ROOT_URL}index.php?cId={$extension_categories.id_category}&page=1" class="orange-link">
      <i class="icon-chevron-left"></i>Back to {$extension_categories.name}s
    </a>
  </section>

  <section class="mt-4 section-fluid">
  <div class="row">
    <div class="col-md-6">

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
    <div class="col-md-6">
      <img class="img-fluid w-100" src="{$PEM_ROOT_URL_PLUGINS}upload/extension-{$extension_id}/screenshot.jpg">
    </div>
  </div>

  </section>

  <section class="mt-5 pt-3 section-fluid">
    <div class="text-center">
      <div class="p-3 border-right d-inline-block">
        <span><i class="icon-download"></i>{$extension_downloads}</span>
      </div>

      <div class="p-3 border-right d-inline-block">
        {$rate_summary.rating_score}
      </div>

      <div class="p-3 border-right d-inline-block">
        <span><i class="icon-check"></i>Compatible with Piwigo {$latest_compatible_version}</span>
      </div>

      <div class="p-3 d-inline-block">
        <span><i class="icon-rocket"></i>{$first_date}</span>
        <span class='badge blue-badge d-inline'>{$first_date_formatted_since}</span>
      </div>
    </div>
    
  </section>

  <section class="mt-5 pt-3 section-fluid">
    <div>
      <p>{$description}</p>
    </div>
  </section>
</div>
