 <?php
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
if (isset($_POST['pem_action']) and isset($_POST['submit']) and "edit_authors" == $_POST['pem_action'])
{
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

// +-----------------------------------------------------------------------+
// |                            Form display                               |
// +-----------------------------------------------------------------------+

// Get list of all users
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
    'users' => $users,
    )
  );

?>