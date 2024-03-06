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

if (!isset($_GET['eid']))
{
  die('missing eid');
}

if (!preg_match('/^\d+$/', $_GET['eid']))
{
  die('expected format for eid');
}

$extension_infos_of = array();

$query = '
SELECT
    id_extension,
    name,
    username
  FROM '.EXT_TABLE.' AS e
    JOIN '.USERS_TABLE.' AS u ON u.id_user = e.idx_user
  WHERE id_extension = '.$_GET['eid'].'
';
$result = pwg_query($query);
while ($row = pwg_db_fetch_assoc($result))
{
  $extension_infos_of[ $row['id_extension'] ] = $row;
}

echo serialize($extension_infos_of);
?>
