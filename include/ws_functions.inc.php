<?php
defined('PEM_PATH') or die('Hacking attempt!');

include_once(PEM_PATH . 'include/functions_core.inc.php');
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
