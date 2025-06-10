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
    ui.pem_remind_every,
    ui.pem_last_reminder,
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

/**
 * Limit user action
 * Checks how many extension in 24H a user has created
 * Checks how many revisions a user has deleted in 24H
 * 
 * This function is implemented to limit hacker actions, they try all different form to fin weaknesses
 * If a user adds more than on extenion and deletes more than 3 revisions per 24H the activity is suspicious
 */

function check_user_activity()
{

  global $user, $logger, $conf;
  //Get current user activity related creating extensions
  $query ='
SELECT 
    count(*)
  FROM '.ACTIVITY_TABLE.'
  WHERE object = "pem_extension" 
    AND action = "add"
    AND performed_by = '.$user['id'].'
    AND occured_on >= NOW() - INTERVAL 1 DAY;
;';

  list($nb_add_ext) = pwg_db_fetch_row(pwg_query($query));

  //Get current user activity related to deleting revisions
  $query ='
SELECT 
    count(*)
  FROM '.ACTIVITY_TABLE.'
  WHERE object = "pem_revision" 
    AND action = "delete"
    AND performed_by = '.$user['id'].'
    AND occured_on >= NOW() - INTERVAL 1 DAY;
;';

  list($nb_del_rev) = pwg_db_fetch_row(pwg_query($query));

  if (!is_admin() and (1 < $nb_add_ext or 3 < $nb_del_rev))
  {
    $logger->info('Suspicious activity in FILE = '.__FILE__.', LINE = '.__LINE__);

    // $country_code = geoip_country_code_by_name($_SERVER['REMOTE_ADDR']);
    // $country_name = geoip_country_name_by_name($_SERVER['REMOTE_ADDR']);

    $country_code = 'unkown';
    $country_name = 'unkown';

    notify_mattermost('['.$conf['mattermost_notif_type'].'] user #'.$user['id'].' ('.$user['username'].') has suspicious PEM activity, IP='.$_SERVER['REMOTE_ADDR'].' country='.$country_code.'/'.$country_name);
    $message = l10n('This action cannot be performed at this time. Please contact an admin.');
    
    exit($message);
  }
}