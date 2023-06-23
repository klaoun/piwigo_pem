<?php
if (isset($_GET['uid']))
{
  check_status(ACCESS_CLASSIC);

  if (!empty($_POST))
  {
    check_pwg_token();
  }
  
  global $conf; 

  $current_extension_page_id = $_GET['uid'];

  // $template->set_filename('pem_page', realpath(PEM_PATH . 'template/account.tpl'));

  $template->assign(
    array(
    'PEM_PATH' => PEM_PATH,
    )
  );

    
  if (!isset($user['remind_every']))
  {
    $user = array_merge(
      $user,
      create_user_infos($user['id'], true)
    );
  }

  // +-----------------------------------------------------------------------+
  // | Form submission                                                       |
  // +-----------------------------------------------------------------------+

  if (isset($_POST['submit']))
  {
    if (!preg_match('/^(day|week|month)$/', $_POST['remind_every']))
    {
      die("hacking attempt!");
    }

    $query = '
  UPDATE '.USER_INFOS_TABLE.'
    SET remind_every = \''.$_POST['remind_every'].'\'
    WHERE idx_user = '.$user['id'].'
  ;';
  pwg_query($query);

    $user['remind_every'] = $_POST['remind_every'];

    $page['infos'][] = 'parameters saved';
  }

  // +-----------------------------------------------------------------------+
  // | Page display                                                          |
  // +-----------------------------------------------------------------------+

  $template->set_filename('pem_page', realpath(PEM_PATH . 'template/account.tpl'));
  // $tpl->set_filenames(
  //   array(
  //     'page' => 'page.tpl',
  //     'my' => 'my.tpl'
  //   )
  // );

  $template->assign('remind_every', $user['remind_every']);

  // Get owned extensions & other extensions

  $extension_ids = get_extension_ids_for_user($user['id']);

  // Get other extensions
  // $query = '
  // SELECT id_extension
  //   FROM '.PEM_EXT_TABLE.' AS ext
  //   INNER JOIN '.PEM_AUTHORS_TABLE.' AS aut
  //     ON ext.id_extension = aut.idx_extension
  //   WHERE aut.idx_user = \''.$user['id'].'\'
  //   ORDER BY name ASC
  // ;';
  // $other_extension_ids = query2array($query, null, 'id_extension');

  // echo('<pre>');print_r( $other_extension_ids);echo('</pre>');

  // Gets the total information about the extensions
  // $extension_ids = array_merge($other_extension_ids, $my_extension_ids);

  // $extension_ids = $my_extension_ids;

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
      // $revision_of[ $row['idx_extension'] ] = $row['version'];
      // $revision_ids_of[ $row['idx_extension'] ] = $row['id_revision'];
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
      $extension = array(
        'id' => $extension_id,
        'name' => htmlspecialchars(strip_tags($extension_infos_of[$extension_id]['name'])),
        'category' => $category_of_extension[$extension_id]['default_name'],
        'rating_score' => generate_static_stars($extension_infos_of[$extension_id]['rating_score'],0),
        'total_rates' =>  isset($total_rates_of_extension[$extension_id]) ? $total_rates_of_extension[$extension_id] : '',
        'nb_reviews' => isset($extension_infos_of[$extension_id]['nb_reviews']) ? $extension_infos_of[$extension_id]['nb_reviews'] : '',
        'nb_downloads' => isset($download_of_extension[$extension_id]) ? $download_of_extension[$extension_id] : '',
        'last_updated'=> $last_revision_date_of[$extension_id],
        'publish_date' => $publish_extension_date_of[$extension_id],
      );


      if (in_array($extension_id, $extension_ids))
      {
        $template->append('extensions', $extension);
      }
    }
  }

  $registration_date_formatted = format_date($user['registration_date']);
  $member_since = time_since($user['registration_date'], $stop='month');

  $current_user = array();

  $current_user['username'] = $user['username']; 
  $current_user['registration_date_formatted'] = $registration_date_formatted;
  $current_user['member_since'] = $member_since;

  // Assign user info to tpl
  if(in_array($user['id'], $conf['admin_users'])){
    $current_user['group'] = 'Piwigo team <img class="certification_blue" src="'.get_absolute_root_url() . PEM_PATH.'images/CertificationBlue.svg"/>';
  }
  else if (in_array($user['id'], $conf['translators']))
  {
    $current_user['group'] = 'Translator';
  }

  $current_user['nb_extensions'] = count($extension_ids);


  $template->assign('USER' , $current_user);

  // echo('<pre>');print_r($current_user);echo('</pre>');
}
else
{
  http_response_code(404);
  $template->set_filenames(array('pem_page' => realpath(PEM_PATH . 'template/404.tpl')));
}