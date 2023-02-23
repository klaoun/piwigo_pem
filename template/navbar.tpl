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
        <li class="nav-item active fw-bold">
          <a class="nav-link" href="{$PEM_ROOT}{$URL.home}">Extensions</a>
        </li>
        <li class="nav-item fw-bold">
          <a class="nav-link" href="{$PEM_ROOT}{$URL.plugins}"><i class="icon-puzzle-piece-solid"></i>Plugins</a>
        </li>
        <li class="nav-item fw-bold">
          <a class="nav-link" href="{$PEM_ROOT}{$URL.themes}"><i class="icon-palette-solid"></i>Themes</a>
        </li>
        <li class="nav-item fw-bold">
          <a class="nav-link" href="{$PEM_ROOT}{$URL.tools}"><i class="icon-screwdriver-wrench-solid"></i>Tools</a>
        </li>
        <li class="nav-item fw-bold">
          <a class="nav-link" href="{$PEM_ROOT}{$URL.languages}"><i class="icon-language-solid"></i>Languages</a>
        </li>
        <li class="nav-item fw-bold" id="account-link">
          <a class="nav-link" href="{$PEM_ROOT}{$URL.account}"><i class="icon-user-solid"></i>Your account</a>
        </li>
      </ul>
    </div> 
  </div>
</nav>