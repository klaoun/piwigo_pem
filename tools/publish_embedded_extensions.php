<?php
// +-----------------------------------------------------------------------+
// | PEM - a PHP based Extension Manager                                   |
// | Copyright (C) 2005-2016 PEM Team - http://piwigo.org                  |
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

if (php_sapi_name() != 'cli')
{
  die('this script must be run from cli');
}

define( 'INTERNAL', true );
$root_path = dirname(dirname(__FILE__)).'/';
$_SERVER['REQUEST_URI'] = '';
require_once($root_path.'include/common.inc.php');

$opt = getopt('', array('ext_id:', 'version_id:', 'release_name:'));

$mandatory_fields = array('version_id', 'release_name');
foreach ($mandatory_fields as $field)
{
  if (!isset($opt[$field]))
  {
    die('missing --'.$field."\n");
  }
}

$conf['publish_url'] = 'https://piwigo.org/ext';
$conf['publish_username'] = 'Piwigo Team';
$conf['publish_password'] = 'qPuUeh0zT';

$cookie_jar = tempnam("/tmp", "COOKIE");
pem_login($cookie_jar);

// publish languages listed in PEM (and which should be in Piwigo release)
$query = '
SELECT
    id_extension,
    svn_url,
    name
  FROM '.EXT_TABLE.'
  WHERE svn_url LIKE \'https://github.com/Piwigo/Piwigo/trunk/language/%\'
';

if (isset($opt['ext_id']))
{
  $query.= '
    AND id_extension = '.$opt['ext_id'].'
';
}

$query.= '
  ORDER BY RAND()
  LIMIT 100
;';
$languages = query2array($query);

foreach ($languages as $language)
{
  $language['svn_url'] = str_replace('trunk', 'tags/'.$opt['release_name'], $language['svn_url']);
  echo 'language #'.$language['id_extension'].' being published...';
  publish_revision($cookie_jar, $language['id_extension'], 'svn', $language['svn_url'], $opt['release_name']);
  echo " done!\n";
}

// publish plugins/themes embedded in Piwigo
$eids = array(
  685,  // elegant
  599,  // smartpocket
  728,  // modus
  720,  // AdminTools
  123,  // LanguageSwitch
  144,  // LocalFilesEditor
  776,  // TakeATour
  );

$query = '
SELECT
    id_extension,
    git_url
  FROM '.EXT_TABLE.'
  WHERE id_extension IN ('.implode(',', $eids).')
;';
$git_url_of = query2array($query, 'id_extension', 'git_url');

foreach ($eids as $eid)
{
  echo 'plugin #'.$eid.' search new languages...';
  $new_languages = get_new_languages($eid);
  echo " done!\n";

  if (!empty($new_languages['desc_extra']))
  {
    $new_languages['desc_extra'] = "\n\n".$new_languages['desc_extra'];
    print $new_languages['desc_extra']."\n\n";
  }
  
  echo 'plugin #'.$eid.' being published...';
  publish_revision(
    $cookie_jar,
    $eid,
    'git',
    $git_url_of[$eid],
    $opt['release_name'],
    $new_languages['desc_extra'],
    $new_languages['language_ids']
    );
  echo " done!\n";
}

unlink($cookie_jar);

function pem_login($cookie_jar)
{
  global $conf;
  
  $url = $conf['publish_url'].'/identification.php';

  $postdata = 'submit=1';
  $postdata.= '&username='.$conf['publish_username'];
  $postdata.= '&password='.$conf['publish_password'];

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_jar);
  curl_setopt($ch, CURLOPT_COOKIEFILE,  $cookie_jar);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  $resultLogin = curl_exec($ch);
  
  //close connection
  curl_close($ch);
}

function publish_revision($cookie_jar, $eid, $file_type, $scm_url, $release_name, $desc_extra=null, $language_ids=null)
{
  global $conf, $opt;

  $url = $conf['publish_url'].'/revision_add.php?eid='.$eid;

  $postfields = array(
    'revision_version' => $release_name,
    'file_type' => $file_type,
    'svn_url' => $scm_url,
    'git_url' => $scm_url,
    'compatible_versions' => array($opt['version_id']),
    'revision_descriptions' => array(5 => 'same as Piwigo '.$release_name),
    'default_description' => 5,
    'submit' => 'Submit',
    );

  if (!empty($language_ids))
  {
    $postfields['extensions_languages'] = $language_ids;
  }

  if (!empty($desc_extra))
  {
    $postfields['revision_descriptions'][5].= $desc_extra;
  }

  // print_r($postfields); exit();

  $postdata = http_build_query($postfields);

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  //set the url, number of POST vars, POST data
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);

  // set cookie
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_jar);
  curl_setopt($ch, CURLOPT_COOKIEFILE,  $cookie_jar);

  //execute post
  $result = curl_exec($ch);
  // echo $result; exit();

  //close connection
  curl_close($ch);
}

function get_new_languages($eid)
{
  global $conf;
  
  $url = $conf['publish_url'].'/api/get_revision_language_info.php?eid='.$eid.'&svn=HEAD';

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  //set the url, number of POST vars, POST data
  curl_setopt($ch, CURLOPT_URL, $url);

  //execute post
  $result = curl_exec($ch);

  //close connection
  curl_close($ch);

  $data = json_decode($result, true);

  return $data;
}
