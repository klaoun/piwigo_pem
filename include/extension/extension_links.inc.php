<?php

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

global $template, $user;

// +-----------------------------------------------------------------------+
// |                           Form submission                             |
// +-----------------------------------------------------------------------+

if (isset($_POST['pem_action']) and isset($_POST['submit']))
{
  if (is_a_guest()) return;

  //Get list of extension authors
  $authors = get_extension_authors($current_extension_page_id);

  if (isset($user['id']) and (is_Admin() or in_array($user['id'], $authors)))
  {
    if ("add_link" == $_POST['pem_action'])
    {
      if (!preg_match('/^https?:/', $_POST['link_url']))
      {
        $template->assign(
          array(
            'MESSAGE' => l10n('Incorrect URL'),
            'MESSAGE_TYPE' => 'error'
          )
        );
        $page['errors'][] = l10n('Incorrect URL');
      }

    if (empty($_POST['link_name']))
    {
      $template->assign(
        array(
          'MESSAGE' => l10n('Link name must not be empty'),
          'MESSAGE_TYPE' => 'error'
        )
      );
      $page['errors'][] = l10n('Link name must not be empty');
    }

    if (empty($page['errors']))
    {
      // find next rank
      $query = '
SELECT MAX(`rank`) AS current_rank
  FROM '.PEM_LINKS_TABLE.'
  WHERE idx_extension = '.$_GET['eid'].'
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
        'idx_extension'   => $_GET['eid'],
        );

      if ($_POST['link_language'] != 'null')
      {
        $insert['idx_language'] = pwg_db_real_escape_string($_POST['link_language']);
      }
      else{
        $insert['idx_language'] = '0';
      }

      mass_inserts(
        PEM_LINKS_TABLE,
        array_keys($insert),
        array($insert)
        );
    }
    }
    else if ("edit_link" == $_POST['pem_action'])
    {
      $data = array(
        'name'            => pwg_db_real_escape_string($_POST['link_name']),
        'url'             => pwg_db_real_escape_string($_POST['link_url']),
        'idx_extension'   => $_GET['eid'],
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
  WHERE id_extension = '.$_GET['eid'].'
;';
        pwg_query($query);
    
        $query = '
UPDATE '.PEM_EXT_TABLE.'
SET '.$_POST['link_id'].'_url = "'.$data['url'].'"
WHERE id_extension = '.$_GET['eid'].'
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
  else
  {
    return;
  }
}

?>