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
define('PHPWG_ROOT_PATH', '../../../');

include_once(PHPWG_ROOT_PATH .'include/common.inc.php');
include_once('../include/constants.inc.php');


$required_params = array('version');
foreach ($required_params as $required_param) {
  if (!isset($_GET[$required_param])) {
    die('"'.$required_param.'" is a required parameter');
  }
}

$filtered_sets = array();
if (isset($_GET['categories']) or isset($_GET['category_id'])) {
  if (isset($_GET['category_id'])) {
    if ($_GET['category_id'] != abs(intval($_GET['category_id']))) {
      die('unexpected category identifier');
    }
    $categories = $_GET['category_id'];
  }
  
  if (isset($_GET['categories'])) {
    $categories = $_GET['categories'];
  
    if (!preg_match('/^\d+(,\d+)*$/', $categories)) {
      die('unexpected categories identifier');
    }
  }

  $filtered_sets['categories'] = get_extension_ids_for_categories(explode(',', $categories));
}

if (count($filtered_sets) > 0) {
  $page['filtered_extension_ids'] = array_shift($filtered_sets);
  foreach ($filtered_sets as $set) {
    $page['filtered_extension_ids'] = array_intersect(
      $page['filtered_extension_ids'],
      $set
      );
  }

  $page['filtered_extension_ids'] = array_unique(
    $page['filtered_extension_ids']
    );

  $page['filtered_extension_ids_string'] = implode(
    ',',
    $page['filtered_extension_ids']
    );
}

$version = $_GET['version'];
if (!preg_match('/^\d+(,\d+)*$/', $version))
{
  die('wrong parameters for version');
}

if (isset($_GET['extension_include']))
{
  $extension_include = $_GET['extension_include'];
  if (!preg_match('/^\d+(,\d+)*$/', $extension_include))
  {
    die('wrong parameters for extension_include');
  }
}
if (isset($_GET['extension_exclude']))
{
  $extension_exclude = $_GET['extension_exclude'];
  if (!preg_match('/^\d+(,\d+)*$/', $extension_exclude))
  {
    die('wrong parameters for extension_exclude');
  }
}

$_SESSION['language'] = $interface_languages[$conf['default_language']];

if (isset($_GET['lang']))
{
  if (isset($interface_languages[$_GET['lang']]))
  {
    $_SESSION['language'] = $interface_languages[$_GET['lang']];
  }
  elseif (strlen($_GET['lang']) == 2)
  {
    foreach ($interface_languages as $k =>$language)
    {
      if (substr($language['code'], 0, 2) == $_GET['lang'])
      {
        $_SESSION['language'] = $interface_languages[$k];
      }
    }
  }
}

$username_field = $conf['user_fields']['username'];
$userid_field = $conf['user_fields']['id'];

$query = '
SELECT
    a.idx_extension       AS extension_id,
    u.'.$username_field.' AS name
  FROM '.PEM_AUTHORS_TABLE.'     AS a
  INNER JOIN '.USERS_TABLE.' AS u ON a.idx_user = u.'.$userid_field;

if (isset($extension_include))
{
  $query .= '
    WHERE a.idx_extension IN (' . $extension_include . ')';
}
if (isset($extension_exclude))
{
  $query .= '
    WHERE a.idx_extension NOT IN (' . $extension_exclude . ')';
}

$extension_authors = array();
$result = pwg_query($query);
while ($row = pwg_db_fetch_assoc($result))
{
  if (!isset($extension_authors[$row['extension_id']]))
  {
    $extension_authors[$row['extension_id']] = array();
  }
  array_push($extension_authors[$row['extension_id']], $row['name']);
}

$query = '
SELECT DISTINCT
    r.id_revision         AS revision_id,
    r.version             AS revision_name,
    e.id_extension        AS extension_id,
    e.name                AS extension_name,
    e.idx_user            AS author_id,
    e.rating_score        AS rating_score,
    e.description         AS default_extension_description,
    et.description        AS extension_description,
    r.date                AS revision_date,
    r.url                 AS filename,
    r.description         AS default_revision_description,
    rt.description        AS revision_description,
    u.'.$username_field.' AS author_name
  FROM '.PEM_REV_TABLE.' AS r
    INNER JOIN '.PEM_EXT_TABLE.'      AS e  ON e.id_extension = r.idx_extension
    INNER JOIN '.PEM_COMP_TABLE.'     AS c  ON c.idx_revision = r.id_revision
    INNER JOIN '.USERS_TABLE.'    AS u  ON u.'.$userid_field.' = e.idx_user
    LEFT JOIN '.PEM_EXT_TRANS_TABLE.' AS et
      ON et.idx_extension = e.id_extension
      AND et.idx_language = '.$_SESSION['language']['id'].'
    LEFT JOIN '.PEM_REV_TRANS_TABLE.' AS rt
      ON rt.idx_revision = r.id_revision
      AND rt.idx_language = '.$_SESSION['language']['id'].'
  WHERE c.idx_version IN ( ' . $version . ' )';

if (isset($page['filtered_extension_ids'])) {
  if (count($page['filtered_extension_ids']) > 0) {
    $query.= '
    AND e.id_extension IN ('.$page['filtered_extension_ids_string'].')';
  }
  else {
    $query.= '
    AND 0=1';
  }
}

if (isset($extension_include))
{
  $query .= '
    AND e.id_extension IN (' . $extension_include . ')';
}
if (isset($extension_exclude))
{
  $query .= '
    AND e.id_extension NOT IN (' . $extension_exclude . ')';
}

$query .= '
  ORDER BY r.date DESC';

if (isset($_GET['last_revision_only']) and $_GET['last_revision_only'] == 'true')
{
  $query = '
SELECT t.* 
  FROM (' . $query . ') AS t
  GROUP BY t.extension_id';
}

$extension_ids = array();
$revision_ids = array();
$revisions = array();
$result = pwg_query($query);
while ($row = pwg_db_fetch_assoc($result)) {
  $row['revision_date'] = date('Y-m-d H:i:s', $row['revision_date']);
  
  $row['file_url'] = sprintf(
    '%s/%s',
    $conf['website_url'],
    get_revision_src(
      $row['extension_id'],
      $row['revision_id'],
      $row['filename']
      )
    );

  $row['download_url'] = sprintf(
    '%s/download.php?rid=%u',
    $conf['website_url'],
    $row['revision_id']
    );

  if (empty($row['extension_description']))
  {
    $row['extension_description'] = $row['default_extension_description'];
  }
  if (empty($row['revision_description']))
  {
    $row['revision_description'] = $row['default_revision_description'];
  }
  unset($row['default_extension_description']);
  unset($row['default_revision_description']);

  if (isset($extension_authors[$row['extension_id']]))
  {
    $row['author_name'] .= ', ' . implode(', ', $extension_authors[$row['extension_id']]);
  }

  array_push($revisions, $row);
  array_push($extension_ids, $row['extension_id']);
  array_push($revision_ids, $row['revision_id']);
}

if (isset($_GET['get_nb_downloads']) and $_GET['get_nb_downloads'] == 'true')
{
  $download_of_extension = get_download_of_extension($extension_ids);
  $download_of_revision = get_download_of_revision($revision_ids);

  foreach ($revisions as $revision_index => $revision)
  {
    $revisions[$revision_index]['extension_nb_downloads'] = $download_of_extension[ $revision['extension_id'] ];
    $revisions[$revision_index]['revision_nb_downloads'] = $download_of_revision[ $revision['revision_id'] ];
  }
}

// nb_ratings for each extension
$nb_ratings_of_extension = array();

$query = '
SELECT
    idx_extension,
    COUNT(*) AS counter
  FROM '.PEM_RATE_TABLE.' 
  WHERE idx_extension IN ('.implode(',', $extension_ids).')
  GROUP BY idx_extension
;';
$result = pwg_query($query);
while ($row = pwg_db_fetch_assoc($result))
{
  $nb_ratings_of_extension[ $row['idx_extension'] ] = $row['counter'];
}

foreach ($revisions as $revision_index => $revision)
{
  $revisions[$revision_index]['nb_ratings'] = isset($nb_ratings_of_extension[ $revision['extension_id'] ]) ? $nb_ratings_of_extension[ $revision['extension_id'] ] : 0;
} 


/**
 * returns the absolute url to the root of PWG
 * @param boolean with_scheme if false - does not add http://toto.com
 */
function get_absolute_root_url($with_scheme=true)
{
  // TODO - add HERE the possibility to call PWG functions from external scripts
  $url = '';
  if ($with_scheme)
  {
    $is_https = false;
    if (isset($_SERVER['HTTPS']) &&
      ((strtolower($_SERVER['HTTPS']) == 'on') or ($_SERVER['HTTPS'] == 1)))
    {
      $is_https = true;
      $url .= 'https://';
    }
    else
    {
      $url .= 'http://';
    }
    if (isset($_SERVER['HTTP_X_FORWARDED_HOST']))
    {
      $url .= $_SERVER['HTTP_X_FORWARDED_HOST'];
    }
    else
    {
      $url .= $_SERVER['HTTP_HOST'];
      if ( (!$is_https && $_SERVER['SERVER_PORT'] != 80)
            ||($is_https && $_SERVER['SERVER_PORT'] != 443))
      {
        $url_port = ':'.$_SERVER['SERVER_PORT'];
        if (strrchr($url, ':') != $url_port)
        {
          $url .= $url_port;
        }
      }
    }
  }
  $url .= cookie_path();
  return $url;
}

// screenshot, thumbnail, versions and tags
$conf['upload_dir'] = '../'.$conf['upload_dir'];
$pem_url = get_absolute_root_url();

$versions_of = get_versions_of_revision($revision_ids);
$tags_of_extension = get_raw_tags_of_extension($extension_ids);

foreach ($revisions as $revision_index => $revision)
{
  $screenshot_infos = get_extension_screenshot_infos($revision['extension_id']);

  if ($screenshot_infos)
  {
    $revisions[$revision_index]['thumbnail_src'] = $pem_url.str_replace('../', '', $screenshot_infos['thumbnail_src']);
    $revisions[$revision_index]['screenshot_url'] = $pem_url.str_replace('../', '', $screenshot_infos['screenshot_url']);
  }

  $revisions[$revision_index]['compatible_with_versions'] = $versions_of[ $revision['revision_id'] ];
  $revisions[$revision_index]['tags'] = $tags_of_extension[ $revision['extension_id'] ];
}

/**
 * returns tags id of a set of extensions
 */
function get_raw_tags_of_extension($extension_ids) {
 
  $tags_of = array();

  foreach ($extension_ids as $eid) {
    $tags_of[$eid] = array();
  }
  
  $query = '
SELECT idx_extension,
       id_tag,
       name
  FROM '.PEM_TAG_TABLE.' AS t
  LEFT JOIN '.PEM_EXT_TAG_TABLE.' AS et
    ON et.idx_tag = t.id_tag
  WHERE et.idx_extension IN ('.implode(',', $extension_ids).')
;';
  $result = pwg_query($query);
  
  while ($row = pwg_db_fetch_assoc($result)) {
    $tags_of[ $row['idx_extension'] ][ $row['id_tag'] ] = $row['name'];
  }

  return $tags_of;
}

$format = 'json';
if (isset($_GET['format'])) {
  $format = strtolower($_GET['format']);
}

switch ($format) {
  case 'json' :
    echo json_encode($revisions);
    break;
  case 'php' :
    echo serialize($revisions);
    break;
  default :
    echo json_encode($revisions);
}
?>
