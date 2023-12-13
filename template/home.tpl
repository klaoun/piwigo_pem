<link rel="stylesheet" type="text/css" href="{$PEM_ROOT_URL_PLUGINS}styles/css/home.css">

{*Start of home tpl*}
<div class="container" id="home">

  <section>
    <div class="row py-5">
      <div class="col-6 position-relative">
        <div class="vertical-align ">
          <h1 class="mt-2">Extensions for Piwigo</h1>
          <h5 class="mt-2">Change appearance with themes. Add features with plugins. Extensions require just a few clicks to get installed. 350 extensions available, and growing!</h5>
        </div>
      </div>
      <div class="col-6 ">
        <img src="{$PEM_ROOT_URL_PLUGINS}images/personnalisation.svg">
      </div>
    </div>
  <section>

  <div id="homeSections">
{foreach from=$CATEGORIES item=category}
  {if $category.cid != 8}
    <div class="row py-5" id="category-{$category.cid}">
      <div class="mt-2 row">
        <h2 class="col-10">{$category.name}s<span class="blue-badge extension-count">{$category.nb_extensions}</span></h2>
        <div class="col-2 text-end">
          <a href="{$PEM_ROOT_URL}index.php?cid={$category.cid}&page=1"><button class="btn btn-primary">See all<i class="icon-chevron-right"></i></button></a>
        </div>
      </div>

      <div class="row card-group d-flex align-items-stretch">

        <div class="col-md-6 col-sm-12 align-self-stretch spotlight extension_{$category.spotlight_extension.eid}">
          <h4 class="mt-4">Spotlight</h4>
          <div class="col-12 pb-5 h-100">
            <div class="card h-100">
              <div class="row h-100">
                <div class="col-4 image-column position-relative">
                  <img class="img-fluid extension_image w-100 pe-3 {if isset($category.spotlight_extension.screenshot_class)}{$category.spotlight_extension.screenshot_class}{/if} "
                  src="{$category.spotlight_extension.screenshot_src}">
                </div>
                <div class="col-8 d-flex info-column align-content-between flex-wrap">
                  <div class="col-12">
                    <h5 class='extension-name' >{$category.spotlight_extension.name}</h5>
                    <p class='description'>{$category.spotlight_extension.description}</p>
                  </div>
                  <div class="col-12 d-flex justify-content-end">
                    <a class="white-link link" href="{$PEM_ROOT_URL}index.php?eid={$category.spotlight_extension.eid}">See  <i class="icon-chevron-right"></i></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-md-6 col-sm-12 align-self-stretch highest-rated">
            <h4 class="mt-4">Highest rated</h4>
            <div class="col-12 pb-5 h-100">
              <div class="card h-100">
                <div class="row h-100">
                  <div class="col-4 image-column position-relative">
                    <img class="img-fluid extension_image w-100 pe-3 {if isset($category.highest_rated_extension.screenshot_class)}{$category.highest_rated_extension.screenshot_class}{/if} "
                      src="{$category.highest_rated_extension.screenshot_src}">
                  </div>
                  <div class="col-8 d-flex info-column align-content-between flex-wrap">
                    <div class="col-12">
                      <h5 class='extension-name'>{$category.highest_rated_extension.name}</h5>
                      <p class='description' >{$category.highest_rated_extension.description}</p>
                      <div class='rating_score'>
  {for $foo=1 to $category.highest_rated_extension.rating_score}
                        <i class='icon-star'></i>
  {/for}
                      </div>
                    </div>
                    <div class="col-12 d-flex justify-content-end">
                      <a class="link" href="{$PEM_ROOT_URL}index.php?eid={$category.spotlight_extension.eid}">See  <i class="icon-chevron-right"></i></a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>

        <div class="col-md-6 col-sm-12 align-self-stretch most-downloaded">
            <h4 class="mt-4">Most downloaded</h4>
            <div class="col-12 pb-5 h-100">
              <div class="card h-100">
                <div class="row h-100">
                  <div class="col-4 image-column position-relative">
                    <img class="img-fluid extension_image w-100 pe-3 {if isset($category.most_downloaded_extension.screenshot_class)}{$category.most_downloaded_extension.screenshot_class}{/if} "
                    src="{$category.most_downloaded_extension.screenshot_src}">
                  </div>
                  <div class="col-8 d-flex info-column align-content-between flex-wrap">
                    <div class="col-12">
                      <h5 class='extension-name'>{$category.most_downloaded_extension.name}</h5>
                      <p class='description' >{$category.most_downloaded_extension.description}</p>
                      <div class='number_of_downloads'>
                        <i class='icon-download'>{$category.most_downloaded_extension.download_count}</i>
                      </div>
                    </div>
                    <div class="col-12 d-flex justify-content-end">
                      <a class="link" href="{$PEM_ROOT_URL}index.php?eid={$category.spotlight_extension.eid}">See  <i class="icon-chevron-right"></i></a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>

        <div class="col-md-6 col-sm-12 align-self-stretch most-recent">
            <h4 class="mt-4">Most recent</h4>
            <div class="col-12 pb-5 h-100">
              <div class="card h-100">
                <div class="row h-100">
                  <div class="col-4 image-column position-relative">
                    <img class="img-fluid extension_image w-100 pe-3 {if isset($category.most_recent_extension.screenshot_class)}{$category.most_recent_extension.screenshot_class}{/if} "
                    src="{$category.most_recent_extension.screenshot_src}">
                  </div>
                  <div class="col-8 d-flex info-column align-content-between flex-wrap">
                    <div class="col-12">
                      <h5 class='extension-name'>{$category.most_recent_extension.name}</h5>
                      <p class='description' >{$category.most_recent_extension.description}</p>
                      <div class='lat_updated'>
                        {$category.most_recent_extension.formatted_date}
                        <span class='badge blue-badge'>{$category.most_recent_extension.time_since}</span>
                      </div>
                    </div>
                    <div class="col-12 d-flex justify-content-end">
                      <a class="link" href="{$PEM_ROOT_URL}index.php?eid={$category.spotlight_extension.eid}">See  <i class="icon-chevron-right"></i></a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>

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
