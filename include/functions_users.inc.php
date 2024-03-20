<?php
include_once(PEM_PATH . 'include/functions_user_local.inc.php');

/**
 * returns the username of an user or a set of users
 */
function get_author_name($ids)
{

  global $conf;
  if (is_string($ids))
  {
    $authors = array($ids);
  }
  else
  {
    $authors = $ids;
  }
  
  $result = array();
  if(!is_null($authors))
  {
    foreach($authors as $author)
    {
      $user_infos_of = get_user_infos_of(array($author));
  
      if (!empty($conf['user_url_template']))
      {
        $author_string = sprintf(
          $conf['user_url_template'],
          $user_infos_of[$author]['id'],
          $user_infos_of[$author]['username']
          );
      }
      else
      {
        $author_string = $user_infos_of[$author]['username'];
      }
      array_push($result, $author_string);
    }
  }

  if (is_string($ids))
  {
    return $result[0];
  }

  return $result;
}

/**
 * returns all infos of a specific user or a set of users
 */
function get_user_infos_of($user_ids)
{
  if (count($user_ids) == 0) {
    return array();
  }
  
  $user_infos_of = get_user_basic_infos_of($user_ids);


  $query = '
SELECT 
    ui.user_id,
    ui.language,
    ui.registration_date,
    ui.remind_every,
    ui.last_reminder,
    u.username as username
  FROM '.USER_INFOS_TABLE.' as ui
  JOIN '.USERS_TABLE.' AS u ON u.id = ui.user_id
  WHERE user_id IN ('.implode(',', $user_ids).')
;';
  $result = pwg_query($query);

  while ($row = pwg_db_fetch_assoc($result))
  {
    $user_infos_of[ $row['user_id'] ] = array_merge(
      $user_infos_of[ $row['user_id'] ],
      $row
    );
  }

  return $user_infos_of;
}

/**
 * checks if the user is translator
 */
function isTranslator($user_id)
{
  global $conf;

  return isset($conf['translator_users'][$user_id]);
}