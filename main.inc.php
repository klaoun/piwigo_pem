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
define('PEM_DIR', PHPWG_ROOT_PATH . 'plugins/piwigo_pem/');

include_once(PEM_PATH . 'include/constants.inc.php');
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

// adapt language depending on url
add_event_handler('user_init', 'pem_user_init');
function pem_user_init()
{
  global $user, $page;
  $page['pem_domain_prefix'] = '';
  //Set user language to 'en_GB'
  $user['language'] = 'en_GB';

}

//Init pem functions
add_event_handler('init', 'pem_init');
function pem_init()
{
  include_once(PEM_PATH . 'include/functions_pem.php');
  require_once(PEM_PATH . 'include/config_default.inc.php');

  //Load languages
  /* Load en_GB translation */
  load_language('plugin.lang', PEM_PATH, array('language' => 'en_GB', 'no_fallback' => true));
  /* Load user language translation */
  load_language('plugin.lang', PEM_PATH);

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
  $pem_root_url_pem = get_absolute_root_url() . PEM_PATH;
  $template->set_template_dir(PEM_PATH);
  $template->set_filenames(array('header_pem' => realpath(PEM_PATH .'template/header.tpl')));
  include(PEM_PATH . '/include/navbar.inc.php');


  $template->assign(
    array(
      'PEM_ROOT' => $pem_root,
      'PEM_ROOT_URL' => $pem_root_url,
      'PEM_ROOT_URL_PLUGINS' => $pem_root_url_pem,
      'URL' => pem_get_page_urls(),
      'PEM_DOMAIN_PREFIX' => $page['pem_domain_prefix'],
    )
  );
}

/**
 * Load Pem content
 */
add_event_handler('init', 'pem_load_content');
function pem_load_content(){
  global $template, $lang, $user, $page, $lang_info;

  $meta_title = null;
  $meta_description = null;

  $pem_root_url = get_absolute_root_url();

  if (isset($_GET['cId']))
  {
    check_input_parameter('cId',$_GET, false,"/^\\d+$/");

    //cId is category ID so display list view of extensions
    include(PEM_PATH . '/include/list_view.inc.php');
  }
  else if (isset($_GET['eId']))
  {
    check_input_parameter('eId',$_GET, false,"/^\\d+$/", true);

    //eId is extension ID so display single view of extension
    include(PEM_PATH . '/include/single_view.inc.php');
  }
  else if (isset($_GET['uId']))
  {
    check_input_parameter('uId',$_GET, false,"/^\\d+$/", true);
    //uId is extension ID so display single view of extension
    include(PEM_PATH . '/include/account.inc.php');
  }
  else
  {
    $template->set_filenames(array('pem_page' => realpath(PEM_PATH . 'template/' . 'home.tpl')));
    if (file_exists(PEM_PATH . '/include/home.inc.php'))
    {
      include(PEM_PATH . '/include/home.inc.php');
    }

  }
  $pem_root_url_pem = get_absolute_root_url() . PEM_PATH;

  $template->assign(
    array(
        'meta_title' => $meta_title,
        'meta_description' => $meta_description,
        'PEMROOT_URL' => $pem_root_url . PEM_PATH,
        'active_page' => isset($pem_page) ? $pem_page : "home",
        'PEM_ROOT_URL_PLUGINS' => $pem_root_url_pem,
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

/**
 * Change identification tpl
 */
add_event_handler('loc_end_identification', 'pem_loc_end_identification');
function pem_loc_end_identification()
{
  global $template;
  $template->set_filenames( array('identification' => realpath(PEM_PATH .'template/identification.tpl')));
}

/**
 * Change register tpl
 */
add_event_handler('loc_end_register', 'pem_loc_end_register');
function pem_loc_end_register()
{
  global $template;
  $template->set_filenames( array('register' => realpath(PEM_PATH .'template/register.tpl')));
}

/**
 * Change password tpl
 */
add_event_handler('loc_end_password', 'pem_loc_end_password');
function pem_loc_end_password()
{
  global $template;
  $template->set_filenames( array('password' => realpath(PEM_PATH .'template/password.tpl')));
}
