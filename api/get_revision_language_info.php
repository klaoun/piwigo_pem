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

function ajax_reply()
{
  global $output;
  echo json_encode($output);
  exit();
}

//
// Check input parameters. Either rid or eid+svn.
//
if (isset($_GET['eid'])) {
  if (preg_match('/^\d+$/', $_GET['eid'])) {
    $page['eid'] = $_GET['eid'];
  }
  else {
    die('unexpected eid');
  }
}

if (isset($_GET['svn'])) {
  if (preg_match('/^(\d+|HEAD)$/', $_GET['svn'])) {
    $page['svn'] = $_GET['svn'];
  }
  else {
    die('unexpected svn revision');
  }
}

if (isset($_GET['rid'])) {
  if (preg_match('/^\d+$/', $_GET['rid'])) {
    $page['rid'] = $_GET['rid'];
  }
  else {
    die('unexpected rid');
  }
}

//
// when we edit a revision, we have to find the SVN revision inside the
// archive
//
if (isset($page['rid'])) {
  $query = '
SELECT
    *
  FROM '.PEM_REV_TABLE.'
  WHERE id_revision = '.$page['rid'].'
;';
  $result = $db->query($query);
  while ($row = pwg_db_fetch_assoc($result)) {
    $revision = $row;
  }

  $extract_dir = $conf['local_data_dir'].'/detect_lang/revision-'.$page['rid'];
  exec('mkdir -p '.$extract_dir);

  $archive_path = $root_path.get_revision_src($revision['idx_extension'], $page['rid'], $revision['url']);
  exec('unzip '.$archive_path.' -d '.$extract_dir);
  exec('find '.$extract_dir.' -name pem_metadata.txt | xargs cat | grep ^Revision', $exec_output);
  exec('rm -rf '.$extract_dir);
  
  if (count($exec_output) > 0) {
    if (preg_match('/^Revision:\s*(\d+)/', $exec_output[0], $matches)) {
       $page['svn'] = $matches[1];
    }
  }

  if (!isset($page['svn'])) {
    $output = array(
      'stat' => 'ko',
      'error_message' => 'revision not generated from SVN',
      );
    ajax_reply();
  }

  $page['eid'] = $revision['idx_extension'];
}

//
// make sure we have the required page parameters (whatever the input was
// eid+svn or just rid)
//
$required_params = array('eid', 'svn');
foreach ($required_params as $required_param) {
  if (!isset($page[$required_param])) {
    die('"'.$required_param.'" is a required parameter');
  }
}

//
// find the reference revision_id based on the extension id: the most recent
// revision
//
$page['ref_revision_id'] = null;

$query = '
SELECT
    id_revision
  FROM '.PEM_REV_TABLE.'
  WHERE idx_extension = '.$page['eid'];

if (isset($revision)) {
  $query.= '
    AND date < '.$revision['date'].'
';
}

$query .= '
  ORDER BY date DESC
  LIMIT 1
;';
$result = pwg_query($query);
while ($row = pwg_db_fetch_assoc($result)) {
  $page['ref_revision_id'] = $row['id_revision'];
}

//
// details about languages
//
$info_of_language = array();

$query = '
SELECT
    id_language,
    code,
    name
  FROM '.PEM_LANG_TABLE.'
;';
$result = pwg_query($query);
while ($row = pwg_db_fetch_assoc($result)) {
  if (isset($conf['language_english_names'][$row['code']])) {
    $row['english_name'] = $conf['language_english_names'][$row['code']];
  }
  
  $info_of_language[ $row['code'] ] = $row;
}

// what is the list of languages in reference revision (the previous
// revision, most of the time)
$languages_old = array();

$query = '
SELECT
    code
  FROM '.PEM_REV_LANG_TABLE.'
    JOIN '.PEM_LANG_TABLE.' ON idx_language = id_language
  WHERE idx_revision = '.$page['ref_revision_id'].'
;';
$result = pwg_query($query);
while ($row = pwg_db_fetch_assoc($result)) {
  $languages_old[] = $row['code'];
}

// echo '<pre>'; print_r($languages_old); echo '</pre>';

//
// what is the list of languages in SVN
//
// 1) get the SVN URL
$svn_url = null;

$query = '
SELECT
    svn_url,
    git_url
  FROM '.PEM_EXT_TABLE.'
    JOIN '.PEM_REV_TABLE.' ON id_extension = idx_extension
  WHERE id_revision = '.$page['ref_revision_id'].'
;';
$result = pwg_query($query);
while ($row = pwg_db_fetch_assoc($result)) {
  $svn_url = $row['svn_url'];

  if (isset($row['git_url']) and preg_match('/github/', $row['git_url'])) {
    $svn_url = $row['git_url'].'/trunk';
  }
}

$svn_command = 'svn list -r'.$page['svn'].' '.$svn_url.'/language';
$svn_output = null;
exec($svn_command, $svn_output);

$languages_cur = array();
$language_ids = array();

foreach ($svn_output as $lang) {
  if (preg_match('#^([a-z]{2,3}_[A-Z]{2,3})#', $lang, $matches)) {
    $languages_cur[] = $matches[1];

    if (isset($info_of_language[ $matches[1] ])) {
      $language_ids[] = $info_of_language[ $matches[1] ]['id_language'];
    }
  }
}

//
// new languages
//
$languages_new = array_diff($languages_cur, $languages_old);
// echo '<pre>'; print_r($languages_new); echo '</pre>';

$desc_extra = '';
if (count($languages_new) > 0) {
  $desc_extra = 'New languages:';
}

foreach ($languages_new as $lang) {
  if (isset($info_of_language[$lang])) {
    $desc_extra.= "\n".'* ';
    if (isset($info_of_language[$lang]['english_name'])) {
      $desc_extra.= $info_of_language[$lang]['english_name'].' ('.$info_of_language[$lang]['name'].')';
    }
    else {
      $desc_extra.= $info_of_language[$lang]['name'];
    }
  }
  
  $svn_command = 'svn log '.$svn_url.'/language/'.$lang.' | grep ", thanks to :"';
  $svn_output = null;
  exec($svn_command, $svn_output);

  $translators = array();

  foreach ($svn_output as $svn_output_line) {
    if (preg_match('/, thanks to : (.+)$/', $svn_output_line, $matches)) {
      $translators = array_merge($translators, explode(' & ', $matches[1]));
    }
  }

  if (count($translators) > 0) {
    $desc_extra.= ', thanks to '.implode(', ', array_unique($translators));
  }
}

//
// output
//
$output = array(
  'stat' => 'ok',
  'language_ids' => $language_ids,
  'desc_extra' => $desc_extra,
  );

ajax_reply();
?>