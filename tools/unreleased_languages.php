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

if (php_sapi_name() != 'cli')
{
  die('this script must be run from cli');
}

define( 'INTERNAL', true );
$root_path = dirname(dirname(__FILE__)).'/';
$_SERVER['REQUEST_URI'] = '';
require_once( $root_path . 'include/common.inc.php' );

$opt = getopt('', array('user:'));

//
// details about languages
//
$info_of_language = array();

$query = '
SELECT
    id_language,
    code,
    name
  FROM '.LANG_TABLE.'
;';
$result = $db->query($query);
while ($row = mysql_fetch_assoc($result)) {
  if (isset($conf['language_english_names'][$row['code']])) {
    $row['english_name'] = $conf['language_english_names'][$row['code']];
  }
  
  $info_of_language[ $row['code'] ] = $row;
}


$query = '
SELECT
    r.idx_extension,
    MAX(r.id_revision) AS id_revision,
    svn_url,
    git_url,
    idx_user,
    name
  FROM '.REV_TABLE.' r
    JOIN '.EXT_CAT_TABLE.' ec ON ec.idx_extension = r.idx_extension
    JOIN '.EXT_TABLE.' ON r.idx_extension = id_extension
  WHERE idx_category IN (10,12)';

if (isset($opt['user']))
{
  $query.= '
    AND idx_user = '.$opt['user'];
}

$query.= '
  GROUP BY idx_extension
  ORDER BY idx_extension DESC
;';

$exts = query2array($query, 'idx_extension');

$versions_of_extension = get_versions_of_extension(array_keys($exts));

// +-----------------------------------------------------------------------+
// | Filter on users                                                       |
// +-----------------------------------------------------------------------+

$maintainers = array();
foreach ($exts as $ext) {
  $maintainers[ $ext['idx_user'] ] = array();
}

if (count($maintainers) > 0)
{
  // print_r(array_keys($maintainers)); exit();
  
  $query = '
SELECT
    idx_user,
    remind_every,
    last_reminder
  FROM '.USER_INFOS_TABLE.'
  WHERE idx_user IN ('.implode(',', array_keys($maintainers)).')
;';
  $infos = query2array($query, 'idx_user');

  foreach (array_keys($maintainers) as $id)
  {
    if (isset($infos[$id]))
    {
      $maintainers[$id] = $infos[$id];
    }
    else
    {
      $maintainers[$id] = create_user_infos($id, true);
    }

    if (empty($maintainers[$id]['last_reminder']))
    {
      $to_notify[$id] = 1;
    }
    else
    {
      // we remove 2 hours, in case of "edge" cases like long execution last time or summer time
      $next_reminder = strtotime($maintainers[$id]['last_reminder'].'+1 '.$maintainers[$id]['remind_every'].' -2 hour');
      
      if ($next_reminder < time())
      {
        $to_notify[$id] = 1;
      }
    }
  }
}

// +-----------------------------------------------------------------------+
// | Filter on versions                                                    |
// +-----------------------------------------------------------------------+

// we only notify user on extensions compatible with Piwigo current version
// or previous version, this way we try to avoid notifying on obsolete
// extensions
$query = '
SELECT
    id_version,
    version
  FROM '.VER_TABLE.'
;';
$versions = query2array($query);
$versions = versort($versions);
$versions = array_slice($versions, -2);

$search_versions = array();
foreach ($versions as $version)
{
  $search_versions[] = $version['version'];
}

// +-----------------------------------------------------------------------+
// | Loop on extensions                                                    |
// +-----------------------------------------------------------------------+

$todo_for_user = array();

foreach ($exts as $ext) {
  if (in_array($ext['idx_extension'], $conf['unreleased_languages_excluded_extensions'])) {
    continue;
  }
  
  if (!isset($to_notify[ $ext['idx_user'] ]))
  {
    echo 'ext #'.$ext['idx_extension'].', user #'.$ext['idx_user'].' already notified recently'."\n";
    continue;
  }

  // check that the extension is compatible with one of the 2 last Piwigo versions
  // print_r($versions_of_extension[ $ext['idx_extension'] ]);
  if (count(array_intersect($search_versions, $versions_of_extension[ $ext['idx_extension'] ])) == 0)
  {
    echo 'ext #'.$ext['idx_extension'].' not compatible with any of versions '.implode('/', $search_versions)."\n";
    continue;
  }
  
  $svn_url = null;

  if (!empty($ext['git_url']) and preg_match('/github/', $ext['git_url'])) {
    $svn_url = $ext['git_url'].'/trunk';
  }
  elseif (!empty($ext['svn_url'])) {
    $svn_url = $ext['svn_url'];
  }
  
  if (!isset($svn_url)) {
    echo 'ext #'.$ext['idx_extension'].' has no svn_url'."\n";
    continue;
  }

  $language_list = array();
  $languages_of = get_languages_of_revision(array($ext['id_revision']));

  if (!isset($languages_of[ $ext['id_revision'] ]))
  {
    // here we consider that if the last revision has not a single language,
    // it means the extension is not designed to be translated
    echo 'ext #'.$ext['idx_extension'].' not designed to be translated'."\n";
    continue;
  }
  
  foreach ($languages_of[ $ext['id_revision'] ] as $language) {
    $language_list[] = $language['code'];
  }

  $language_svn = get_languages_in_svn($svn_url);

  $language_unreleased = array_diff($language_svn, $language_list);

  if (count($language_unreleased) > 0)
  {
    echo "#".$ext['idx_extension']." ";
    echo $svn_url." ";
    echo join(',', $language_unreleased);
    echo "\n";

    if (!isset($todo_for_user[ $ext['idx_user'] ]))
    {
      $todo_for_user[ $ext['idx_user'] ] = array();
    }
    
    $todo_for_user[ $ext['idx_user'] ][$ext['idx_extension']] = array(
      'name' => $ext['name'],
      'unreleased_languages' => $language_unreleased,
      );
    // exit();
    // break;
  }
  else
  {
    echo 'ext #'.$ext['idx_extension'].' no unreleased language'."\n";
  }
}

// print_r($todo_for_user);

$user_infos_of = get_user_infos_of(array_keys($todo_for_user));

$admin_message = '';

$headers = 'From: '.$conf['from']."\n";
$headers.= 'X-Mailer: PEM Mailer'."\n";
$headers.= "MIME-Version: 1.0\n";
$headers.= "Content-type: text/plain; charset=utf-8\n";
$headers.= "Content-Transfer-Encoding: quoted-printable\n";

foreach ($todo_for_user as $user_id => $todo)
{
  $user_infos = $user_infos_of[$user_id];
  // echo '<pre>'; print_r($user_infos_of[$user_id]); echo '</pre>';

  $subject = count($todo).' of your Piwigo extensions have unreleased languages';

  $message = 'Hello '.$user_infos['username'].',

New languages are available for your extensions. Maybe it\'s time to publish some new revisions :-) Don\' forget to use the "Detect languages" action.';

  $i = 1;

  foreach ($todo as $ext_id => $ext)
  {
    $message.= "\n\n".$i.') '.$ext['name'];
    $message.= ' '.quoted_printable_encode('http://piwigo.org/ext/extension_view.php?eid='.$ext_id);
    $message.= "\n".'... has '.count($ext['unreleased_languages']).' new translations';
    $message.= "\n";

    foreach ($ext['unreleased_languages'] as $language_code)
    {
      $language_info = $info_of_language[$language_code];
      $message.= "\n* ".$language_info['name'];
      $message.= ' ('.$language_code.(isset($language_info['english_name']) ? ', '.$language_info['english_name'] : '').')';
    }

    $i++;
  }

  $message.= '

You will receive this reminder email once a '.$user_infos['remind_every'].', you can change this frequency on http://piwigo.org/ext/my.php

Cheers,

--
Piwigo Team';

  $admin_message.= $message;
  
  // echo $message;

  mail($user_infos['email'], $subject, $message, $headers);

  $query = '
UPDATE '.USER_INFOS_TABLE.'
  SET last_reminder = NOW()
  WHERE idx_user = '.$user_id.'
;';
  $db->query($query);
}

$subject = count($todo_for_user).' users notified by unreleased_languages script';
mail('plg@piwigo.org', $subject, $admin_message, $headers);

function get_languages_in_svn($svn_url) {
  global $info_of_language;
  
  $svn_command = 'svn list -rHEAD '.$svn_url.'/language';
  $svn_output = null;
  exec($svn_command, $svn_output);

  $languages_cur = array();
  $language_ids = array();

  foreach ($svn_output as $lang) {
    if (preg_match('#^([a-z]{2,3}_[A-Z]{2,3})#', $lang, $matches)) {
      $languages_cur[] = $matches[1];

      if (isset($info_of_language[ $matches[1] ])) {
        $language_ids[] = $info_of_language[ $matches[1] ]['code'];
      }
    }
  }

  return $language_ids;
}