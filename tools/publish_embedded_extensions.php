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

// php publish_embedded_extensions.php --version_id=86 --release_name=14.4.0

if (php_sapi_name() != 'cli')
{
  die('this script must be run from cli');
}

$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
define ('PHPWG_ROOT_PATH', '../../../');
include(PHPWG_ROOT_PATH.'include/common.inc.php');
include(PHPWG_ROOT_PATH.'admin/include/functions.php');

$query = 'SELECT id_language, code from piwigo_pem_languages;';
$pem_ext_languages = query2array($query, 'code', 'id_language');

$opt = getopt('', array('version_id:', 'release_name:'));

$mandatory_fields = array('version_id', 'release_name');
foreach ($mandatory_fields as $field)
{
  if (!isset($opt[$field]))
  {
    die('missing --'.$field."\n");
  }
}

// check release_name
if (!preg_match('/^\d+\.\d+\.\d+$/', $opt['release_name']))
{
  die('invalid release_name parameter');
}

$unzip_dir = bin2hex(random_bytes(16));
mkdir($unzip_dir);
$tools_dir = getcwd();
chdir($unzip_dir);

$archive_base_url = $conf['publish_url'].'/plugins/piwigo_pem/tools/'.$unzip_dir.'/piwigo/';

$url = 'https://piwigo.org/download/dlcounter.php?code='.$opt['release_name'];
$file_name = 'piwigo-'.$opt['release_name'].'.zip';
    
if (file_put_contents($file_name, file_get_contents($url)))
{
  echo 'Piwigo '.$opt['release_name']." downloaded successfully\n";
}
else
{
  die('Piwigo '.$opt['release_name'].' download failed');
}

exec('unzip '.$file_name);

$cwd = getcwd();

$cookie_jar = tempnam("/tmp", "COOKIE");
pem_login($cookie_jar);
// pem_session_status($cookie_jar);

$exts = array(
  'themes' => array(
    'elegant' => 685,
    'smartpocket' => 599,
    'modus' => 728,
  ),  
  'plugins' => array(
    'AdminTools' => 720,
    'language_switch' => 123,
    'LocalFilesEditor' => 144,
    'TakeATour' => 776,
  ),  
);
$exts = array(); // TODO comment if you want to publish themes/plugins

foreach ($exts as $ext_type => $ext_list)
{
  chdir('piwigo/'.$ext_type);
  foreach ($ext_list as $ext_dir => $eid)
  {
    $language_ids = array();

    $directories = array_filter(glob($ext_dir.'/language/*'), 'is_dir');
    foreach ($directories as $dirpath)
    {
      $dirname = basename($dirpath);
      if (preg_match('/^[a-z]{2,3}_[A-Z]{2}$/', $dirname))
      {
        if (isset($pem_ext_languages[$dirname]))
        {
          $language_ids[] = $pem_ext_languages[$dirname];
        }
        else
        {
          echo $dirpath.' is an unknown language in PEM'."\n";
        }
      }
    }
    // print_r($language_ids);exit();

    $zip_name = $ext_dir.'_'.$opt['release_name'].'.zip';
    exec('zip -r '.$zip_name.' '.$ext_dir);
    echo $zip_name.' created'."\n";

    $archive_url = $archive_base_url.$ext_type.'/'.$zip_name;
    echo '$archive_url = '.$archive_url."\n";

    echo $ext_type.' #'.$eid.' ('.$ext_dir.') being published...';
    publish_revision(
      $cookie_jar,
      $eid,
      $archive_url,
      $opt['release_name'],
      null,
      $language_ids
    );
    echo " done!\n";
  }
  chdir($cwd);
}

// time to deal with languages
chdir('piwigo/language');

$query = '
SELECT
    archive_root_dir,
    id_extension
  FROM piwigo_pem_extensions
    JOIN piwigo_pem_extensions_categories ON idx_extension=id_extension
  WHERE idx_category=8
    AND archive_root_dir IS NOT NULL
    AND archive_root_dir REGEXP \'^[a-z]{2,3}_[a-z]{2}$\'
;';
$piwigo_languages_published = query2array($query, 'id_extension', 'archive_root_dir');
//$piwigo_languages_published = array(); // TODO to comment if you want to publish languages

$ext_type = 'language';

foreach ($piwigo_languages_published as $eid => $language_code)
{
  if (!is_dir($language_code))
  { 
    echo $language_code.' is not in the filesystem'."\n";
    continue;
  }

  $zip_name = $language_code.'_'.$opt['release_name'].'.zip';
  exec('zip -r '.$zip_name.' '.$language_code);
  echo $zip_name.' created'."\n";

  $archive_url = $archive_base_url.$ext_type.'/'.$zip_name;
  echo '$archive_url = '.$archive_url."\n";

  echo $ext_type.' #'.$eid.' ('.$language_code.') being published...';
  publish_revision(
    $cookie_jar,
    $eid,
    $archive_url,
    $opt['release_name']
  );
  echo " done!\n";
}

chdir($tools_dir);
deltree($unzip_dir);
unlink($cookie_jar);

function pem_login($cookie_jar)
{
  global $conf;
  
  $url = $conf['publish_url'].'/ws.php?method=pwg.session.login&format=json';

  $postdata = '&username='.$conf['publish_username'];
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

function pem_session_status($cookie_jar)
{
  global $conf;

  $url = $conf['publish_url'].'/ws.php?method=pwg.session.getStatus&format=json';

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_jar);
  curl_setopt($ch, CURLOPT_COOKIEFILE,  $cookie_jar);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  $resultLogin = curl_exec($ch);
  print_r(json_decode($resultLogin, true)); exit();
  
  //close connection
  curl_close($ch);
}

function publish_revision($cookie_jar, $eid, $file_url, $release_name, $desc_extra=null, $language_ids=null)
{
  global $conf, $opt;

  $url = $conf['publish_url'].'/index.php?eid='.$eid;

  $postfields = array(
    'pem_action' => 'add_revision',
    'revision_version' => $release_name,
    'file_type' => 'url',
    'download_url' => $file_url,
    'compatible_versions' => array($opt['version_id']),
    'revision_descriptions' => array(5 => 'Same as embed in Piwigo '.$release_name),
    'default_description' => 5,
    'submit' => 'Submit',
    );

  if (!empty($language_ids))
  {
    $postfields['revision_languages'] = $language_ids;
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
