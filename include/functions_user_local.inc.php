<?php

/**
 * returns basic infos of a specific user or a set of users
 */
function get_user_basic_infos_of($author_ids)
{
  global $conf;

  if (count($author_ids) == 0) {
    return array();
  }

  $user_basic_infos_of = array();
  $query = '
SELECT '.$conf['user_fields']['id'].' AS id,
       '.$conf['user_fields']['username'].' AS username,
       '.$conf['user_fields']['password'].' AS password,
       '.$conf['user_fields']['email'].' AS email
  FROM '.USERS_TABLE.'
  WHERE '.$conf['user_fields']['id'].' IN ('.implode(',', $author_ids).')
;';

  $result = pwg_query($query);

  while ($row = pwg_db_fetch_assoc($result))
  {
    $user_basic_infos_of[ $row['id'] ] = $row;
  }

  return $user_basic_infos_of;
}