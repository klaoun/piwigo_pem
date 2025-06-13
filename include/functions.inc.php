<?php

/**
 * sort an array by version number
 */
function versort($array)
{
  if (empty($array)) return array();
  
  if (is_array($array[0])) {
    usort($array, 'pem_version_compare');
  }
  else {
    usort($array, 'version_compare');
  }

  return $array;
}

/**
 * specific version_compare for PEM
 */
function pem_version_compare($a, $b)
{
  return version_compare($a['version'], $b['version']);
}


/**
 * extracts a subfield from nested array
 */
function array_from_subfield($hash, $field)
{
  $array = array();
  
  foreach ($hash as $row)
  {
    array_push($array, $row[$field]);
  }

  return $array;
}


/**
 * generate read-only rating stars as displayed by jQuery Raty
 */
function generate_static_stars($score, $space=true)
{
  if ($score === null) return null;
  
  $score = min(max($score, 0), 5);
  $floor = floor($score);
  $space = $space ? "\n" : null;
  
  $html = null;
  for ($i=1; $i<=$floor; $i++)
  {
    $html.= '<i alt="'.$i.'" class="icon-star"></i>'.$space;
  }
  
  if ($score != 5)
  {
    if ($score-$floor <= .25)
    {
      $html.= '<i alt="'.($floor+1).'" class="icon-star"></i>'.$space;
    }
    else if ($score-$floor <= .75)
    {
      $html.= '<i alt="'.($floor+1).'" class="icon-star-half"></i>'.$space;
    }
    else
    {
      $html.= '<i alt="'.($floor+1).'" class="icon-star-empty"></i>'.$space;
    }
  
    for ($i=$floor+2; $i<=5; $i++)
    {
      $html.= '<i alt="'.$i.'" class="icon-star-empty"></i>'.$space;
    }
  }
  
  return $html;
}

/**
 * create necessary vars for the navigation bar
 */
function create_pagination_bar($base_url, $nb_pages, $current_page, $param_name)
{
  global $conf;

  $navbar = array();
  $pages_around = $conf['paginate_pages_around'];
  $url = $base_url.(preg_match('/\?/', $base_url) ? '&' : '?').$param_name.'=';

  // current page detection
  if (!isset($current_page) or !is_numeric($current_page) or $current_page < 0)
  {
    $current_page = 1;
  }

  // navigation bar useful only if more than one page to display !
  if ($nb_pages > 1)
  {
    $navbar['CURRENT_PAGE'] = $current_page;

    // link to first and previous page?
    if ($current_page > 1)
    {
      $navbar['URL_FIRST'] = $url . 1;
      $navbar['URL_PREV'] = $url . ($current_page - 1);
    }
    // link on next page?
    if ($current_page < $nb_pages)
    {
      $navbar['URL_NEXT'] = $url . ($current_page + 1);
      $navbar['URL_LAST'] = $url . $nb_pages;
    }

    // pages to display
    $navbar['pages'] = array();
    $navbar['pages'][1] = $url;
    $navbar['pages'][$nb_pages] = $url.$nb_pages;

    for ($i = max($current_page - $pages_around, 2), $stop = min($current_page + $pages_around + 1, $nb_pages);
         $i < $stop; $i++)
    {
      $navbar['pages'][$i] = $url.$i;
    }
    ksort($navbar['pages']);
  }
  return $navbar;
}

/**
 * Returns the number of pages to display in a pagination bar, given the number
 * of items and the number of items per page.
 */
function get_nb_pages($nb_items, $nb_items_per_page)
{
  return intval(($nb_items - 1) / $nb_items_per_page) + 1;
}

/***
 * Notify mattermost
 */
function notify_mattermost($message)
{
  global $conf;

  if (!isset($conf['mattermost_webhook_url']))
  {
    return 'ko';
  }

  $mattermost_data = array(
    'text' => $message,
    'channel' => $conf['mattermost_channel'],
  );

  $payload = 'payload='.json_encode($mattermost_data);

  $ch = curl_init($conf['mattermost_webhook_url']);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  $result = curl_exec($ch);

  return $result;
}


/**
 * Check how many downloads and since how long a revision was added
 * if the user isn't an admin and the revision was published more than 1 hour ago or has more than 10 downlaods
 * Then it can't be deleted
 */

function can_revision_be_deleted($id_revision)
{
    $query = '
SELECT
    id_revision,
    nb_downloads,
    date
  FROM '.PEM_REV_TABLE.'
  WHERE id_revision = '.$id_revision.'
;';

  $result = query2array($query, null);
  $nb_downloads = $result[0]['nb_downloads'];
  $date_created = strtotime($result[0]['date']);
  $one_hour_ago = time() - 3600;

  if (!is_admin() and $date_created < $one_hour_ago)
  {
    if(10 < $nb_downloads){
      $can_delete_revision = true;
    }
    else{
      $can_delete_revision = false;
    }
  }
  else
  {
    $can_delete_revision = false;
  }

  return $can_delete_revision;
}