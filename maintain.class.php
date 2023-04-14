<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

class piwigo_pem_maintain extends PluginMaintain
{
  private $installed = false;

  function __construct($plugin_id)
  {
    parent::__construct($plugin_id);
  }

  /**
   * plugin installation
   */
  function install($plugin_version, &$errors=array())
  {
    include(PHPWG_ROOT_PATH.'admin/include/functions_install.inc.php');
    global $prefixeTable, $conf;

    if (empty($conf['pem_conf']))
    {
      conf_update_param('pem_conf', $this->default_conf, true);
    }
    else
    {
      $old_conf = safe_unserialize($conf['pem_conf']);

      conf_update_param('pem_conf', $old_conf, true);
    }

    // Create tables
    execute_sqlfile(
      PHPWG_ROOT_PATH.'plugins/piwigo_pem/install/pem_structure-mysql.sql',
      'pem_',
      $prefixeTable.'pem_',
      'mysql'
    );

    $this->table =  $prefixeTable.'pem_'.'categories';

    // add icon_class to categories table to make displaying categories easier
    $result = pwg_query('SHOW COLUMNS FROM `'.$this->table.'` LIKE "icon_class";');
    if (!pwg_db_num_rows($result))
    {
      pwg_query('ALTER TABLE `' . $this->table . '` ADD `icon_class` varchar(50);');
    }

    //add remind every and last-reminder to user_infos to ba able to convert existing pem users into piwigo users

    $this->table =  $prefixeTable.'pem_'.'user_infos';
    
    $result = pwg_query('SHOW COLUMNS FROM `'.$this->table.'` LIKE "remind_every";');
    if (!pwg_db_num_rows($result))
    {
      pwg_query('ALTER TABLE `' . $this->table . '` ADD `remind_every` ENUM (`day`,`week`,`month`)  default `week`;');
    }

    $result = pwg_query('SHOW COLUMNS FROM `'.$this->table.'` LIKE "last_reminder";');
    if (!pwg_db_num_rows($result))
    {
      pwg_query('ALTER TABLE `' . $this->table . '` ADD `last_reminder` DATETIME;');
    }
  }

  /**
   * Plugin activation
   */
  function activate($plugin_version, &$errors=array())
  {
  }

  /**
   * Plugin deactivation
   */
  function deactivate()
  {
  }

  /**
   * Plugin (auto)update
   */
  function update($old_version, $new_version, &$errors=array())
  {
    $this->install($new_version, $errors);
  }

    /**
   * Plugin uninstallation
   */
  function uninstall() 
  {
    include(PHPWG_ROOT_PATH.'admin/include/functions_install.inc.php');
    global $prefixeTable, $logger;

    $tables_to_drop = array(
      'authors',
      'categories',
      'categories_translations',
      'download_log',
      'extensions',
      'extensions_categories',
      'extensions_tags',
      'extensions_translations',
      'hosting_details',
      'languages',
      'links',
      'rates',
      'reviews',
      'revisions',
      'revisions_compatibilities',
      'revisions_languages',
      'revisions_translations',
      'tags',
      'tags_translations',
      'user_infos',
      'users',
      'versions',
    );

    foreach ($tables_to_drop as $table)
    {
      $query = 'DROP TABLE '.$prefixeTable.'pem_'.$table.';';
      pwg_query($query);
    }
  }

}