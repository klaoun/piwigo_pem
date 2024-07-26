<link rel="stylesheet" type="text/css" href="{$PEM_ROOT_URL_PLUGINS}styles/css/home.css">

{*Start of home tpl*}
<div class="container" id="home">

  <section id="homeHeader">
    <img class="dotted_line" src="{$PEM_ROOT_URL_PLUGINS}images/dotted_line.svg">
    <div class="row py-5">
      <div class="col-6 position-relative">
        <div class="vertical-align ">
          <h1 class="mt-2">{'Extensions for Piwigo'|translate}</h1>
          <h5 class="mt-2">{'Change appearance with themes. Add features with plugins. Extensions require just a few clicks to get installed. 350 extensions available, and growing!'|translate}</h5>
        </div>
      </div>
      <div class="col-6 ">
        <img class="w-100" src="{$PEM_ROOT_URL_PLUGINS}images/image.png">
      </div>
    </div>
  </section>

  <div id="homeSections">
{foreach from=$CATEGORIES item=category}
  {if $category.cid != 8}
    <div class="row py-5" id="category-{$category.cid}">
      <div class="mt-2 row">
        <h2 class="col-10">{$category.plural_name|translate}<span class="blue-badge badge extension-count">{$category.nb_extensions}</span></h2>
        <div class="col-2 text-end">
          <a href="{$PEM_ROOT_URL}index.php?cid={$category.cid}&page=1"><button class="btn btn-primary">{'See all'|translate} <i class="icon-chevron-right"></i></button></a>
        </div>
      </div>

      <div class="row card-group d-flex align-items-stretch">

      {* Spotlighted *}

        <div class="col-md-6 col-sm-12 align-self-stretch spotlight extension_{$category.spotlight_extension.eid}">
          <h4 class="mt-4">{'Spotlight'|translate}</h4>

          <div class="col-12 pb-5 h-100">
            <div class="card h-100">
              <div class="row h-100">
                <div class="col-5 image-column position-relative">
                  <div class="image-background w-100 h-100 position-relative">
{if isset($category.spotlight_extension.screenshot_src)}
                    <img class="img-fluid extension_image w-100" src="{$category.spotlight_extension.screenshot_src}">
{else}
                    <div class="image-background">
                      <i class="icon-image vertical-align vertical-horizontal-align"></i>
                    </div>
{/if}
                  </div>
                </div>
                <div class="col-7 d-flex info-column align-content-between flex-wrap">
                  <div class="col-12">
                    <a href="{$PEM_ROOT_URL}index.php?eid={$category.spotlight_extension.eid}">
                      <h5 class='extension-name' >{$category.spotlight_extension.name}</h5>
                    </a>
                    <p class='description'>{$category.spotlight_extension.description}</p>
                  </div>
                  <div class="col-12 d-flex justify-content-end">
                    <a class="btn btn-tertiary" href="{$PEM_ROOT_URL}index.php?eid={$category.spotlight_extension.eid}">{'See'|translate} <i class="icon-chevron-right"></i></a>
                  </div>
                </div>
              </div>
            </div>
          </div> 

        </div>

        
        {* Highest rated *}

        <div class="col-md-6 col-sm-12 align-self-stretch highest-rated">

            <h4 class="mt-4">{'Highest rated'|translate}</h4>
            <div class="col-12 pb-5 h-100">
              <div class="card h-100">
                <div class="row h-100">
                <div class="col-5 image-column position-relative">
                  <div class="image-background w-100 h-100 position-relative">
{if isset($category.highest_rated_extension.screenshot_src)}
                    <img class="img-fluid extension_image w-100" src="{$category.highest_rated_extension.screenshot_src}">
{else}
                    <i class="icon-image vertical-align vertical-horizontal-align"></i>
{/if}
                    </div>
                  </div>
                  <div class="col-7 d-flex info-column align-content-between flex-wrap">
                    <div class="col-12">
                      <a href="{$PEM_ROOT_URL}index.php?eid={$category.highest_rated_extension.eid}">
                        <h5 class='extension-name'>{$category.highest_rated_extension.name}</h5>
                      </a>
                      <p class='description' >{$category.highest_rated_extension.description}</p>
                      <div class='rating_score'>
  {for $foo=1 to $category.highest_rated_extension.rating_score}
                        <i class='icon-star'></i>
  {/for}
                      </div>
                    </div>
                    <div class="col-12 d-flex justify-content-end">
                      <a class="btn btn-tertiary" href="{$PEM_ROOT_URL}index.php?eid={$category.highest_rated_extension.eid}">{'See'|translate} <i class="icon-chevron-right"></i></a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>

        
{if $category.cid == 12}
        {* Most downloaded *}

        <div class="col-md-6 col-sm-12 align-self-stretch most-downloaded">
            <h4 class="mt-4">{'Most downloaded'|translate}</h4>
            <div class="col-12 pb-5 h-100">
              <div class="card h-100">
                <div class="row h-100">
                  <div class="col-5 image-column position-relative">
                    <div class="image-background w-100 h-100 position-relative">
  {if isset($category.most_downloaded_extension.screenshot_src)}
                      <img class="img-fluid extension_image w-100" src="{$category.most_downloaded_extension.screenshot_src}">
  {else}
                      <i class="icon-image vertical-align vertical-horizontal-align"></i>               
  {/if}
                    </div>
                  </div>
                  <div class="col-7 d-flex info-column align-content-between flex-wrap">
                    <div class="col-12">
                      <a href="{$PEM_ROOT_URL}index.php?eid={$category.most_downloaded_extension.eid}">
                        <h5 class='extension-name'>{$category.most_downloaded_extension.name}</h5>
                      </a>
                      <p class='description' >{$category.most_downloaded_extension.description}</p>
                      <div class='number_of_downloads'>
                        <i class='icon-download'>{$category.most_downloaded_extension.download_count}</i>
                      </div>
                    </div>
                    <div class="col-12 d-flex justify-content-end">
                      <a class="btn btn-tertiary" href="{$PEM_ROOT_URL}index.php?eid={$category.most_downloaded_extension.eid}">{'See'|translate} <i class="icon-chevron-right"></i></a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        {* Most recent *}
        <div class="col-md-6 col-sm-12 align-self-stretch most-recent">
            <h4 class="mt-4">{'Most recent'|translate}</h4>
            <div class="col-12 pb-5 h-100">
              <div class="card h-100">
                <div class="row h-100">
                  <div class="col-5 image-column position-relative">
                    <div class="image-background w-100 h-100 position-relative">
  {if isset($category.most_recent_extension.screenshot_src)}
                      <img class="img-fluid extension_image w-100" src="{$category.most_recent_extension.screenshot_src}">
  {else}
                      <i class="icon-image position-absolute vertical-horizontal-align"></i>
  {/if}
                    </div>
                  </div>
                  <div class="col-7 d-flex info-column align-content-between flex-wrap">
                    <div class="col-12">
                      <a href="{$PEM_ROOT_URL}index.php?eid={$category.most_downloaded_extension.eid}">
                        <h5 class='extension-name'>{$category.most_recent_extension.name}</h5>
                      </a>
                      <p class='description' >{$category.most_recent_extension.description}</p>
                      <div class='lat_updated'>
                        {$category.most_recent_extension.formatted_date}
                        <span class='badge blue-badge'>{$category.most_recent_extension.time_since}</span>
                      </div>
                    </div>
                    <div class="col-12 d-flex justify-content-end">
                      <a class="btn btn-tertiary" href="{$PEM_ROOT_URL}index.php?eid={$category.most_recent_extension.eid}">{'See'|translate} <i class="icon-chevron-right"></i></a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>
{/if}

      </div>
    </div>
  {/if}
{/foreach}
  </div>
</div>

<script>
  //allows any filters set in list view to be cleared 
  sessionStorage.clear()
</script>
