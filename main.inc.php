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
 * Use plugin tpl for identification, register, password
 * Use php provided by Piwigo
 */
if (in_array(script_basename(), array('identification', 'register', 'password')))
{
  add_event_handler('init', 'replace_header_init');
  function replace_header_init()
  {
    global $template;
    $pem_root_url_pem = get_absolute_root_url() . 'plugins/piwigo_pem/';
    
    $template->assign(
      array(
        'PEM_ROOT_URL_PLUGINS' => $pem_root_url_pem,
      )
    );
    $template->smarty->setTemplateDir(PEM_DIR.'template');
  }
}

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


  if (isset($_GET['cid']))
  {
    check_input_parameter('cid',$_GET, false,"/^\\d+$/",true);
    //cid is category ID so display list view of extensions
    include(PEM_PATH . '/include/list_view.inc.php');
  }
  else if (isset($_GET['eid']))
  {
    check_input_parameter('eid',$_GET, false,"/^\\d+$/",true);
    //eid is extension ID so display single view of extension
    include(PEM_PATH . '/include/single_view.inc.php');
  }
  else if (isset($_GET['uid']))
  {
    check_input_parameter('uid',$_GET, false,"/^\\d+$/", true);
    //uid is extension ID so display account
    include(PEM_PATH . '/include/account.inc.php');
  }
  else
  {
    if (count($_GET) > 0)
    {
      $template->set_filenames(array('pem_page' => realpath(PEM_PATH . 'template/' . '404.tpl')));
      if (file_exists(PEM_PATH . '/include/404.inc.php'))
      {
        include(PEM_PATH . '/include/404.inc.php');
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
  }

  $template->assign(
    array(
        'meta_title' => $meta_title,
        'meta_description' => $meta_description,
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

  $template->set_filenames(array('footer_pem' => realpath(PEM_PATH .'template/pem_footer.tpl')));

  $template->parse('header_pem');
  $template->parse('navbar_pem');
  $template->parse('pem_page');
  $template->parse('footer_pem');
  $template->p();
  exit();

}
  