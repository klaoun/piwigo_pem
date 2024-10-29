<?php
// +-----------------------------------------------------------------------+
// | PEM - a PHP based Extension Manager                                   |
// | Copyright (C) 2005-2017 PEM Team - http://piwigo.org                  |
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

# mark revisions as compatible with version X:
# * if belong to $conf['categories']
# * if compatible with version Y
# * if no revision of the same extensions is already compatible with version X
# * if author is not in the $conf['exclude_authors'] array

$conf['mark_compatible_with'] = 79; // version 2.9
$conf['already_compatible_with'] = 78; // version 2.8
$conf['exclude_authors'] = array(3572);
$conf['categories'] = array(10, 11, 12); // themes, tools, plugins

$extension_ids = get_extension_ids_for_categories($conf['categories'], 'or');

// filter on extension not already compatible with Y
$versions_of_extension = get_version_ids_of_extension($extension_ids);
foreach ($versions_of_extension as $eid => $version_ids)
{
  // is it already compatible with Y?
  if (in_array($conf['mark_compatible_with'], $version_ids))
  {
    $extension_ids = array_diff($extension_ids, array($eid));
  }
}

// exclude extensions of specific authors who do not want this automatic compatibility
$exclude_ext_ids = array();
foreach ($conf['exclude_authors'] as $user_id)
{
  $exclude_ext_ids = array_merge($exclude_ext_ids, get_extension_ids_for_user($user_id));
}
$extension_ids = array_diff($extension_ids, $exclude_ext_ids);

echo 'count = '.count($extension_ids)."\n";

// find all revisions compatible with Y
$query = '
SELECT
    id_extension,
    MAX(idx_revision) AS rev
  FROM '.COMP_TABLE.'
    JOIN '.REV_TABLE.'
      ON id_revision = idx_revision
    JOIN '.EXT_TABLE.'
      ON id_extension = idx_extension
  WHERE idx_version = '.$conf['already_compatible_with'].'
    AND id_extension IN ('.implode(',', $extension_ids).')
  GROUP BY id_extension
';
$last_rev_of = query2array($query, 'id_extension', 'rev');

$inserts = array();
foreach ($last_rev_of as $eid => $rid)
{
  $inserts[] = array(
    'idx_version' => $conf['mark_compatible_with'],
    'idx_revision' => $rid,
    );
}

echo 'count($inserts) = '.count($inserts)."\n";

if (count($inserts) > 0)
{
  mass_inserts(
    COMP_TABLE,
    array_keys($inserts[0]),
    $inserts
    );
}
