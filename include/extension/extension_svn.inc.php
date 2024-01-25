<?php

// +-----------------------------------------------------------------------+
// |                           Form submission                             |
// +-----------------------------------------------------------------------+

if (isset($_POST['pem_action']) and isset($_POST['submit']))
{
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
      $archive_name = $root_dir . '_%.zip';
    }
    else
    {
      if (preg_match('/[^a-z0-9_-]/i', $_POST['root_dir']))
      {
        die('Characters not allowed in archive root directory.');
      }
      if (preg_match('/[^a-z0-9_\-%\.]/i', $_POST['archive_name']))
      {
        die('Characters not allowed in archive name.');
      }

      $root_dir = pwg_db_real_escape_string($_POST['root_dir']);
      $archive_name = pwg_db_real_escape_string($_POST['archive_name']);

      $extension = substr(strrchr($_POST['archive_name'], '.' ), 1, strlen($_POST['archive_name']));
      if ($extension != 'zip')
      {
        $archive_name .= '.zip';
      }
    }

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
  WHERE id_extension = '.$_GET['eid'].';';

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

?>