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

/**
 * plugin initialization
 */
add_event_handler('init', 'pem_init');
function pem_init()
{
}

/**
 * add ws_methods
 */
add_event_handler('ws_add_methods', 'pem_ws_add_methods');
include_once(PEM_PATH . 'include/ws_functions.inc.php');

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
      // 'URL' => pem_get_page_urls(),
      // 'PEM_DOMAIN_PREFIX' => $page['pem_domain_prefix'],
    )
  );
}

/**
 * Load Pem content
 */
add_event_handler('init', 'pem_load_content');
function pem_load_content(){
  global $template;
  include_once(PEM_PATH.'include/home.inc.php');
  include_once(PEM_PATH.'include/list_view.inc.php');
}

/**
 * Load Pem footer
 */
add_event_handler('init', 'pem_load_footer');
function pem_load_footer(){
  global $template;
  // echo('<pre>');print_r($template);echo('</pre>');

  $template->set_filenames(array('footer_pem' => realpath(PEM_PATH .'template/footer.tpl')));

  $template->parse('header_pem');
  $template->parse('navbar_pem');
  $template->parse('home_pem');
  // $template->parse('list_view_pem');
  $template->parse('footer_pem');
  $template->p();
  exit();
}