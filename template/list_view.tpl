<link rel="stylesheet" type="text/css" href="{$PEM_ROOT_URL_PLUGINS}styles/css/list_view.css">
{* Load selectize *}
<script src="{$PEM_ROOT_URL_PLUGINS}vendor/js/selectize.min.js"></script>
<link rel="stylesheet" type="text/css" href="{$PEM_ROOT_URL_PLUGINS}vendor/css/selectize.clear.css">

{*Start of list_view tpl*}
<div id="list_view" class="container">

{if $SPOTLIGHTED}
  <div class="col-12 py-4 spotlighted">
    <h2>Spotlighted</h2>
    <div class="col-12 p-3 purple-gradient">
      <div class="row">
  {if $CATEGORY.spotlight_extension.screenshot}
        <img class="col-md-2" src="{$CATEGORY.spotlight_extension.screenshot}">
        <div class="col-md-8">
  {else}
        <img class="col-md-2" src="{$PEM_DIR}images/image-solid.svg">
        <div class="col-md-8">
  {/if}
          <h3>{$CATEGORY.spotlight_extension.name}</h3>
          <p class="description">{$CATEGORY.spotlight_extension.description}</p>
        </div>
        <div class="col-md-2 col-md-2 d-flex justify-content-end align-items-end">
          <a href="{$PEM_ROOT_URL}index.php?eid={$CATEGORY.spotlight_extension.eid}">Voir <i class="icon-chevron-right"></i></a>
        </div>
      </div>
    </div>
  </div>
{/if}

  <div class="d-flex content_header">
    <h4>{$CATEGORY.name_plural_EN}
      <span class='badge blue-badge'>{$CATEGORY.extension_count}</span>
    </h4>
    <div class="d-flex filter_section">
      <div class="filter_tab mx-2" onclick="toggleFilter()"><h5 >Filter</h5></div>
      <label for="sort_order">Sort order</label>
      <select name="sort_order" id="sort_order" class="form-control">
          <option value="date_desc">Newest to oldest</option>
          <option value="date_asc">Oldest to Newest</option>
          <option value="a_z">A to Z</option>
          <option value="z_a">Z to A</option>
      </select>
      <form class="form-inline  cid-search-form">
        <i class="icon-magnifying-glass"></i>
        <input id="cid-search" class="form-control mr-sm-2" placeholder="Search" aria-label="Search">
      </form>
    </div>
  </div>

  <div class="col-12 extension_filters">
    <div class="row">

      <div class="col-md-4 form-group version_filter_container">
        <label for="extension_version_select" class="col-12 pb-2">Compatible version of Piwigo</label>
        <select class="extension_version_select w-100">
          <option selected value="all">All versions</option>
{foreach from=$VERSIONS item=version}
          <option value="{$version.id_version}">{$version.version}</option>
{/foreach}
        </select>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          <label for="extension_author_select" class="col-12 pb-2">Authors</label>
          <select class="extension_author_select" data-selectize="authors" data-value="{$author_selection|@json_encode|escape:html}"
            placeholder="{'Type in a search term'|translate}"
            data-create="true" name="authors[]" multiple style="width:calc(100% + 2px);">
  {foreach from=$AUTHORS item=author}
              <option value="{$author.uid}">{$author.username}</option>
  {/foreach}
          </select>
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          <label for="extension_tag_select" class="col-12 pb-2">Tags</label>
          <select class="extension_tag_select" data-selectize="tags" data-value="{$tag_selection|@json_encode|escape:html}"
            placeholder="{'Type in a search term'|translate}"
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
      <span class="sr-only ms-3 align-middle">Loading...</span>
    </div>

    <div class="extension_info card" id="jango_fett">
      <div class="row card-body">

      

        <div class="col col-4 text-center extension_image_container">
          <div class="extension_image_div">
          </div>
        </div>

        <div class="col col-3">
          <h5 class="card-title extension_name my-2"></h5>
          <div class="card-text extension_authors my-2"></div>
          <div class="extension_score my-2"></div>
          <div class="d-flex my-2"><i class="icon-download"></i><p class="card-text extension_number_downloads"></p></div>
        </div>

        <div class="col col-5 extension_description_container">
          <p class="card-text extension_description"></p>
          <a class="more_info_link" href="{$PEM_ROOT_URL}index.php?eid=" >
            <button class="btn btn-primary">Discover this {$CATEGORY.name}</button>
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

