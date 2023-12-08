 <?php
// define('INTERNAL', true);
// $root_path = './';
// require_once($root_path.'include/common.inc.php');

// $tpl->set_filenames(
//   array(
//     'page' => 'page.tpl',
//     'extension_authors' => 'extension_authors.tpl'
//   )
// );

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

$extension_infos = get_extension_infos_of($page['extension_id']);

// if ($user['id'] != $extension_infos['idx_user'] and !is_Admin($user['id']))
// {
//   die('You must be the extension author to modify it.');
// }

$query = '
SELECT name
  FROM '.PEM_EXT_TABLE.'
  WHERE id_extension = '.$page['extension_id'].'
;';
$result = pwg_query($query);

if (!pwg_db_num_rows($result))
{
  die('Incorrect extension identifier');
}
list($page['extension_name']) = pwg_db_fetch_array($result);

// +-----------------------------------------------------------------------+
// |                           Form submission                             |
// +-----------------------------------------------------------------------+
if (isset($_POST['pem_action']) and "edit_authors" == $_POST['pem_action'])
{
  echo('<pre>');print_r($_POST);echo('</pre>');
  // $authors = implode(',',$_POST['authors']);
  $query = '
SELECT '.$conf['user_fields']['id'].' AS id
  FROM '.USERS_TABLE.'
  WHERE '.$conf['user_fields']['id'].' = '. pwg_db_real_escape_string($_POST['author']) .'
;';

  list($author_id) = pwg_db_fetch_array(pwg_query($query));

  if (empty($author_id))
  {
    $page['errors'][] = l10n('This user does not exist in database.');
  }
  else
  {
    $authors = get_extension_authors($page['extension_id']);

    if (!in_array($author_id, $authors))
    {
      $query = '
INSERT INTO '.PEM_AUTHORS_TABLE.' (idx_extension, idx_user)
  VALUES ('.$page['extension_id'].', '.$author_id.')
;';
      pwg_query($query);
    }
  }
}

if (isset($_GET['delete']))
{
  $author = intval($_GET['delete']);

  if ($author > 0)
  {
    $query = '
DELETE FROM '.AUTHORS_TABLE.'
  WHERE idx_user = '.$author.'
  AND idx_extension = '.$page['extension_id'].'
;';
    pwg_query($query);
  }
}

if (isset($_GET['owner']))
{
  $author = intval($_GET['owner']);

  if ($author > 0)
  {
    $query = '
UPDATE '.PEM_EXT_TABLE.'
  SET idx_user = '.$author.'
  WHERE id_extension = '.$page['extension_id'].'
;';
    pwg_query($query);

    $query = '
DELETE FROM '.AUTHORS_TABLE.'
  WHERE idx_user = '.$author.'
  AND idx_extension = '.$page['extension_id'].'
;';
    pwg_query($query);

    $query = '
INSERT INTO '.AUTHORS_TABLE.' (idx_extension, idx_user)
  VALUES ('.$page['extension_id'].', '.$extension_infos['idx_user'].')
;';
    pwg_query($query);

    $extension_infos['idx_user'] = $author;
  }
}


// +-----------------------------------------------------------------------+
// |                            Form display                               |
// +-----------------------------------------------------------------------+

$authors = get_extension_authors($page['extension_id']);

foreach ($authors as $author_id)
{
  $author = array(
    'ID' => $author_id,
    'NAME' => get_author_name($author_id),
    'OWNER' => $author_id == $extension_infos['idx_user'],
    'u_delete' => 'extension_authors.php?eid='.$page['extension_id'].
                  '&amp;delete='.$author_id,
    );

  if (is_Admin($user['id']))
  {
    $author['u_owner'] = 'extension_authors.php?eid='.$page['extension_id'].
                  '&amp;owner='.$author_id;
  }

  $template->append('authors', $author);
}

// Get all user list
$query = '
SELECT '.$conf['user_fields']['id'].' AS id,
       '.$conf['user_fields']['username'].' AS username
  FROM '.USERS_TABLE.'
  ORDER BY username
;';
$result = pwg_query($query);

$users = array(0 => '');
while ($row = pwg_db_fetch_assoc($result))
{
  if (!empty($row['username']))
  {
    $users[$row['id']] = $row['username'];
  }
}

$template->assign(
  array(
    'extension_name' => $page['extension_name'],
    'u_extension' => 'extension_view.php?eid='.$page['extension_id'],
    'f_action' => 'extension_authors.php?eid='.$page['extension_id'],
    'users' => $users,
    )
  );

// +-----------------------------------------------------------------------+
// |                           html code display                           |
// +-----------------------------------------------------------------------+
// flush_page_messages();
// $template->assign_var_from_handle('main_content', 'extension_authors');
// include($root_path.'include/header.inc.php');
// include($root_path.'include/footer.inc.php');
// $template->parse('page');
// $template->p();
?>