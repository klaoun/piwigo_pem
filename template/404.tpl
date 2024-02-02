<div class="container py-5" id="404">
  <div class="row position-relative">
    <div class="col-6 position-relative">
      <img class="w-100" src="{$PEM_ROOT_URL_PLUGINS}images/404.svg">
    </div>
    <div class="col-6 position-relative">
      <h2 class="vertical-align">
{if isset($MESSAGE)}
        {$MESSAGE}
{else}
      {'Sorry, the page you are looking for doesn\'t exist'|translate}
{/if}
      </h2>
    </div>
  </div>
</div>

<script>
  //allows any filters set in list view to be cleared 
  sessionStorage.clear()
</script>
