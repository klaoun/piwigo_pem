<?php
// +-----------------------------------------------------------------------+
// | PEM - a PHP based Extension Manager                                   |
// | Copyright (C) 2005-2013 PEM Team - http://piwigo.org                  |
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+

// if a category_id is given, it will say the number of extensions available
// for each version, but all versions are returned.

define('PHPWG_ROOT_PATH','../../');

include_once(PHPWG_ROOT_PATH .'/include/constants.inc.php');
include_once(PHPWG_ROOT_PATH .'/include/common.inc.php');

include_once('../include/constants.inc.php');

$category_id = null;
if (isset($_GET['category_id'])) {
  $category_id = $_GET['category_id'];
  if ($category_id != abs(intval($category_id))) {
    die('unexpected category identifier');
  }
}

$query = '
SELECT
    idx_version,
    COUNT(DISTINCT(r.idx_extension)) AS counter
  FROM '.PEM_COMP_TABLE.' AS c
    JOIN '.PEM_REV_TABLE.' AS r ON r.id_revision = c.idx_revision';
if (isset($category_id)) {
  $query.= '
    JOIN '.PEM_EXT_TABLE.' AS e ON e.id_extension = r.idx_extension
    JOIN '.PEM_EXT_CAT_TABLE.'  AS ec ON ec.idx_extension = e.id_extension
  WHERE idx_category = '.$category_id.'
';
}
$query.= '
  GROUP BY idx_version
;';
$nb_ext_of_version = query2array($query, 'idx_version', 'counter');

$query = '
SELECT
    id_version,
    version
  FROM '.PEM_VER_TABLE.'
;';
$versions = array_reverse(
  versort(
    query2array($query)
    )
  );

$output_versions = array();

foreach ($versions as $version) {
  $id_version = $version['id_version'];
  
  array_push(
    $output_versions,
    array(
      'id' => $id_version,
      'name' => $version['version'],
      'nb_extensions' => isset($nb_ext_of_version[$id_version]) ? $nb_ext_of_version[$id_version] : 0,
      )
    );
}

$format = 'json';
if (isset($_GET['format'])) {
  $format = strtolower($_GET['format']);
}

switch ($format) {
  case 'json' :
    echo json_encode($output_versions);
    break;
  case 'php' :
    echo serialize($output_versions);
    break;
  default :
    echo json_encode($output_versions);
}

// piwigo.org/ext specific : Piwigo send data about hosting details so that
// we can have a few statistics on PHP/MySQL versions. This is useful when
// we plan to change Piwigo code and we wonder if we can use PHP features
// available with PHP 5.2 at least : how many Piwigo users will be
// concerned?
if (isset($_POST['uuid']))
{
  $input_data = array();
  
  foreach ($_POST as $key => $value) {
    $input_data[$key] = urldecode($value);
  }

  $existing_hosting_details = null;
  
  $query = '
SELECT
    id_hosting_details,
    os,
    pwgversion,
    phpversion,
    dbengine,
    dbversion
  FROM pwg_hosting_details
  WHERE uuid = \''.$db->escape($input_data['uuid']).'\'
  ORDER BY created_on DESC
  LIMIT 1
;';
  $result = pwg_query($query);
  while ($row = $db->fetch_assoc($result))
  {
    $existing_hosting_details = $row;
  }

  $insert_or_update = 'insert';
  if (isset($existing_hosting_details))
  {
    $insert_or_update = 'update';

    $compare_keys = array_diff(array_keys($existing_hosting_details), array('id_hosting_details'));
    foreach ($compare_keys as $compare_key)
    {
      if (!isset($input_data[$compare_key]) or $input_data[$compare_key] != $existing_hosting_details[$compare_key])
      {
        $insert_or_update = 'insert';
        break;
      }
    }
  }

  if ('update' == $insert_or_update)
  {
    $query = '
UPDATE pwg_hosting_details
  SET updated_on = NOW()
  WHERE id_hosting_details = '.$existing_hosting_details['id_hosting_details'].'
;';
    pwg_query($query);
  }
  else
  {
    $input_data = $db->escape_array($input_data);
    
    $query = '
INSERT INTO pwg_hosting_details
  SET uuid = \''.$input_data['uuid'].'\',
    created_on = NOW(),
    os = \''.$input_data['os'].'\',
    pwgversion = \''.$input_data['pwgversion'].'\',
    phpversion = \''.$input_data['phpversion'].'\',
    dbengine = \''.$input_data['dbengine'].'\',
    dbversion = \''.$input_data['dbversion'].'\'
;';
    pwg_query($query);
  }
}
?>
