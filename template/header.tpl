<!DOCTYPE html>
<html lang="{$lang_info.code}"  dir="{$lang_info.direction}">
  <head>
    {* Required meta tags *}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    {* Bootstrap CSS *}
    <link rel="stylesheet" type="text/css" href="{$PEM_ROOT_URL_PLUGINS}vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="{$PEM_ROOT_URL_PLUGINS}fonts/open-sans/open-sans.css">

    {* PEM CSS files *}
    <link rel="stylesheet" type="text/css" href="{$PEM_ROOT_URL_PLUGINS}styles/css/main.css">
        <link rel="stylesheet" type="text/css" href="{$PEM_ROOT_URL_PLUGINS}styles/css/navbar.css">

    {* Jquery *}
    <script src="{$PEM_ROOT_URL_PLUGINS}vendor/js/jquery-3.6.0.min.js"></script>

    {* Bootstrap javascript *}
    <script src="{$PEM_ROOT_URL_PLUGINS}vendor/bootstrap/js/bootstrap.min.js"></script>

    {* ChartJs *}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.8.0/chart.min.js" integrity="sha512-sW/w8s4RWTdFFSduOTGtk4isV1+190E/GghVffMA9XczdJ2MDzSzLEubKAs5h0wzgSJOQTRYyaz73L3d6RtJSg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.8.0/helpers.esm.js" integrity="sha512-dSutS1n8KEMUnQMa9YGa6CxAmoUfaZdxL2+s2xBgEq7WHaWdtjna/rzGsjqkT27GxKBDLT0Fr3C/TzzHvBRaAg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light justify-content-between">
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
          <ul class="navbar-nav ">
            <li class="nav-item active fw-bold">
              <a class="nav-link" href="#">Extensions</a>
            </li>
            <li class="nav-item fw-bold">
              <a class="nav-link" href="#">Plugin</a>
            </li>
            <li class="nav-item fw-bold">
              <a class="nav-link" href="#">Thème</a>
            </li>
            <li class="nav-item fw-bold">
              <a class="nav-link" href="#">Outil</a>
            </li>
            <li class="nav-item fw-bold">
              <a class="nav-link" href="#">Langage</a>
            </li>
            <li class="nav-item fw-bold">
              <a class="nav-link" href="#">Votre compte</a>
            </li>
          </ul>
        </div> 
      </div>
    </nav>