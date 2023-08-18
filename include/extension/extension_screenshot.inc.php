<?php

$current_extension_page_id = $_GET['eid'];

// +-----------------------------------------------------------------------+
// |                             Functions                                 |
// +-----------------------------------------------------------------------+

function get_picture_size(
  $original_width,
  $original_height,
  $max_width,
  $max_height
  )
{
  $width = $original_width;
  $height = $original_height;

  if ($width > $max_width)
  {
    $width = $max_width;
    $height = floor(($width * $original_height) / $original_width);
  }

  if ($height > $max_height)
  {
    $height = $max_height;
    $width = floor(($height * $original_width) / $original_height);
  }
  
  return array('width' => $width, 'height' => $height);
}

// $dimensions = array(
//   array('width' => 750, 'height' => 500),
//   array('width' => 850, 'height' => 500),
//   array('width' => 750, 'height' => 700),
//   array('width' => 800, 'height' => 600),
//   array('width' => 900, 'height' => 900),
//   array('width' => 763, 'height' => 687),
//   );
// foreach ($dimensions as $dim)
// {
//   echo "<br />";
//   $new_dim = get_picture_size($dim['width'], $dim['height'], 800, 600);
//   echo implode('x', array($dim['width'], $dim['height']));
//   echo ' => ';
//   echo implode('x', array($new_dim['width'], $new_dim['height']));
//   echo '<br />';
// }
// exit();

function resize_picture(
  $original_filename,
  $destination_filename,
  $destination_dimensions
  )
{
  list($original_width, $original_height, $type) =
    getimagesize($original_filename);

  // $type == 2 means JPG
  // $type == 3 means PNG
  switch ($type) {
    case 2:
    {
      $source_image = imagecreatefromjpeg($original_filename);
      break;
    }
    case 3 :
    {
      $source_image = imagecreatefrompng($original_filename);
      break;
    }
    default:
    {
      message_die('Can only resize PNG and JPEG files');
    }
  }

  $destination_image = imagecreatetruecolor(
    $destination_dimensions['width'],
    $destination_dimensions['height']
    );
  
  imagecopyresampled(
    $destination_image,
    $source_image,
    0, 0, 0, 0,
    $destination_dimensions['width'],
    $destination_dimensions['height'],
    $original_width,
    $original_height
    );

  imagejpeg($destination_image, $destination_filename, 95);
  
  // freeing memory ressources
  imagedestroy($source_image);
  imagedestroy($destination_image);
}

// resize_picture(
//   '/home/z0rglub/temp/resize/forum.png',
//   '/home/z0rglub/temp/resize/forum_resized.jpg',
//   array('width' => 666, 'height' => 600)
//   );
// exit();

// +-----------------------------------------------------------------------+
// |                           Initialization                              |
// +-----------------------------------------------------------------------+

if (!isset($user['id']))
{
  die('You must be connected to reach this page.');
}

// We need a valid extension
$page['extension_id'] =
  (isset($_GET['eid']) and is_numeric($_GET['eid']))
  ? $_GET['eid']
  : '';

if (empty($page['extension_id']))
{
  die('Incorrect extension identifier');
}

$authors = get_extension_authors($page['extension_id']);

// if (!in_array($user['id'], $authors) and !is_Admin($user['id']))
// {
//   die('You must be the extension author to modify it.');
// }

$query = '
SELECT
    name
  FROM '.PEM_EXT_TABLE.'
  WHERE id_extension = '.$page['extension_id'].'
;';
$result = pwg_query($query);

if (pwg_db_num_rows( pwg_query($query) ) == 0)
{
  die('Unknown extension');
}
list($page['extension_name']) = pwg_db_fetch_array($result);

// +-----------------------------------------------------------------------+
// |                           Form submission                             |
// +-----------------------------------------------------------------------+

if (isset($_POST['pem_action']) and "edit_screenshot" == $_POST['pem_action'])
{
  if (!isset($_FILES['picture']))
  {
    $page['errors'][] = l10n('You did not upload anything!');
  }
  else
  {
    // echo('<pre>');print_r(get_extension_dir($page['extension_id']));echo('</pre>');

    $extension_dir = PEM_DIR.get_extension_dir($page['extension_id']);

    // echo('<pre>');print_r($extension_dir);echo('</pre>');

    if (!is_dir($extension_dir)) {
      umask(0000);
      if (!mkdir($extension_dir, 0777)) {
        die("problem during ".$extension_dir." creation");
      }
    }
    
    $temp_name = PEM_DIR.get_extension_dir($page['extension_id']).'/screenshot.tmp';
    if (!move_uploaded_file($_FILES['picture']['tmp_name'], $temp_name))
    {
      $page['errors'][] = l10n('Problem during upload');
    }
    else
    {
      list($width, $height, $type) = getimagesize($temp_name);
      
      // $type == 2 means JPG
      // $type == 3 means PNG
      if (!in_array($type, array(2, 3)))
      {
        unlink($temp_name);
        $page['errors'][] = l10n('You can only upload PNG and JPEG files as screenshot.');
      }
      else
      {
        $screenshot_filename = get_extension_screenshot_src($page['extension_id']);

        // does the upload screenshot needs a resize?
        $new_dimensions = get_picture_size(
          $width,
          $height,
          $conf['screenshot_maxwidth'],
          $conf['screenshot_maxheight']
          );
        
        if ($width != $new_dimensions['width']
            or $height > $new_dimensions['height'])
        {
          resize_picture(
            $temp_name,
            $screenshot_filename,
            $new_dimensions
            );
          
          $width  = $new_dimensions['width'];
          $height = $new_dimensions['height'];
  
          unlink($temp_name);
        }
        else
        {
          @unlink($screenshot_filename);
          rename($temp_name, $screenshot_filename);
        }

        // create the thumbnail
        $thumbnail_filename = get_extension_thumbnail_src($page['extension_id']);

        resize_picture(
          $screenshot_filename,
          $thumbnail_filename,
          get_picture_size(
            $width,
            $height,
            $conf['thumbnail_maxwidth'],
            $conf['thumbnail_maxheight']
            )
          );
      }
    }
  }
  $template->assign(
    array(
      'MESSAGE' => 'Screenshot successfuly updated. Thank you.',
      'MESSAGE_TYPE' => 'success'
    )
  );
}

if (isset($_POST['submit_delete']))
{
  $screenshot_infos = get_extension_screenshot_infos($page['extension_id']);
  
  if ($screenshot_infos)
  {
    unlink($screenshot_infos['thumbnail_src']);
    unlink($screenshot_infos['screenshot_url']);
  }
}

// +-----------------------------------------------------------------------+
// |                            Form display                               |
// +-----------------------------------------------------------------------+

// $action = 'extension_screenshot.php?eid='.$page['extension_id'];

$template->assign(
  array(
    'u_extension' => 'extension_view.php?eid='.$page['extension_id'],
    // 'f_action' => $action,
    'extension_name' => $page['extension_name'],
    )
  );

if ($screenshot_infos = get_extension_screenshot_infos($page['extension_id']))
{
  $template->assign(
    'current',
    array(
      'thumbnail_src' => $screenshot_infos['thumbnail_src'],
      'u_screenshot'  => $screenshot_infos['screenshot_url'],
      )
    );
}

// +-----------------------------------------------------------------------+
// |                           html code display                           |
// +-----------------------------------------------------------------------+
// flush_page_messages();
// $tpl->assign_var_from_handle('main_content', 'extension_screenshot');
// include($root_path.'include/header.inc.php');
// include($root_path.'include/footer.inc.php');
// $tpl->parse('page');
// $tpl->p();
?>