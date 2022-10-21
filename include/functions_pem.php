<?php
/**
 * list of <page ids> => <language key for page title>. They are the default "porg=xxx" in URLs. We use "-" and not "_".
 */
function pem_get_pages()
{
  return array(
    'home' => 'Piwigo extension manager',
    'extensions' => 'Extensions',
    'plugins' => 'Plugins',
    'themes' => 'Themes',
    'tools' => 'Tools',
    'languages' => 'Langauges',
    'account' => 'Account',
  );
}

/**
 * transforms a page id into a localized label.
 */
function pem_get_page_label($page)
{
  global $lang;

  if (isset($lang['pem_urls'][$page]))
  {
    return $lang['pem_urls'][$page];
  }

  return $page;
}

/**
 * returns the relative URL for a page id. The pattern can be changed with configuration param $conf['pem_url_rewrite']
 */
function pem_get_page_url($page)
{
  global $conf;

  if ('home' == $page)
  {
    return get_gallery_home_url();
  }

  $label = pem_get_page_label($page);

  if (isset($conf['pem_url_rewrite']) and $conf['pem_url_rewrite'])
  {
    $url_prefix = '';

    // when we are on a page such as piwigo.org/guides/install/requirements, the relative URL must be prefixed with ../../
    if (isset($_GET['pem']))
    {
      if (preg_match('/\/+/', $_GET['pem']))
      {
        $url_prefix = str_repeat('../', substr_count(preg_replace('/\/+/', '/', $_GET['pem']), '/'));
      }
    }

    return $url_prefix.$label;
  }

  return 'index.php?pem='.$label;
}

/**
 * converts a page id into the file name. We use "_" instead of "-" in files (include/xxx.inc.php or template/xxx.tpl)
 */
function pem_page_to_file($pem_page)
{
  return str_replace('-', '_', $pem_page);
}

/**
 * list of all urls, used in header/footer (and in the middle of some pages).
 *
 * return associative array 'file id' => 'relative url to page', like 'what_is_piwigo' => 'piwigo-cest-quoi' (FR)
 */
function pem_get_page_urls()
{
  $pem_pages = array_keys(pem_get_pages());

  $pem_page_urls = array();
  foreach ($pem_pages as $pem_page)
  {
    $pem_page_urls[pem_page_to_file($pem_page)] = pem_get_page_url($pem_page);
  }

  return $pem_page_urls;
}

/**
 * list of all page labels
 *
 * return associative array 'page id' => 'page label'
 */
function pem_get_page_labels()
{
  $pem_pages = array_keys(pem_get_pages());

  $pem_page_labels = array();
  foreach ($pem_pages as $pem_page)
  {
    $pem_page_labels[$pem_page] = pem_get_page_label($pem_page);
  }

  return $pem_page_labels;
}

/**
 * returns the page id, based on a label. Returns false if nothing found.
 */
function pem_label_to_page($label)
{
  $pem_page_labels = pem_get_page_labels();
  $flip = array_flip($pem_page_labels);

  if (isset($flip[$label]))
  {
    return $flip[$label];
  }

  return false;
}

function pem_get_page_title($page)
{
  global $lang;

  if (isset($lang['page_meta_title']))
  {
    return $lang['page_meta_title'];
  }

  $pem_pages = pem_get_pages();

  $title = l10n($pem_pages[$page]);
  if ('home' != $page)
  {
    $title.= ' | Piwigo';
  }

  return $title;
}

?>