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
  {if $category.cId != 8}
    <div class="row py-5" id="category-{$category.cId}">
      <div class="mt-2 row">
        <h2 class="col-10">{$category.name}s<span class="blue-badge extension-count">{$category.nb_extensions}</span></h2>
        <div class="col-2 text-end">
          <a href="{$PEM_ROOT_URL}index.php?cId={$category.cId}&page=1"><button class="btn btn-primary">See all<i class="icon-chevron-right"></i></button></a>
        </div>
      </div>
      <div class="row card-group">
        <div class="col-md-6 col-sm-12 align-items-stretch spotlight">
          <h4>Spotlight</h4>
          <div class="col-12">
            <div class="card align-items-stretch">
              <div class="row">
                <div class="image-column">
                
                </div>
                <div class="col-12 info-column">
                  <h5 class='extension-name' >{$category.spotlight_extension.name}</h5>
                  <p class='description'>{$category.spotlight_extension.description}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-sm-12 align-items-stretch highest-rated">
          <h4>Highest rated</h4>
          <div class="col-12">
            <div class="card align-items-stretch">
              <div class="row">
                <div class="image-column">

                </div>
                <div class="col-12 info-column">
                  <h5 class='extension-name'>{$category.highest_rated_extension.name}</h5>
                  <p class='description' >{$category.highest_rated_extension.description}</p>
                  <div class='rating_score'>
  {for $foo=1 to $category.highest_rated_extension.rating_score}
                    <i class='icon-star'></i>
  {/for}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-sm-12 align-items-stretch most-downloaded">
          <h4>Most downloaded</h4>
          <div class="col-12">
            <div class="card align-items-stretch">
              <div class="row">
                <div class="image-column">
                
                </div>
                <div class="col-12 info-column">
                    <h5 class='extension-name'>{$category.most_downloaded_extension.name}</h5>
                    <p class='description' >{$category.most_downloaded_extension.description}</p>
                  <div class='number_of_downloads'>
                     <i class='icon-download'>{$category.most_downloaded_extension.download_count}</i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-sm-12 align-items-stretch most-recent">
          <h4>Most recent</h4>
          <div class="col-12">
            <div class="card align-items-stretch">
              <div class="row">
                <div class="image-column">
                
                </div>
                <div class="col-12 info-column">
                  <h5 class='extension-name'>{$category.most_recent_extension.name}</h5>
                  <p class='description' >{$category.most_recent_extension.description}</p>
                  <div class='lat_updated'>
                    {$category.most_recent_extension.formatted_date}
                    <span class='badge blue-badge'>{$category.most_recent_extension.time_since}</span>
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

