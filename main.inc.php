<?php
/*
Plugin Name: piwigo_pem
Version: auto
Description: Piwigo extension manager
Plugin URI: 
Author: HWFord
Author URI: https://github.com/HWFord
Has Settings: false
*/

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

if (basename(dirname(__FILE__)) != 'piwigo_pem')
{
  add_event_handler('init', 'piwigo_pem_error');
  function piwigo_pem_error()
  {
    global $page;
    $page['errors'][] = 'Piwigo extension manager folder name is incorrect, uninstall the plugin and rename it to "piwigo_pem"';
  }
  return;
}

// +-----------------------------------------------------------------------+
// | Define plugin constants                                               |
// +-----------------------------------------------------------------------+

define('PEM_ID', basename(dirname(__FILE__)));
define('PEM_PATH' , PHPWG_PLUGINS_PATH . PEM_ID . '/');
define('PEM_DIR', PHPWG_ROOT_PATH . PWG_LOCAL_DIR . 'piwigo_pem/');

// +-----------------------------------------------------------------------+
// | Add event handlers                                                    |
// +-----------------------------------------------------------------------+

// we put these handlers "before" the test on index page (and the return) because
// whatever the page, we want to execute them

/**
 * add ws_methods
 */
add_event_handler('ws_add_methods', 'pem_ws_add_methods');
include_once(PEM_PATH . 'include/ws_functions.inc.php');

/**
 * plugin initialization
 */

if (script_basename() != 'index') {
  return;
}

add_event_handler('init', 'pem_init');
function pem_init()
{
  include_once(PEM_PATH . 'include/functions_pem.php');
}

/**
 * Load PEM header
 */
add_event_handler('init', 'pem_load_header');
function pem_load_header()
{
  global $template, $page, $lang, $user;

  $pem_root = '';
  $pem_root_url = get_absolute_root_url();
  $pem_root_url_piwigodotorg = get_absolute_root_url() . PEM_PATH;
  $template->set_template_dir(PEM_PATH);
  $template->set_filenames(array('header_pem' => realpath(PEM_PATH .'template/header.tpl')));
  $template->set_filenames(array('navbar_pem' => realpath(PEM_PATH .'template/navbar.tpl')));

  $template->assign(
    array(
      'PEM_ROOT' => $pem_root,
      'PEM_ROOT_URL' => $pem_root_url,
      'PEM_ROOT_URL_PLUGINS' => $pem_root_url_piwigodotorg,
      'URL' => pem_get_page_urls(),
      // 'PEM_DOMAIN_PREFIX' => $page['pem_domain_prefix'],
    )
  );
}

/**
 * Load Pem content
 */
add_event_handler('init', 'pem_load_content');
function pem_load_content(){
  global $template, $logger, $lang, $user, $page, $lang_info;

  $logger->info(__FUNCTION__.', $_GET[pem] = '.(isset($_GET['pem']) ? $_GET['pem'] : 'null'));

  $meta_title = null;
  $meta_description = null;

  $pem_root_url = get_absolute_root_url();
  if (isset($_GET['pem']))
  {
    $pem_page = pem_label_to_page($_GET['pem']);

    if ($pem_page !== false)
    {
      $pem_file = pem_page_to_file($pem_page);
      $tpl_file = PEM_PATH . 'template/' . $pem_file . '.tpl';

      $template->set_filenames(array('pem_page' => realpath($tpl_file)));

      /* Load en_UK translation */
      // if ('en_UK' != $user['language'])
      // {
      //     load_language($pem_file . '.lang', PEM_PATH, array('language' => 'en_UK', 'no_fallback' => true));
      // }
      /* Load user language translation */
      // load_language($pem_file . '.lang', PEM_PATH);

      // $meta_title = pem_get_page_title($pem_page);
      // $meta_description = isset($lang['page_meta_description']) ? $lang['page_meta_description'] : null;

      if (file_exists(PEM_PATH . '/include/' . $pem_file . '.inc.php'))
      {
          include(PEM_PATH . '/include/' . $pem_file . '.inc.php');
      }
    }
    else
    {
      http_response_code(404);
      $template->set_filenames(array('pem_page' => realpath(PEM_PATH . 'template/404.tpl')));
    }
  }
  else
  {
    $template->set_filenames(array('pem_page' => realpath(PEM_PATH . 'template/' . 'home.tpl')));
    if (file_exists(PEM_PATH . '/include/home.inc.php'))
    {
      include(PEM_PATH . '/include/home.inc.php');
    }

  }
  $template->assign(
    array(
        'meta_title' => $meta_title,
        'meta_description' => $meta_description,
        'PEMROOT_URL' => $pem_root_url . PEM_PATH,
        'active_page' => isset($pem_page) ? $pem_page : "home",
    )
  );
}


/**
 * Load Pem footer
 */
add_event_handler('init', 'pem_load_footer');
function pem_load_footer(){
  global $template;

  $porg_root_url = get_absolute_root_url();

  $template->set_filenames(array('footer_pem' => realpath(PEM_PATH .'template/footer.tpl')));

  $template->parse('header_pem');
  $template->parse('navbar_pem');
  $template->parse('pem_page');
  $template->parse('footer_pem');
  $template->p();
  exit();
}