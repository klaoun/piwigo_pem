<?php
defined('PEM_PATH') or die('Hacking attempt!');

include_once(PEM_PATH . 'constants.inc.php');

function pem_ws_add_methods($arr)
{
  $service = &$arr[0];

  $service->addMethod(
    'pem.extensions.getList',
    'ws_pem_extensions_getList',
    array(),
    'Get list of extensions.'
  );

  $service->addMethod(
    'pem.extensions.getExtensionInfo',
    'ws_pem_get_extension_info',
    array(
      'extension_id' => array('type'=>WS_TYPE_INT|WS_TYPE_POSITIVE),
    ),
    'Get extension info.'
  );

  $service->addMethod(
    'pem.extensions.getExtensionCount',
    'ws_pem_get_extension_count',
    array(
      'extension_type' => array('info'=>'Language, Theme, Tool, Plugin'),
    ),
    'Get number of extensions depending on type.'
  );

  $service->addMethod(
    'pem.extensions.getExtensionHighestRated',
    'ws_pem_get_extension_highest_rated',
    array(
      'extension_type' => array('info'=>'Language, Theme, Tool, Plugin'),
    ),
    'Get number of extensions depending on type.'
  );

}

/**
 * Get list of extensions
 */
function ws_pem_extensions_getList($params, &$service)
{
  $extension_infos_of = array();

  $query = '
SELECT
    id_extension,
    name,
    username
  FROM '.PEM_EXT_TABLE.' AS e
    JOIN '.PEM_USER_TABLE.' AS u ON u.id_user = e.idx_user
;
';
  $extension_infos_of = query2array($query, 'id_extension');

  if (!isset($_REQUEST['format']))
  {
    //Echo to be compatible with previous version of Piwigo
    echo serialize($extension_infos_of);
    exit;
  }

  return $extension_infos_of;
}

/**
 * Get extension info
 */
function ws_pem_get_extension_info($params, &$service)
{

  if (!isset($params['extension_id']))
  {
    die('missing eid');
  }

  if (!preg_match('/^\d+$/', $params['extension_id']))
  {
    die('expected format for eid');
  }
  
  $extension_infos_of = array();
  
  $query = '
SELECT
    id_extension,
    name,
    username
  FROM '.PEM_EXT_TABLE.' AS e
    JOIN '.PEM_USER_TABLE.' AS u ON u.id_user = e.idx_user
  WHERE id_extension = '.$params['extension_id'].'
;
';

  $extension_infos_of = query2array($query, 'id_extension');

  if (!isset($_REQUEST['format']))
  {
    //Echo to be compatible with previous version of Piwigo
    echo serialize($extension_infos_of);
    exit;
  }

  return $extension_infos_of;  
}

/**
 * Get number of themes, plugins or languages
 */
function ws_pem_get_extension_count($params, &$service)
{
  $category_id;
  switch ($params['extension_type']) 
  {
    case 'Language':
      $category_id = 8;
      break;
    case 'Theme':
      $category_id = 10;
      break;
    case 'Tool':
      $category_id = 11;
      break;
    case 'Plugin':
      $category_id = 12;
      break;
  }

  $query = '
SELECT 
	  COUNT(id_extension)
  FROM piwigo_pem_pem_extensions AS extensions
    LEFT JOIN piwigo_pem_pem_extensions_categories AS categories
 	    ON extensions.id_extension = categories.idx_extension
 	    WHERE categories.idx_category = '.$category_id.'
;
';
}

/**
 * Get highest rated extension per category
 */
function ws_pem_get_extension_highest_rated($params, &$service){
  $query = '
  SELECT 
  id_extension,
  "name",
  description,
  rating_score
  FROM piwigo_pem_pem_extensions AS extensions
  left JOIN piwigo_pem_pem_extensions_categories AS categories
   ON extensions.id_extension = categories.idx_extension
   WHERE categories.idx_category = 12
   ORDER BY rating_score DESC
   LIMIT 1
  ;
  ';
}

