<?php
if (isset($_GET['uid']) && 1 == count($_GET))
{

  global $conf; 

  include_once(PEM_PATH . 'include/functions_language.inc.php');

  $current_user_page_id = $_GET['uid'];

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
    
    if (!isset($user['remind_every']))
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

  $template->assign('remind_every', $user['remind_every']);

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
      $last_revision_date_of[ $row['idx_extension'] ] = format_date($row['date']);
    }

    $extension_infos_of = get_extension_infos_of($extension_ids);
    $download_of_extension = get_download_of_extension($extension_ids);
    $category_of_extension = get_categories_of_extension($extension_ids);

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
          'total_rates' =>  isset($total_rates_of_extension[$extension_id]) ? $total_rates_of_extension[$extension_id] : '',
          'nb_reviews' => isset($extension_infos_of[$extension_id]['nb_reviews']) ? $extension_infos_of[$extension_id]['nb_reviews'] : '',
          'nb_downloads' => isset($download_of_extension[$extension_id]) ? $download_of_extension[$extension_id] : '',
          'last_updated'=> isset($last_revision_date_of[$extension_id]) ? $last_revision_date_of[$extension_id] : '',
          'publish_date' => isset($publish_extension_date_of[$extension_id]) ? $publish_extension_date_of[$extension_id] : '',
        );
  
        if (in_array($extension_id, $extension_ids))
        {
          $template->append('extensions', $extension);
        }
      }
      else
      {
        continue;
      }
    }
  }

  $current_user_page_infos = get_user_infos_of(explode(' ', $current_user_page_id));
  $current_user_page_infos = $current_user_page_infos[$current_user_page_id];

  $registration_date_formatted = format_date($current_user_page_infos['registration_date']);
  $member_since = time_since($current_user_page_infos['registration_date'], $stop='month');

  // $current_user_page_infos['username'] = $user['username']; 
  $current_user_page_infos['registration_date_formatted'] = $registration_date_formatted;
  $current_user_page_infos['member_since'] = $member_since;

  // Assign user info to tpl
  if(in_array($current_user_page_id , $conf['admin_users'])){
    $current_user_page_infos['group'] = 'Piwigo team <img class="certification_blue" src="'.get_absolute_root_url() . PEM_PATH.'images/CertificationBlue.svg"/>';
  }
  else if (in_array($current_user_page_id , $conf['translator_users']))
  {
    $current_user_page_infos['group'] = 'Translator';
  }

  $current_user_page_infos['nb_extensions'] = count($extension_ids);

  $template->assign(
    array(
      'USER' => $current_user_page_infos,
      'can_modify' => isset($page['user_can_modify']) ? $page['user_can_modify'] : false ,
    )
  );

  // Assign template for edit user info modal
  $template->set_filename('pem_user_edit_info_form', realpath(PEM_PATH . 'template/modals/user_edit_info_form.tpl'));
  $template->assign_var_from_handle('PEM_USER_EDIT_INFO_FORM', 'pem_user_edit_info_form');

  // Assign template for new extension modal
  $template->set_filename('pem_add_ext_form', realpath(PEM_PATH . 'template/modals/add_ext_form.tpl'));
  $template->assign_var_from_handle('PEM_ADD_EXT_FORM', 'pem_add_ext_form');
  
}
else
{
  http_response_code(404);
  $template->set_filenames(array('pem_page' => realpath(PEM_PATH . 'template/404.tpl')));
}