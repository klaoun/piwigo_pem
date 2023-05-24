<?php
if (isset($_GET['eId']))
{
  global $conf, $user;

  include_once(PEM_PATH . 'include/functions_language.inc.php');

  $current_extension_page_id = $_GET['eId'];

  // Get current language id, transition to PEM plugin means that language id is the language code in the old PEM database
  // Using this we get the number id from PEM_LANG_TABLE
  $query = '
  SELECT 
    id_language
    FROM '.PEM_LANG_TABLE.'
      WHERE code = "'.$user['language'].'"
  ';
  $result = query2array($query);
  $id_language = $result[0]['id_language'];

  //Using old PEM functions
  // define( 'INTERNAL', true );
  // $root_path = './';
  // require_once( $root_path . 'include/common.inc.php' );
    
  // $page['extension_id'] = isset($_GET['eid']) ? abs(intval($_GET['eid'])) : null;
  // if (!isset($page['extension_id']))
  // {
  //   message_die('eid URL parameter is missing', 'Error', false );
  // }
  
  $self_url = PEM_PATH.'index.php?eid='.$current_extension_page_id;

  // $template->set_filenames(
  //   array(
  //     'page' => 'page.tpl',
  //     'extension_view' => 'extension_view.tpl'
  //   )
  // );

  // actions
  if (isset($_GET['action']))
  {
    switch ($_GET['action'])
    {
      case 'rate':
        rate_extension($page['extension_id'], $_POST['score']);
        header('Location: '.$self_url);
        break;
      
      case 'add_review':
        if (empty($_POST)) break;
        
        $user_review = array(
          'idx_extension' => $page['extension_id'],
          'author' => trim($_POST['author']),
          'email' => trim($_POST['email']),
          'title' => trim($_POST['title']),
          'content' => $_POST['content'],
          'rate' => (float)@$_POST['score'],
          'idx_language' => $_POST['idx_language'],
          );
        
        insert_user_review($user_review);
        switch ($user_review['action'])
        {
          case 'validate':
            unset($user_review);
            $user_review['action'] = 'validate';
            $user_review['message'] = l10n('Thank you!');
            break;
            
          case 'moderate':
            unset($user_review);
            $user_review['action'] = 'moderate';
            $user_review['message'] = l10n('Thank you! Your review is awaiting moderation.');
            break;
            
          case 'reject':
            $user_review['display'] = true;
            break;
        }
        
        break;
        
      case 'edit_review':
        if (get_user_status() !='admin') break;
        
        $query = '
  UPDATE '.REVIEW_TABLE.'
    SET 
      title = "'.$_POST['title'].'", 
      content = "'.$_POST['content'].'"
    WHERE
      id_review = '.$_POST['id_review'].'
  ;';
        $db->query($query);
        
        header('Location: '.$self_url.'#reviews');
        break;
    }
  }

  // comments management
  if(get_user_status() !='admin')
  {
    if (isset($_GET['delete_review']))
    {
      delete_user_review($_GET['delete_review']);
      header('Location: '.$self_url);
    }
    else if (isset($_GET['validate_review']))
    {
      validate_user_review($_GET['validate_review']);
      header('Location: '.$self_url);
    }
  }

  $data = get_extension_infos_of($current_extension_page_id);

  if (!isset($data['id_extension']))
  {
    message_die('Unknown extension', 'Error', false );
  }

  //Get extension authors
  $authors = get_extension_authors($current_extension_page_id);

  //Check if user can make change to extension
  $page['user_can_modify'] = false;
  if (isset($user['id']) and 
    (is_Admin($user['id']) or isTranslator($user['id']) or in_array($user['id'], $authors)))
  {
    $page['user_can_modify'] = true;
  }

  //Check if user is extension owner
  $user['extension_owner'] = false;
  if (isset($user['id']) and (is_Admin($user['id']) or $user['id'] == $data['idx_user']))
  {
    $user['extension_owner'] = true;
  }

  // Get extension versions
  $versions_of_extension = get_versions_of_extension(
    array($current_extension_page_id)
  );

  // Get extension category
  $categories_of_extension = get_categories_of_extension(
    array($current_extension_page_id)
  );
    
  // Get extension tag
  $tags_of_extension = get_tags_of_extension(
    array($current_extension_page_id)
  );
  
  // download statistics
  $query = '
  SELECT
      id_revision,
      nb_downloads
    FROM '.PEM_REV_TABLE.'
    WHERE idx_extension = '.$current_extension_page_id.'
  ;';
  $result = pwg_query($query);

  $extension_downloads = 0;
  $downloads_of_revision = array();

  while ($row = pwg_db_fetch_assoc($result)) {
    $extension_downloads += $row['nb_downloads'];
    $downloads_of_revision[ $row['id_revision'] ] = $row['nb_downloads'];
  }

  $template->set_filename('pem_page', realpath(PEM_PATH . 'template/single_view.tpl'));

  $template->assign(
    array(
      'extension_id' => $current_extension_page_id,
      'extension_name' => htmlspecialchars(
        strip_tags($data['name'])
        ),
      'description' => nl2br(
        htmlspecialchars(
          strip_tags($data['description'])
          )
        ),
      'authors' => array_combine($authors, get_author_name($authors)),
      'first_date' => l10n('no revision yet'),
      'last_date'  => l10n('no revision yet'),
      'compatible_with' => implode(
          ', ',
          $versions_of_extension[$current_extension_page_id]
        ),
      'latest_compatible_version' =>  end($versions_of_extension[$current_extension_page_id]),
      'extension_downloads' => $extension_downloads,
      'extension_categories' => $categories_of_extension[$current_extension_page_id],
      'extension_tags' => empty($tags_of_extension[$current_extension_page_id]) ? array() : $tags_of_extension[$current_extension_page_id]
      )
    );

  if (isset($user['id']))
  {
    if ($page['user_can_modify'])
    {
      $template->assign(
        array(
          'can_modify' => $page['user_can_modify'],
          'u_modify' => 'extension_mod.php?eid='.$current_extension_page_id,
          'u_add_rev' => 'revision_add.php?eid='.$current_extension_page_id,
          'u_links' => 'extension_links.php?eid='.$current_extension_page_id,
          'u_screenshot'=> 'extension_screenshot.php?eid='.$current_extension_page_id,
          'translator' => !in_array($user['id'], $authors) and get_user_status() !='admin',
          )
        );
    }
    if ($user['extension_owner'])
    {
      $template->assign(
        array(
          'u_delete' => 'extension_del.php?eid='.$current_extension_page_id,
          'u_authors' => 'extension_authors.php?eid='.$current_extension_page_id
          )
        );
    }
    if ($conf['allow_svn_file_creation'] and $user['extension_owner'])
    {
      $template->assign('u_svn', 'extension_svn.php?eid='.$current_extension_page_id);
        
    }
  }

  if ($screenshot_infos = get_extension_screenshot_infos($current_extension_page_id))
  {
    $template->assign(
      'thumbnail',
      array(
        'src' => $screenshot_infos['thumbnail_src'],
        'url' => $screenshot_infos['screenshot_url'],
        )
      );
  }

  // Links associated to the current extension
  $tpl_links = array();

  // if the extension is hosted on github, add a link to the Github page
  if (isset($data['git_url']) and preg_match('/github/', $data['git_url']))
  {
    array_push(
      $tpl_links,
      array(
        'name' => l10n('Github page'),
        'url' => $data['git_url'],
        'description' => l10n('source code, bug/request tracker'),
      )
    );
  }

  $query = '
  SELECT name,
        url,
        description
    FROM '.PEM_LINKS_TABLE.'
    WHERE idx_extension = '.$current_extension_page_id.'
      AND (idx_language = 0 OR idx_language = '.$id_language.')
    ORDER BY rank ASC
  ;';
  $result = pwg_query($query);

  while ($row = pwg_db_fetch_assoc($result))
  {
    array_push(
      $tpl_links,
      array(
        'name' => $row['name'],
        'url' => $row['url'],
        'description' => $row['description'],
        )
      );
  }
  $template->assign('links', $tpl_links);

  // which revisions to display?
  $revision_ids = array();

  $query = '
  SELECT id_revision
    FROM '.PEM_REV_TABLE.' r
      LEFT JOIN '.PEM_COMP_TABLE.' c ON c.idx_revision = r.id_revision
      INNER JOIN '.PEM_EXT_TABLE.' e ON e.id_extension = r.idx_extension
    WHERE id_extension = '.$current_extension_page_id;

  if (isset($_SESSION['filter']['id_version']))
  {
    $query.= '
      AND idx_version = '.$_SESSION['filter']['id_version'];
  }
    
  $query.= '
  ;';
  $revision_ids = query2array($query, null, 'id_revision');

  $tpl_revisions = array();

  if (count($revision_ids) > 0)
  {
    $versions_of = get_versions_of_revision($revision_ids);
    $languages_of = get_languages_of_revision($revision_ids);
    $diff_languages_of = get_diff_languages_of_extension($current_extension_page_id);
    
    $revisions = array();

    $query = '
  SELECT id_revision,
        version,
        r.description  as default_description,
        date,
        url,
        author,
        rt.description
    FROM '.PEM_REV_TABLE.' AS r
    LEFT JOIN '.PEM_REV_TRANS_TABLE.' AS rt
      ON r.id_revision = rt.idx_revision
      AND rt.idx_language = '.$id_language.'
    WHERE id_revision IN ('.implode(',', $revision_ids).')
    ORDER by date DESC
  ;';

    $first_date = '';

    $is_first_revision = true;
    
    $result = pwg_query($query);  
    while ($row = pwg_db_fetch_assoc($result))
    {
      if (empty($row['description']))
      {
        $row['description'] = $row['default_description'];
      }
      if (!isset($last_date_set))
      {
        $last_languages = get_languages_of_revision(array($row['id_revision']));
        $template->assign(array(
          'last_date' => date('Y-m-d', $row['date']),
          'last_date_formatted_since' => time_since($row['date'], $stop='month'),
          'download_last_url' => 'download.php?rid='.$row['id_revision'],
          'ext_languages' => array_shift($last_languages),
          ));
        $last_date_set = true;
      }

      $is_first_revision = false;

      $tpl_revisions[] = array(
          'id' => $row['id_revision'],
          'version' => $row['version'],
          'versions_compatible' => implode(
            ', ',
            $versions_of[ $row['id_revision'] ]
            ),
          'languages' => isset($languages_of[$row['id_revision']]) ?
            $languages_of[$row['id_revision']] : array(),
          'languages_diff' => isset($diff_languages_of[$row['id_revision']]) ?
            $diff_languages_of[$row['id_revision']] : array(),
          'date' => date('Y-m-d', $row['date']),
          'author' => (count($authors) > 1 or $row['author'] != $data['idx_user']) ?
                        get_author_name($row['author']) : '',
          'u_download' => 'download.php?rid='.$row['id_revision'],
          'description' => nl2br(
            htmlspecialchars($row['description'])
            ),
          'can_modify' => $page['user_can_modify'],
          'u_modify' => 'revision_mod.php?rid='.$row['id_revision'],
          'u_delete' => 'revision_del.php?rid='.$row['id_revision'],
          'expanded' => isset($_GET['rid']) && $row['id_revision'] == $_GET['rid'],
          'downloads' => isset($downloads_of_revision[$row['id_revision']]) ? 
                          $downloads_of_revision[$row['id_revision']] : 0,
          );

      $first_date = $row['date'];
    }

    $template->assign(
      'first_date', format_date($first_date, array('day_name','day','month','year'))
    );

    $template->assign(
      'first_date_formatted_since', time_since($first_date, $stop='month'),
    );

    if ($conf['revisions_sort_order'] == 'version')
    {
      usort($tpl_revisions, function($a, $b) {
        return safe_version_compare($b['version'], $a['version']);
      });
    }

    if (!isset($_GET['rid']))
    {
      $tpl_revisions[0]['expanded'] = true;
    }

    $template->assign('revisions', $tpl_revisions);
  }

  // rating
  $rate_summary = array(
    'count' => 0, 
    'count_text' => sprintf(l10n('Rated %d times'), 0), 
    'rating_score' => generate_static_stars($data['rating_score']), 
    );
  if ($rate_summary['rating_score'] != null)
  {
    $query = '
  SELECT COUNT(1)
    FROM '.PEM_RATE_TABLE.'
    WHERE idx_extension = '.$current_extension_page_id.'
  ;';
    list($rate_summary['count']) = pwg_db_fetch_row(pwg_query($query));
    $rate_summary['count_text'] = sprintf(l10n('Rated %d times'), $rate_summary['count']);
  }
  $template->assign('rate_summary', $rate_summary);

  $user_rate = null;
  if ($rate_summary['count'] > 0)
  {
    $user_id = empty($user['id']) ? 0 : $user['id'];
    
    $query = '
  SELECT rate
    FROM '.PEM_RATE_TABLE.'
    WHERE 
      idx_extension = '.$current_extension_page_id. '
      AND idx_user = '.$user_id.';';
    if ($user_id == 0)
    {
      $ip_components = explode('.', $_SERVER['REMOTE_ADDR']);
      if (count($ip_components) > 3)
      {
        array_pop($ip_components);
      }
      $query.= '
      AND anonymous_id = "'.implode('.', $ip_components) . '"';
    }
    $query.= '
  ;';

    if ( pwg_db_num_rows( pwg_query($query) ) > 0 )
    {
      list($user_rate) = pwg_db_fetch_row($result);
    }
  }
  $template->assign('user_rating', array(
    'action' => $self_url.'&amp;action=rate',
    'rate' => $user_rate,
    ));
    
  // REVIEWS
  // total reviews in each language
  $current_language_id = get_current_language_id();

  $query = '
  SELECT
      idx_language,
      COUNT(1) AS count
    FROM '.PEM_REVIEW_TABLE.'
    WHERE 
      idx_extension = '.$current_extension_page_id.'
      '.(get_user_status() !='admin' ? 'AND validated = "true"' : null).'
    GROUP BY idx_language
  ;';
  $total_reviews = query2array($query, 'idx_language');

  $total=0;
  foreach ($total_reviews as $language) $total+= $language['count'];
  $template->assign('nb_reviews', $total);

  // reviews filter
  if ( isset($_GET['display_all_reviews']) or !array_key_exists($current_language_id, $total_reviews) )
  {
    $where_clause = '1=1';
  }
  else
  {
    $where_clause = 'idx_language = '.$current_language_id;
    if ($total != $total_reviews[ $current_language_id ]['count'])
    {
      $template->assign('U_DISPLAY_ALL_REVIEWS', $self_url.'&amp;display_all_reviews#reviews');
      $template->assign('NB_REVIEWS_MASKED', $total-$total_reviews[ $current_language_id ]['count']);
    }
  }

  // get displayed reviews
  $query = '
  SELECT *
    FROM '.PEM_REVIEW_TABLE.'
    WHERE
      idx_extension = '.$current_extension_page_id.'
      AND '.$where_clause.'
      '.(get_user_status() !='admin' ? 'AND validated = "true"' : null).'
    ORDER BY date DESC
  ;';
  $all_reviews = query2array($query);

  $language_reviews = $other_reviews = array();
  foreach ($all_reviews as $review)
  {
    $review['in_edit'] = false;
    $review['rate'] = generate_static_stars($review['rate'], false);
    $review['date'] = date('d F Y', strtotime($review['date']));
    
    if (get_user_status() =='admin')
    {
      if ( isset($_GET['edit_review']) and $_GET['edit_review'] == $review['id_review'] ) 
      {
        $review['in_edit'] = true;
        $review['u_cancel'] = $self_url;
        $review['action'] = $self_url.'&amp;action=edit_review';
      }
      
                                          $review['u_delete']   = $self_url.'&amp;delete_review='.$review['id_review'];
      if (!$review['in_edit'])             $review['u_edit']     = $self_url.'&amp;edit_review='.$review['id_review'];
      if ($review['validated'] == 'false') $review['u_validate'] = $self_url.'&amp;validate_review='.$review['id_review'];
    }
    else
    {
      unset($review['email']);
    }
    
    if (!$review['in_edit'])
    {
      $review['content'] = nl2br($review['content']);
    }
    
    if ($review['idx_language'] == $current_language_id)
    {
      array_push($language_reviews, $review);
    }
    else
    {
      array_push($other_reviews, $review);
    }
  }
  $template->assign('reviews', array_merge($language_reviews, $other_reviews));

  // prefilled and invisible inputs
  if (!empty($user['id']))
  {
    $user_review['author'] = $user['username'];
    $user_review['email'] = $user['email'];
    $user_review['is_logged'] = true;
  }
  $user_review['form_action'] = $self_url.'&amp;action=add_review';
  $user_review = array_map('stripslashes', $user_review);
  $template->assign('user_review', $user_review);

  $scores = array_combine(range(0.5,5,0.5), range(0.5,5,0.5));
  $scores[0] = '--';
  asort($scores);
  $template->assign('scores', $scores);


  $template->assign(
    array(
    'PEM_PATH' => PEM_PATH,
    )
  );
}
else
{
  http_response_code(404);
  $template->set_filenames(array('pem_page' => realpath(PEM_PATH . 'template/404.tpl')));
}