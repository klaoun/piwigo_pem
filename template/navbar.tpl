<nav class="navbar navbar-expand-lg navbar-light bg-light justify-content-between py-3">
  <div class="container">
    <div class="brand">
      <a class="d-inline-block" href="https://www.piwigo.org">
        <img src="{$PEM_ROOT_URL_PLUGINS}images/porg.svg" alt="piwigo logo in orange and grey">
      </a>
      <a class="d-inline-block fw-bold" id="link-back-porg" href="https://www.piwigo.org">Accueil piwigo.org</a>
    </div>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item fw-bold" id="extensions-link">
          <a class="nav-link" href="{$PEM_ROOT_URL}">Extensions</a>
        </li>
{foreach from=$CATEGORIES_INFO item=category}
        <li class="nav-item active fw-bold">
          <a class="nav-link" href="{$PEM_ROOT_URL}index.php?cId={$category.cId}&page=1"><i class="{$category.icon_class}"></i>{$category.plural_name}</a>
        </li>
{/foreach}
        <li class="nav-item fw-bold" id="account-link">
          <a class="nav-link" href="{$ACCOUNT_URL}"><i class="icon-user"></i>Your account</a>
        </li>
      </ul>
    </div> 
  </div>
</nav>