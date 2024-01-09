<?php

// define('INTERNAL', true);
// $root_path = './';
// require_once($root_path.'include/common.inc.php');

// $tpl->set_filenames(
//   array(
//     'page' => 'page.tpl',
//     'extension_links' => 'extension_links.tpl'
//   )
// );

// +-----------------------------------------------------------------------+
// |                             Functions                                 |
// +-----------------------------------------------------------------------+

function order_links($extension_id)
{
  $query = '
SELECT id_link
  FROM '.PEM_LINKS_TABLE.'
  WHERE idx_extension = '.$extension_id.'
  ORDER by rank ASC
;';
  $sorted_link_ids = query2array($query, null, 'id_link');

  save_order_links($sorted_link_ids);
}

function save_order_links($sorted_link_ids)
{
  $current_rank = 0;

  $datas = array();

  foreach ($sorted_link_ids as $link_id)
  {
    array_push(
      $datas,
      array(
        'id_link' => $link_id,
        'rank' => ++$current_rank,
        )
      );
  }

  mass_updates(
    PEM_LINKS_TABLE,
    array(
      'primary' => array('id_link'),
      'update' => array('rank'),
      ),
    $datas
    );
}

// +-----------------------------------------------------------------------+
// |                           Initialization                              |
// +-----------------------------------------------------------------------+

global $template;

// We need a valid extension
$page['extension_id'] =
  (isset($_GET['eid']) and is_numeric($_GET['eid']))
  ? $_GET['eid']
  : '';

if (empty($page['extension_id']))
{
  die('Incorrect extension identifier');
}

// $authors = get_extension_authors($page['extension_id']);

// if (!in_array($user['id'], $authors) and !is_Admin($user['id']))
// {
//   die('You must be the extension author to modify it.');
// }

// $query = '
// SELECT name
//   FROM '.PEM_EXT_TABLE.'
//   WHERE id_extension = '.$page['extension_id'].'
// ;';
// $result = pwg_query($query);

// if (pwg_db_num_rows( pwg_query($query) ) == 0)
// {
//   message_die('Incorrect extension identifier');
// }
// list($page['extension_name']) = pwg_db_fetch_array($result);

// +-----------------------------------------------------------------------+
// |                           Form submission                             |
// +-----------------------------------------------------------------------+

if (isset($_POST['pem_action']) and isset($_POST['submit']) and "add_link" == $_POST['pem_action'])
{
    // find next rank
    $query = '
SELECT MAX(rank) AS current_rank
  FROM '.PEM_LINKS_TABLE.'
  WHERE idx_extension = '.$page['extension_id'].'
;';
    list($current_rank) = pwg_db_fetch_array(pwg_query($query));

    if (empty($current_rank))
    {
      $current_rank = 0;
    }

    $insert = array(
      'name'            => pwg_db_real_escape_string($_POST['link_name']),
      'url'             => pwg_db_real_escape_string($_POST['link_url']),
      'rank'            => $current_rank + 1,
      'idx_extension'   => $page['extension_id'],
      );

    if (!empty($_POST['link_language']))
    {
      $insert['idx_language'] = pwg_db_real_escape_string($_POST['link_language']);
    }

    mass_inserts(
      PEM_LINKS_TABLE,
      array_keys($insert),
      array($insert)
      );
}

if (isset($_POST['submit_order']))
{
  asort($_POST['linkRank'], SORT_NUMERIC);
  save_order_links(array_keys($_POST['linkRank']));
}

if (isset($_GET['delete']) and is_numeric($_GET['delete']))
{
  $query = '
DELETE
  FROM '.PEM_LINKS_TABLE.'
  WHERE id_link = '.$_GET['delete'].'
    AND idx_extension = '.$page['extension_id'].'
;';
  pwg_query($query);

  order_links($page['extension_id']);
}

// +-----------------------------------------------------------------------+
// |                            Form display                               |
// +-----------------------------------------------------------------------+

$template->assign(
  array(
    'u_extension' => 'extension_view.php?eid='.$page['extension_id'],
    'f_action' => 'extension_links.php?eid='.$page['extension_id'],
    'extension_name' => $page['extension_name'],
    'LINK_URL' => @$_POST['link_url'],
    'LINK_NAME' => @$_POST['link_name'],
    'LINK_DESC' => @$_POST['link_description'],
    'LINK_LANG' => @$_POST['link_language'],
    )
  );

$tpl_links =array();
  
$query = '
SELECT
    id_link,
    name,
    url,
    description,
    rank
  FROM '.PEM_LINKS_TABLE.'
  WHERE idx_extension = '.$page['extension_id'].'
  ORDER BY rank ASC
;';
$result = pwg_query($query);
while ($row = pwg_db_fetch_array($result))
{
  $description = '';

  if (!empty($row['description']))
  {
    if (strlen($row['description']) > 50)
    {
      $description = substr($row['description'], 0, 50).'...';
    }
    else
    {
      $description = $row['description'];
    }
  }

  array_push(
    $tpl_links,
    array(
      'id' => $row['id_link'],
      'name' => $row['name'],
      'rank' => $row['rank'] * 10,
      'description' => $description,
      'url' => $row['url'],
      'u_delete' =>
        'extension_links.php?eid='.$page['extension_id'].
        '&amp;delete='.$row['id_link'],
      )
    );
}

$template->assign('links', $tpl_links);

// +-----------------------------------------------------------------------+
// |                           html code display                           |
// +-----------------------------------------------------------------------+
// flush_page_messages();
// $tpl->assign_var_from_handle('main_content', 'extension_links');
// include($root_path.'include/header.inc.php');
// include($root_path.'include/footer.inc.php');
// $tpl->parse('page');
// $tpl->p();
?>