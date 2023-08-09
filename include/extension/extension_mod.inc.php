<?php
global $page;

if (isset($_GET['eid']))
{
  // using single_view.tpl
  $current_extension_page_id = $_GET['eid'];
}
else if (isset($_GET['uid']))
{
  // Using account.tpl
  $current_user_page_id = $_GET['uid'];
}

// Form submitted
if (isset($_POST['submit']))
{

  // Checks that all the fields have been well filled
  $required_fields = array(
    'extension_name',
    'extension_category'
    );

  foreach ($required_fields as $field)
  {

    if (empty($_POST[$field]))
    {
      $page['errors'][] = l10n('Some fields are missing');
      break;
    }
  }

  if (empty($page['errors']))
  {

    // this action comes from single_view, we have an eid that is set
    if (isset($_POST['pem_action']) and "edit_general_info" == $_POST['pem_action'])
    {

      $authors = get_extension_authors($current_extension_page_id);

      if (!in_array($user['id'], $authors) and !is_Admin($user['id']) and !isTranslator($user['id']))
      {
        die('You must be the extension author to modify it.');
      }

      if (empty($_POST['extension_descriptions'][@$_POST['default_description']]))
      {
        $page['errors'][] = l10n('Default description can not be empty');
      }

      // Update the extension
      $query = '
UPDATE '.PEM_EXT_TABLE.'
  SET name = \''. pwg_db_real_escape_string($_POST['extension_name']) .'\',
      description = \''. pwg_db_real_escape_string($_POST['extension_descriptions'][$_POST['default_description']]) .'\',
      idx_language = '. pwg_db_real_escape_string($_POST['default_description']) .'
  WHERE id_extension = '.$current_extension_page_id.'
;';

      pwg_query($query);

      $query = '
DELETE
  FROM '.PEM_EXT_TRANS_TABLE.'
  WHERE idx_extension = '.$current_extension_page_id.'
;';

      pwg_query($query);

      $query = '
DELETE
  FROM '.PEM_EXT_CAT_TABLE.'
  WHERE idx_extension = '.$current_extension_page_id.'
;';

      pwg_query($query);

      $query = '
DELETE
  FROM '.PEM_EXT_TAG_TABLE.'
  WHERE idx_extension = '.$current_extension_page_id.'
;';

      pwg_query($query);

      $post_type ='updated';
    }
    // This actions comes from account, we have a uid that is set and not an eid
    else if (isset($_POST['pem_action']) and "add_ext" == $_POST['pem_action'])
    {
      // Inserts the extension (need to be done before the other includes, to
      // retrieve the insert id
      $insert = array(
        'idx_user'   => $user['id'],
        'name'         => pwg_db_real_escape_string($_POST['extension_name']),
        'description'  => 'Default description',
        'idx_language' => '5',
        );
      mass_inserts(PEM_EXT_TABLE, array_keys($insert), array($insert));
      $current_extension_page_id = pwg_db_insert_id();

      $post_type ='added';
    }


    // Insert translations
    $inserts = array();
    if (isset($_POST['extension_descriptions']))
    {
      foreach ($_POST['extension_descriptions'] as $lang_id => $desc)
      {
        if ($lang_id == $_POST['default_description'] or empty($desc))
        {
          continue;
        }
        array_push(
          $inserts,
          array(
            'idx_extension'  => $current_extension_page_id,
            'idx_language'   => $lang_id,
            'description'    => pwg_db_real_escape_string($desc),
            )
          );
      }
    }

    if (!empty($inserts))
    {
      mass_inserts(PEM_EXT_TRANS_TABLE, array_keys($inserts[0]), $inserts);
    }

    // Inserts the extensions <-> categories link
    $inserts = array();
    foreach ($_POST['extension_category'] as $category)
    {
      array_push(
        $inserts,
        array(
          'idx_category'   => pwg_db_real_escape_string($category),
          'idx_extension'  => $current_extension_page_id,
          )
        );
    }
    mass_inserts(PEM_EXT_CAT_TABLE, array_keys($inserts[0]), $inserts);

    // Inserts the extensions <-> tags link
    if (!empty($_POST['tags']))
    {
      $inserts = array();
      foreach ($_POST['tags'] as $tag)
      {
        array_push(
          $inserts,
          array(
            'idx_tag'   => $tag,
            'idx_extension'  => $current_extension_page_id,
            )
          );
      }
      mass_inserts(PEM_EXT_TAG_TABLE, array_keys($inserts[0]), $inserts);
    }
    
    $template->assign(
      array(
        'MESSAGE' => 'Extension successfuly '.$post_type.'. Thank you.',
        'MESSAGE_TYPE' => 'success'
      )
    );

  }
}

// Gets the available tags
$query = '
SELECT
    id_tag as tid,
    t.name AS default_name,
    tt.name
  FROM '.PEM_TAG_TABLE.' AS t
  LEFT JOIN '.PEM_TAG_TRANS_TABLE.' AS tt
      ON t.id_tag = tt.idx_tag
      AND tt.idx_language = \''.get_current_language_id().'\'
    ORDER BY LOWER(t.name)
;';

$all_tags = query2array($query);

// Gets the avaiable authors
$query = '
SELECT DISTINCT
    aT.idx_user as uid,
    uT.username
  FROM '.PEM_AUTHORS_TABLE.' as aT
    JOIN '.USERS_TABLE.' as uT on id = aT.idx_user
;';

$all_authors= query2array($query, 'uid');

$template->assign(
  array(
    'ALL_TAGS' => $all_tags,
    'ALL_AUTHORS' => $all_authors,
  )
);

