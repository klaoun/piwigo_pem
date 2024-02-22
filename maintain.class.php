<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

#[AllowDynamicProperties]
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

    $this->table =  $prefixeTable.'pem_'.'categories';

    //add remind every and last-reminder to user_infos to be able to convert existing pem users into piwigo users

    $this->table =  $prefixeTable.'user_infos';
    
    $result = pwg_query('SHOW COLUMNS FROM `'.$this->table.'` LIKE "pem_remind_every";');
    if (!pwg_db_num_rows($result))
    {
      pwg_query('ALTER TABLE `' . $this->table . '` ADD `pem_remind_every` ENUM ("day","week","month")  default "week";');
    }

    $result = pwg_query('SHOW COLUMNS FROM `'.$this->table.'` LIKE "pem_last_reminder";');
    if (!pwg_db_num_rows($result))
    {
      pwg_query('ALTER TABLE `' . $this->table . '` ADD `pem_last_reminder` DATETIME;');
    }

    // Convert pem users to piwigo user, only to be called on first install of PEM plugin with and exisiting database
    if(isset($_GET['convert_users']) and $conf['start_converting_users'] == $_GET['convert_users'])
    {
      include( PHPWG_ROOT_PATH.'plugins/piwigo_pem/include/convert_users.inc.php');
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