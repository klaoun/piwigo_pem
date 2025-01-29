<?php
if (isset($_GET['uid']) && 1 == count($_GET))
{

  global $conf, $user; 

  include_once(PEM_PATH . 'include/functions_language.inc.php');

  $current_user_page_id = $_GET['uid'];

  $data = get_user_infos_of(array($current_user_page_id));

  //  If user exists get info
  if (!isset($data[$current_user_page_id]))
  {
    http_response_code(404);
    $template->set_filenames(array('pem_page' => realpath(PEM_PATH . 'template/404.tpl')));

    $template->assign(
      array(
        'MESSAGE' => l10n('Sorry, this user doesn\'t exist.'),
      )
    );
  }
  else
  {
    // For user info modal
    include_once(PEM_PATH . 'include/user/user_mod.inc.php');

    // Assign template for new extension modal
    // Use extension mod because there is a multiple parts that are the same, it saves having the same code twice 
    include_once(PEM_PATH . 'include/extension/extension_mod.inc.php');

    // Specific is the connected user is on their own acocunt page
    if ($user['id'] == $current_user_page_id)
    {
      check_status(ACCESS_CLASSIC);

      // Check if user page displayed is for connected user
      if (isset($user['id']))
      {
        $page['user_can_modify'] = true;
      }
      
      if (!isset($user['pem_remind_every']))
      {
        $user = array_merge(
          $user,
          create_user_infos($user['id'], true)
        );
      }
    }

    // +-----------------------------------------------------------------------+
    // | Page display                                                          |
    // +-----------------------------------------------------------------------+

    $template->set_filename('pem_page', realpath(PEM_PATH . 'template/account.tpl'));

    $template->assign('remind_every', $user['pem_remind_every']);

    // Get owned extensions & other extensions

    $extension_ids = get_extension_ids_for_user($current_user_page_id);

    if (count($extension_ids) > 0)
    {
      // This query is used to get the publish date of extension,
      // By getting the date of first revision
      $query = '
SELECT
      MIN(date) as date,
      idx_extension
  FROM '.PEM_REV_TABLE.'
    WHERE idx_extension IN ('.implode(',',$extension_ids).')
    GROUP BY idx_extension
    ORDER BY date ASC
;';
      $result = pwg_query($query);
      while ($row = pwg_db_fetch_array($result))
      {
        $publish_extension_date_of[ $row['idx_extension'] ] = format_date($row['date']);
        $publish_extension_date_of_NF[ $row['idx_extension'] ] = $row['date'];
        $age[ $row['idx_extension'] ] = time_since($row['date'], 'month', null, false);
      }

      //This query is used to get the date of the extensions last update, 
      $query = '
SELECT
    MAX(date) as date,
    idx_extension
FROM '.PEM_REV_TABLE.'
  WHERE idx_extension IN ('.implode(',',$extension_ids).')
  GROUP BY idx_extension
  ORDER BY date ASC
;';

      $result = pwg_query($query);

      $revision_of = $revision_ids_of = array();
      while ($row = pwg_db_fetch_array($result))
      {
        $last_revision_date_of[ $row['idx_extension'] ] = format_date($row['date'], array('day','month','year'));
      }

      $extension_infos_of = get_extension_infos_of($extension_ids);
      $download_of_extension = get_download_of_extension($extension_ids);
      $category_of_extension = get_categories_of_extension($extension_ids);
      $compatible_version_of_extensions = get_versions_of_extension($extension_ids);

      $query = '
SELECT 
    COUNT(rate) AS total,
    idx_extension
  FROM '.PEM_RATE_TABLE.'
  WHERE idx_extension IN ('.implode(',',$extension_ids).')
  GROUP BY idx_extension
;';
      $total_rates_of_extension = query2array($query, 'idx_extension', 'total');

      foreach ($extension_ids as $extension_id)
      {

        if(isset($extension_infos_of[$extension_id]['name']))
        {
          $extension = array(
            'id' => $extension_id,
            'name' =>htmlspecialchars(strip_tags(stripslashes($extension_infos_of[$extension_id]['name']))),
            'category' => isset($category_of_extension[$extension_id]['default_name']) ? $category_of_extension[$extension_id]['default_name'] : '' ,
            'rating_score' => generate_static_stars($extension_infos_of[$extension_id]['rating_score'],0),
            'rating_score_not_formatted' => $extension_infos_of[$extension_id]['rating_score'] ?? 0,
            'total_rates' =>  isset($total_rates_of_extension[$extension_id]) ? $total_rates_of_extension[$extension_id] : '',
            'nb_reviews' => isset($extension_infos_of[$extension_id]['nb_reviews']) ? $extension_infos_of[$extension_id]['nb_reviews'] : '',
            'nb_downloads' => isset($download_of_extension[$extension_id]) ? $download_of_extension[$extension_id] : '',
            'last_updated'=> isset($last_revision_date_of[$extension_id]) ? $last_revision_date_of[$extension_id] : '',
            'publish_date' => isset($publish_extension_date_of[$extension_id]) ? $publish_extension_date_of[$extension_id] : '',
            'publish_date_not_formatted' =>  isset($publish_extension_date_of_NF[$extension_id]) ? $publish_extension_date_of_NF[$extension_id] : '',
            'age' => isset($age[$extension_id]) ? $age[$extension_id] : '',
            'compatibility_first' => !empty($compatible_version_of_extensions[$extension_id]) ? $compatible_version_of_extensions[$extension_id][0] : '',
            'compatibility_last' => !empty($compatible_version_of_extensions[$extension_id]) ? end($compatible_version_of_extensions[$extension_id]) : '',
          ); 

          $can_see_all_ext = (is_admin($user['id']) || $user['id'] == $current_user_page_id) ? true : false;

          if( $can_see_all_ext && in_array($extension_id, $extension_ids)){
            $extensions[$extension_id] = $extension;
          }
          else if (!$can_see_all_ext && in_array($extension_id, $extension_ids))
          {
            if('' != $extension['publish_date'])
            {
              $extensions[$extension_id] = $extension;
            }
          }
        }
        else
        {
          continue;
        }
      }
      if (!empty($extensions))
      {
        $name  = array_column($extensions, 'name');

        array_multisort($name, SORT_ASC, SORT_NATURAL|SORT_FLAG_CASE, $extensions);
  
        $template->assign('extensions', $extensions);
        $template->assign('extensions_json', json_encode($extensions));
  
      }
    }

    $current_user_page_infos = get_user_infos_of(explode(' ', $current_user_page_id));
    $current_user_page_infos = $current_user_page_infos[$current_user_page_id];

    $registration_date_formatted = format_date($current_user_page_infos['registration_date'], array('month','year'));
    $member_since = time_since($current_user_page_infos['registration_date'], $stop='year');

    // $current_user_page_infos['username'] = $user['username']; 
    $current_user_page_infos['registration_date_formatted'] = $registration_date_formatted;
    $current_user_page_infos['member_since'] = $member_since;

    // Assign user info to tpl
    if(in_array($current_user_page_id , $conf['admin_users'])){
      $current_user_page_infos['group'] = 'Piwigo team <img class="certification_pink" src="'.get_absolute_root_url() . PEM_PATH.'images/CertificationPink.svg"/>';
    }
    else if (in_array($current_user_page_id , $conf['translator_users']))
    {
      $current_user_page_infos['group'] = 'Translator';
    }

    $current_user_page_infos['nb_extensions'] = count($extension_ids);

    $query ='
SELECT 
  `occured_on`
  FROM '.ACTIVITY_TABLE.'
  WHERE object = "user" 
    AND object_id = '.$current_user_page_id.'
  ORDER BY `occured_on` DESC
  LIMIT 1
';

    $result = pwg_db_fetch_row(pwg_query($query));

    if(!is_null($result))
    {
      $current_user_page_infos['last_activity_formatted'] = format_date(strtotime($result[0]));
      $current_user_page_infos['last_activity_since'] = time_since(strtotime($result[0]), $stop ='day');
    }

    $template->assign(
      array(
        'USER' => $current_user_page_infos,
        'can_modify' => isset($page['user_can_modify']) ? $page['user_can_modify'] : false ,
      )
    );


    // Get available languages from Piwigo
    $language_selected = PHPWG_DEFAULT_LANGUAGE;
    foreach (get_languages() as $language_code => $language_name)
    {
      if ($user['language'] == $language_code)
      {
        $language_selected = $language_code;
      }
      $language_options[$language_code] = $language_name;
    }

    $template->assign('language_selected', $language_selected);
    $template->assign('language_options', $language_options);

    // Assign template for edit user info modal
    $template->set_filename('pem_user_edit_info_form', realpath(PEM_PATH . 'template/modals/user_edit_info_form.tpl'));
    $template->assign_var_from_handle('PEM_USER_EDIT_INFO_FORM', 'pem_user_edit_info_form');

    // Assign template for new extension modal
    $template->set_filename('pem_add_ext_form', realpath(PEM_PATH . 'template/modals/add_ext_form.tpl'));
    $template->assign_var_from_handle('PEM_ADD_EXT_FORM', 'pem_add_ext_form');
  }
}
else
{
  http_response_code(404);
  $template->set_filenames(array('pem_page' => realpath(PEM_PATH . 'template/404.tpl')));
}