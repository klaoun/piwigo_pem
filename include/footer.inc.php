<?php
global $conf;
/* Include file for FetchRemote */
if (!isset($_GET['refresh_cache'])){
  include(PHPWG_ROOT_PATH . 'admin/include/functions.php');
}

$raw_url = $_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];

$cache_path = $conf['data_location'].PEM_ID.'/porg_footer.cache.php';

// what is the subdomain, if any?
if (preg_match('#([a-z]{2,3})\.piwigo\.org#', $raw_url, $matches))
{
  $subdomain = $matches[1];

}

  if (!is_file($cache_path) or filemtime($cache_path) < strtotime('1 hour ago'))
  {
    fetchRemote("https://".(isset($subdomain)?$subdomain:'')."piwigo.org/ws.php?format=php&method=porg.footer.getTemplate", $result);

    if (mkgetdir(dirname($cache_path)))
    {
      file_put_contents($cache_path, serialize($result));
    }
  }


echo(unserialize(file_get_contents($cache_path)));