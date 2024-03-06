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

/*
 * Retrieve data from external URL.
 *
 * @param string $src
 * @param string|Ressource $dest - can be a file ressource or string
 * @param array $get_data - data added to request url
 * @param array $post_data - data transmitted with POST
 * @param string $user_agent
 * @param int $step (internal use)
 * @return bool 
 */
function fetchRemote($src, &$dest, $get_data=array(), $post_data=array(), $user_agent='Piwigo', $step=0)
{ 
  global $conf;
  
  // Try to retrieve data from local file?
  if (!url_is_remote($src))
  { 
    $content = @file_get_contents($src);
    if ($content !== false)
    {
      is_resource($dest) ? @fwrite($dest, $content) : $dest = $content;
      return true; 
    }  
    else
    { 
      return false;
    }
  }
 
  // After 3 redirections, return false
  if ($step > 3) return false;
 
  // Initialization
  $method  = empty($post_data) ? 'GET' : 'POST';
  $request = empty($post_data) ? '' : http_build_query($post_data, '', '&');
  if (!empty($get_data))
  { 
    $src .= strpos($src, '?') === false ? '?' : '&';
    $src .= http_build_query($get_data, '', '&');
  }
  
  // Initialize $dest
  is_resource($dest) or $dest = '';

  // Try curl to read remote file
  // TODO : remove all these @
  if (function_exists('curl_init') && function_exists('curl_exec'))
  {
    $ch = @curl_init();

    if (isset($conf['use_proxy']) && $conf['use_proxy'])
    { 
      @curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 0);
      @curl_setopt($ch, CURLOPT_PROXY, $conf['proxy_server']);
      if (isset($conf['proxy_auth']) && !empty($conf['proxy_auth']))
      {
        @curl_setopt($ch, CURLOPT_PROXYUSERPWD, $conf['proxy_auth']);
      }  
    } 

    @curl_setopt($ch, CURLOPT_URL, $src);
    @curl_setopt($ch, CURLOPT_HEADER, 1);
    @curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
    @curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if ($method == 'POST')
    {
      @curl_setopt($ch, CURLOPT_POST, 1);
      @curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
    }
    $content = @curl_exec($ch);
    $header_length = @curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $status = @curl_getinfo($ch, CURLINFO_HTTP_CODE);
    @curl_close($ch);
    if ($content !== false and $status >= 200 and $status < 400)
    {
      if (preg_match('/Location:\s+?(.+)/', substr($content, 0, $header_length), $m))
      {
        return fetchRemote($m[1], $dest, array(), array(), $user_agent, $step+1);
      }
      $content = substr($content, $header_length);
      is_resource($dest) ? @fwrite($dest, $content) : $dest = $content;
      return true;
    }
  }

  // Try file_get_contents to read remote file
  if (ini_get('allow_url_fopen'))
  { 
    $opts = array(
      'http' => array(
        'method' => $method,
        'user_agent' => $user_agent,
      )
    );
    if ($method == 'POST')
    {
      $opts['http']['content'] = $request;
    }
    $context = @stream_context_create($opts);
    $content = @file_get_contents($src, false, $context);
    if ($content !== false)
    {
      is_resource($dest) ? @fwrite($dest, $content) : $dest = $content;
      return true;
    }
  }

  // Try fsockopen to read remote file
  $src = parse_url($src);
  $host = $src['host'];
  $path = isset($src['path']) ? $src['path'] : '/';
  $path .= isset($src['query']) ? '?'.$src['query'] : '';

  if (($s = @fsockopen($host,80,$errno,$errstr,5)) === false)
  {
    return false;
  }

  $http_request  = $method." ".$path." HTTP/1.0\r\n";
  $http_request .= "Host: ".$host."\r\n";
  if ($method == 'POST')
  {
    $http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
    $http_request .= "Content-Length: ".strlen($request)."\r\n";
  }
  $http_request .= "User-Agent: ".$user_agent."\r\n";
  $http_request .= "Accept: */*\r\n";
  $http_request .= "\r\n";
  $http_request .= $request;

  fwrite($s, $http_request);

  $i = 0;
  $in_content = false;
  while (!feof($s))
  {
    $line = fgets($s);

    if (rtrim($line,"\r\n") == '' && !$in_content)
    {
      $in_content = true;
      $i++;
      continue;
    }
    if ($i == 0)
    { 
      if (!preg_match('/HTTP\/(\\d\\.\\d)\\s*(\\d+)\\s*(.*)/',rtrim($line,"\r\n"), $m))
      {
        fclose($s);
        return false;
      }
      $status = (integer) $m[2];
      if ($status < 200 || $status >= 400)
      {
        fclose($s);
        return false;
      }
    }
    if (!$in_content)
    {
      if (preg_match('/Location:\s+?(.+)$/',rtrim($line,"\r\n"),$m))
      {
        fclose($s);
        return fetchRemote(trim($m[1]),$dest,array(),array(),$user_agent,$step+1);
      }
      $i++;
      continue;
    }
    is_resource($dest) ? @fwrite($dest, $line) : $dest .= $line;
    $i++;
  }
  fclose($s);
  return true;
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
  $result = pwg_query($query);
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
$git_url = null;
$language_candidates = array();

$query = '
SELECT
    svn_url,
    git_url
  FROM '.PEM_EXT_TABLE.'
    JOIN '.PEM_REV_TABLE.' ON id_extension = idx_extension
  WHERE id_revision = '.$page['ref_revision_id'].'
  LIMIT 1
;';
$result = pwg_query($query);
while ($row = pwg_db_fetch_assoc($result)) {
  if (!empty($row['svn_url']))
  {
    $svn_url = $row['svn_url'];
  }

  if (!empty($row['git_url']) and preg_match('/github/', $row['git_url'])) {
    $git_url = $row['git_url'];
  }
}

if (!empty($svn_url))
{
  $svn_command = 'svn list -r'.$page['svn'].' '.$svn_url.'/language';
  exec($svn_command, $language_candidates);
}

if (!empty($git_url))
{
  // from https://github.com/plegall/Piwigo-check_files_integrity
  // to   https://api.github.com/repos/plegall/Piwigo-check_files_integrity/contents/language
  $github_api_url = str_replace('//github.com', '//api.github.com/repos', $git_url).'/contents/language';
  fetchRemote($github_api_url, $result);
  $language_candidates = array_column(json_decode($result, true), 'name');
}

$languages_cur = array();
$language_ids = array();

foreach ($language_candidates as $lang) {
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
  
  if (!empty($svn_url))
  {
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
