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
// |                           Form submission                             |
// +-----------------------------------------------------------------------+

if (isset($_POST['pem_action']) and isset($_POST['submit']) and "edit_screenshot" == $_POST['pem_action'])
{
  if (!isset($_FILES['picture']))
  {
    $template->assign(
      array(
        'MESSAGE' => l10n('You did not upload anything!'),
        'MESSAGE_TYPE' => 'error'
      )
    );
    $page['errors'][] = l10n('You did not upload anything!');
  }
  else
  {
    $extension_dir = PEM_DIR.get_extension_dir($_GET['eid']);

    if (!is_dir($extension_dir)) {
      umask(0000);
      if (!mkdir($extension_dir, 0777)) {
        die("problem during ".$extension_dir." creation");
      }
    }
    
    $temp_name = PEM_DIR.get_extension_dir($_GET['eid']).'/screenshot.tmp';
    if (!move_uploaded_file($_FILES['picture']['tmp_name'], $temp_name))
    {
      $template->assign(
        array(
          'MESSAGE' => l10n('Problem during upload'),
          'MESSAGE_TYPE' => 'error'
        )
      );
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
        $template->assign(
          array(
            'MESSAGE' => l10n('You can only upload PNG and JPEG files as screenshot.'),
            'MESSAGE_TYPE' => 'error'
          )
        );
        $page['errors'][] = l10n('You can only upload PNG and JPEG files as screenshot.');
      }
      else
      {
        $screenshot_filename = get_extension_screenshot_src($_GET['eid']);

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
        $thumbnail_filename = get_extension_thumbnail_src($_GET['eid']);

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
      'MESSAGE' => 'Screenshot successfuly updated.',
      'MESSAGE_TYPE' => 'success'
    )
  );
}

if (isset($_POST['submit_delete']))
{
  $screenshot_infos = get_extension_screenshot_infos($_GET['eid']);
  
  if ($screenshot_infos)
  {
    unlink($screenshot_infos['thumbnail_src']);
    unlink($screenshot_infos['screenshot_url']);
  }
}

?>
