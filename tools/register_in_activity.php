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

$opt = getopt('', array('dry-run'));

$query = '
SELECT
    url,
    project_id
  FROM activity_timeline.project
';
$acts = query2array($query, 'url', 'project_id');

// print_r($acts);

// list extensions having at least 2 revisions in the last year (we do not want to list unactive projects)
$query = '
SELECT
    idx_extension
  FROM '.REV_TABLE.'
  WHERE date > UNIX_TIMESTAMP(subdate(now(), interval 1 year))
  GROUP BY idx_extension
  HAVING COUNT(*) > 1
;';
$ext_ids = query2array($query, null, 'idx_extension');

$query = '
SELECT
    name,
    svn_url,
    git_url
  FROM '.EXT_TABLE.'
  WHERE id_extension IN ('.implode(',', $ext_ids).')
    AND (svn_url IS NOT NULL OR git_url IS NOT NULL)
';
$exts = query2array($query);

foreach ($exts as $ext)
{
  $url = $ext['git_url'];
  if (isset($ext['svn_url']))
  {
    if (preg_match('{^https://github.com/Piwigo/Piwigo/}', $ext['svn_url']))
    {
      continue;
    }
    $url = $ext['svn_url'];
  }

  // remove trailing ".git"
  $url = preg_replace('/\.git$/', '', $url);

  if (!isset($acts[$url]))
  {
    echo '['.$ext['name'].'] '.$url.' is not registered yet'."\n";

    if (!isset($opt['dry-run']))
    {
      $query = '
INSERT
  INTO activity_timeline.project
  SET name = \''.$db->escape($ext['name']).'\',
    url = \''.$url.'\'
;';
      $db->query($query);
    }
  }
}
