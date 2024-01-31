<?php

// +-----------------------------------------------------------------------+
// |                           Form submission                             |
// +-----------------------------------------------------------------------+

if (isset($_POST['pem_action']) and isset($_POST['submit']) and "edit_user_info" == $_POST['pem_action'])
{
  $query = '
SELECT '.$conf['user_fields']['id'].' AS id
  FROM '.USERS_TABLE.'
  WHERE '.$conf['user_fields']['id'].' = '. pwg_db_real_escape_string($_POST['user_id']) .'
;';

  list($author_id) = pwg_db_fetch_array(pwg_query($query));

  if (empty($author_id))
  {
    $page['errors'][] = l10n('This user does not exist in database.');
  }
  else
  {
    $data = array(
      'username'         => pwg_db_real_escape_string($_POST['user_name']),
      'mail_address'     => pwg_db_real_escape_string($_POST['user_email']),
      'id'               => pwg_db_real_escape_string($_POST['user_id']),
    );

    $language = array(
      'user_id'          => pwg_db_real_escape_string($_POST['user_id']),
      'language'         => pwg_db_real_escape_string($_POST['user_language']),
    );

    if(is_numeric($data['id']))
    {   
      single_update(
        USERS_TABLE,
        $data,
        array('id' => $data['id'])
      );

      single_update(
        USER_INFOS_TABLE,
        $language,
        array('user_id' => $language['user_id'])
      );


      $template->assign('language_selected', $language['language']);
      
      $template->assign(
        array(
          'MESSAGE' => 'This user has been succesfully updated.',
          'MESSAGE_TYPE' => 'success'
        )
      );
    }
  }
}
