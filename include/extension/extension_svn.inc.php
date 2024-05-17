<?php

// +-----------------------------------------------------------------------+
// |                           Form submission                             |
// +-----------------------------------------------------------------------+

if (isset($_POST['pem_action']) and isset($_POST['submit']))
{

  $query = '
SELECT svn_url, git_url, archive_root_dir, archive_name
  FROM '.PEM_EXT_TABLE.'
  WHERE id_extension = '.$_GET['eid'].'
;';
$result = pwg_query($query);

list($svn_url, $git_url, $root_dir, $archive_name) = pwg_db_fetch_array($result);

  // echo('<pre>');print_r(($_POST));echo('</pre>');
  if ("edit_svn_git" == $_POST['pem_action'])
  {
    if (!in_array($_POST['type'], array('svn', 'git')))
    {
      die("unexpected repository type, either svn or git");
    }

    $url = pwg_db_real_escape_string($_POST['url']);
    
    if (empty($svn_url) and empty($git_url))
    { 
      $root_dir = ltrim(strrchr(rtrim($url, '/\\'), '/'), '/\\');
      $root_dir = str_replace(".git","", $root_dir);
      $archive_name = $root_dir . '_%.zip';
    }
    else
    {
      if (preg_match('/[^a-z0-9_-]/i', $_POST['root_dir']))
      {
        $template->assign(
          array(
            'MESSAGE' => l10n('Characters not allowed in archive root directory.'),
            'MESSAGE_TYPE' => 'error'
          )
        );
        $page['errors'][] = l10n('Characters not allowed in archive root directory.');
      }
      if (preg_match('/[^a-z0-9_\-%\.]/i', $_POST['archive_name']))
      {
        $template->assign(
          array(
            'MESSAGE' => l10n('Characters not allowed in archive root directory.'),
            'MESSAGE_TYPE' => 'error'
          )
        );
        $page['errors'][] = l10n('Characters not allowed in archive root directory.');
      }

      $root_dir = pwg_db_real_escape_string($_POST['root_dir']);
      $archive_name = pwg_db_real_escape_string($_POST['archive_name']);

      $extension = substr(strrchr($_POST['archive_name'], '.' ), 1, strlen($_POST['archive_name']));
      if ($extension != 'zip')
      {
        $archive_name .= '.zip';
      }
    }

    if (empty($page['errors']))
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
SET '.$_POST['type'].'_url = "'.$url.'",
    archive_root_dir = "'.$root_dir.'",
    archive_name = "'.$archive_name.'"
WHERE id_extension = '.$_GET['eid'].'
;';

      pwg_query($query);

      list($svn_url, $git_url) = array(null,null);
      if ('svn' == $_POST['type'])
      {
        $svn_url = $url;
      }
      elseif ('git' == $_POST['type'])
      {
        $git_url = $url;
      }

      $template->assign(
        array(
          'MESSAGE' => 'SVN/Git information succesfully updated.',
          'MESSAGE_TYPE' => 'success'
        )
      );
    }
  }
}

?>