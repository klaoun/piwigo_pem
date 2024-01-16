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

// +-----------------------------------------------------------------------+
// |                           Form submission                             |
// +-----------------------------------------------------------------------+

if (isset($_POST['pem_action']) and isset($_POST['submit']))
{
  if ("add_link" == $_POST['pem_action'])
  {
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

      $template->assign(
        array(
          'MESSAGE' => 'New link added succesfully added.',
          'MESSAGE_TYPE' => 'success'
        )
      );
  }
  else if ("edit_link" == $_POST['pem_action'])
  {
    $data = array(
      'name'            => pwg_db_real_escape_string($_POST['link_name']),
      'url'             => pwg_db_real_escape_string($_POST['link_url']),
      'idx_extension'   => $page['extension_id'],
      'id_link'         => pwg_db_real_escape_string($_POST['link_id']),
    );

    if (!empty($_POST['link_language']))
    {
      $data['idx_language'] = pwg_db_real_escape_string($_POST['link_language']);
    }

    if(is_numeric($data['id_link']) && 'git' != $data['id_link'])
    {   
      single_update(
        PEM_LINKS_TABLE,
        $data,
        array('id_link' => $data['id_link'])
      );
      
      $template->assign(
        array(
          'MESSAGE' => 'This link has been succesfully updated.',
          'MESSAGE_TYPE' => 'success'
        )
      );
    }
    else if(!is_numeric($data['id_link']) && 'git' == $data['id_link'] || 'svn' == $data['id_link'])
    {
      // first we reset both URLs
      $query = '
UPDATE '.PEM_EXT_TABLE.'
  SET svn_url = NULL
    , git_url = NULL
  WHERE id_extension = '.$page['extension_id'].'
;';
      pwg_query($query);
  
      $query = '
UPDATE '.PEM_EXT_TABLE.'
SET '.$_POST['link_id'].'_url = "'.$data['url'].'"
WHERE id_extension = '.$page['extension_id'].'
;';
    pwg_query($query);
    
    $template->assign(
      array(
        'MESSAGE' => 'This link has been succesfully updated.',
        'MESSAGE_TYPE' => 'success'
      )
    );
    }
  }
}

?>