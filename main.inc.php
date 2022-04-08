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


