<script src="{$PEM_ROOT_URL_PLUGINS}template/js/home.js"></script>

<div class="container" id="home">

  <section>
    <div class="row py-4">
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
    <div class="row py-4" id="{$category.type}">
      <div class="mt-2 row">
        <h2 class="col-10">{$category.name}s<span class="blue-badge extension-count">{$category.counter}</span></h2>
        <div class="col-2 text-end">
          <a href="{$PEM_ROOT}"><button class="btn btn-primary">See all<i class="icon-chevron-right-solid"></i></button></a>
        </div>
      </div>
      <div class="row card-group">
        <div class="col-md-6 col-sm-12 spotlight">
          <h4>Spotlight</h4>
          <div class="col-12">
            <div class="card">
              <div class="row">
                <div class="image-column">
                
                </div>
                <div class="col-12 info-column">
                  
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-sm-12 highest-rated">
          <h4>Highest rated</h4>
          <div class="col-12">
            <div class="card">
              <div class="row">
                <div class="image-column">
                
                </div>
                <div class="col-12 info-column">
                  
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-sm-12 most-downloaded">
          <h4>Most downloaded</h4>
          <div class="col-12">
            <div class="card">
              <div class="row">
                <div class="image-column">
                
                </div>
                <div class="col-12 info-column">
                  
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-sm-12 most-recent">
          <h4>Most recent</h4>
          <div class="col-12">
            <div class="card">
              <div class="row">
                <div class="image-column">
                
                </div>
                <div class="col-12 info-column">
                  
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
{/foreach}
  </div>
</div>

