<link rel="stylesheet" type="text/css" href="{$PEM_ROOT_URL_PLUGINS}styles/css/list_view.css">

{*Start of list_view tpl*}
<div id="list_view" class="container">

{if isset($SPOTLIGHTED)}
  <div class="col-12 py-4 spotlighted">
    <h2>{'Spotlighted'|translate}</h2>
    <div class="col-12 p-3 purple-gradient">
      <div class="row">
        <div class="col-2">
          <div class="image-background position-relative">
  {if isset($CATEGORY.spotlight_extension.screenshot)}
            <img class="img-fluid extension_image position-absolute vertical-horizontal-align" src="{$CATEGORY.spotlight_extension.screenshot}">
  {else}
        <img class="col-md-2" src="{$PEM_DIR}images/image-solid.svg">
        <div class="col-md-8">
  {/if}
          <h3>{$CATEGORY.spotlight_extension.name}</h3>
          <p class="description">{$CATEGORY.spotlight_extension.description}</p>
        </div>
        <div class="col-md-2 col-md-2 d-flex justify-content-end align-items-end">
          <a href="{$PEM_ROOT_URL}index.php?eid={$CATEGORY.spotlight_extension.eid}">{'See'|translate} <i class="icon-chevron-right"></i></a>
        </div>
      </div>
    </div>
  </div>
{/if}

  <div class="d-flex content_header">
    <h4>{"%ss"|translate:$CATEGORY.name}<span class='badge blue-badge'>{$CATEGORY.extension_count}</span>
    </h4>
    <div class="d-flex filter_section">
      <div class="filter_tab mx-2 hover-pointer" onclick="toggleFilter()"><h5>{'Filter'|translate}</h5></div>
      <label for="sort_order">{'Sort order'|translate}</label>
      <select name="sort_order" id="sort_order" class="form-control hover-pointer">
          <option value="date_desc">{'Newest to oldest'|translate}</option>
          <option value="date_asc">{'Oldest to Newest'|translate}</option>
          <option value="a_z">{'A to Z'|translate}</option>
          <option value="z_a">{'Z to A'|translate}</option>
      </select>
      <div class="form-inline  cid-search-form">
        <i class="icon-magnifying-glass"></i>
        <input id="cid-search" class="form-control mr-sm-2" placeholder="{'Search'|translate}" aria-label="Search">
      </div>
    </div>
  </div>

  <div class="col-12 extension_filters">
    <div class="row">

      <div class="col-md-4 form-group version_filter_container">
        <span id="extension_version_select" class="col-12 pb-2">{'Compatible version of Piwigo'|translate}</span>
        <select class="extension_version_select w-100">
          <option selected value="all">{'All versions'}</option>
{foreach from=$VERSIONS item=version}
          <option value="{$version.id_version}">{$version.version}</option>
{/foreach}
        </select>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          <span id="extension_author_select" class="col-12 pb-2">{'Authors'|translate}</span>
          <select class="extension_author_select" data-selectize="authors"
            placeholder="{'Select authors'|translate}"
            data-create="true" name="authors[]" multiple style="width:calc(100% + 2px);">
  {foreach from=$AUTHORS item=author}
              <option value="{$author.uid}">{$author.username}</option>
  {/foreach}
          </select>
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          <span id="extension_tag_select" class="col-12 pb-2">{'Tags'|translate}</span>
          <select class="extension_tag_select" data-selectize="tags"
            placeholder="{'Select tags'|translate}"
            data-create="true" name="tags[]" multiple style="width:calc(100% + 2px);">
{foreach from=$TAGS item=tag}
              <option value="{$tag.tid}">{$tag.name}</option>
{/foreach}
          </select>
        </div>
      </div>

    </div>
  </div>

  <div class="extensions_container">

    <div class="d-flex justify-content-center spinner">
      <div class="spinner-border" role="status"></div>
      <span class="sr-only ms-3 align-middle">{'Loading...'|translate}</span>
    </div>

    <div class="extension_info card" id="jango_fett">
      <div class="row card-body">

        <div class="col col-4 text-center extension_image_container">
          <div class="extension_image_div">
          </div>
        </div>

        <div class="col col-3">
          <h5 class="card-title extension_name my-2"></h5>
          <div class="card-text extension_authors my-2 hide-text-overflow-150  "></div>
          <div class="extension_score my-2"></div>
          <div class="d-flex my-2"><i class="icon-download"></i><p class="card-text extension_number_downloads"></p></div>
        </div>

        <div class="col col-5 extension_description_container">
          <p class="card-text extension_description"></p>
          <a class="more_info_link" href="{$PEM_ROOT_URL}index.php?eid=" >
            <button class="btn btn-primary">{'Discover this %s'|translate:{$CATEGORY.name}}</button>
          </a>
        </div>

      </div>
    </div>

  </div>

  <div class="pagination text-center justify-content-center">
    <a class="align-middle d-none" id="previous_page" href="{$PEM_ROOT_URL}index.php?" ><i class="icon-chevron-left"></i></a>
    <div class="page_buttons align-middle">
    </div>
    <a class="align-middle d-none" id="next_page" href="{$PEM_ROOT_URL}index.php?"><i class="icon-chevron-right"></i></a>
  </div>

</div>

<script>

var PEM_NO_EXTENSIONS =  `{$PEM_NO_EXTENSIONS}`;

var PEM_ROOT_URL = '{$PEM_ROOT_URL}';
var PEM_ROOT_URL_PLUGINS = '{$PEM_ROOT_URL_PLUGINS}';


</script>

<script src="{$PEM_ROOT_URL_PLUGINS}template/js/list_view.js" require="jquery"></script>

