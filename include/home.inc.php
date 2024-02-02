<?php
global $conf;

include_once(PEM_PATH . 'include/functions_core.inc.php');

/**
 * Spotlighted, highest rated and most downloaded extensions are defined by admin in local config
 */
$pem_spotlight_extensions = conf_get_param('pem_spotlight_extensions',array());
$pem_highest_rated_extensions = conf_get_param('pem_highest_rated_extensions',array());
$pem_most_downloaded_extensions = conf_get_param('pem_most_downloaded_extensions',array());
$pem_most_recent_extensions = [];

/**
 * Get most recent extensions details
 */
$pem_most_recent_extensions_ids = implode(",", array_values($pem_most_recent_extensions));

$query = '
SELECT
    r.idx_extension AS eid,
    c.idx_category AS cid,
    r.date,
    r.description
 FROM '.PEM_REV_TABLE.' AS r
   	LEFT JOIN '.PEM_EXT_CAT_TABLE.' AS c
      	ON c.idx_extension = r.idx_extension
ORDER BY r.date DESC
;';

$result = pwg_query($query);

$category_ids = [];
$pem_most_recent_extensions_ids = [];
while($row = pwg_db_fetch_assoc($result))
{

  if(!in_array($row['cid'], $category_ids))
  {
    array_push($pem_most_recent_extensions_ids, $row['eid'] );
    $pem_most_recent_extensions[$row['cid']] = $row;
    $pem_most_recent_extensions[$row['cid']]['formatted_date'] = format_date($row['date']);
    $pem_most_recent_extensions[$row['cid']]['time_since'] = time_since($row['date'], $stop='month');

    array_push($category_ids, $row['cid']);
  }
}

$pem_most_recent_extensions_ids = implode(",", array_values($pem_most_recent_extensions_ids));

$query = '
SELECT
    id_extension AS eid,
    name,
    idx_category as cid
  FROM '.PEM_EXT_TABLE.' AS extensions
    left JOIN '.PEM_EXT_CAT_TABLE.' AS categories
      ON extensions.id_extension = categories.idx_extension
  WHERE id_extension IN ('.$pem_most_recent_extensions_ids.')
;';
$result = pwg_query($query);

while($row = pwg_db_fetch_assoc($result))
{
  $pem_most_recent_extensions[$row['cid']]['name'] = $row['name'];
}

/**
 * Get list of categories with name and count of plugins
 */

$query = '
SELECT
    id_category as cid,
    name 
  FROM '.PEM_CAT_TABLE.' 
  ORDER BY name ASC
;';

$categories = query2array($query, 'cid');

foreach ($categories as $i => $category) {
  // Skip languages on homepage
  if (8 == $category['cid'])
  {
    continue;
  }

  //Set count of extensions per category
  $categories[$i]['nb_extensions'] = pem_extensions_get_count($category['cid']);

  /**
   * Get spolighted extension details 
   */
  $query = '
SELECT
  id_extension  AS eid,
  name,
  description,
  idx_category AS cid
FROM '.PEM_EXT_TABLE.' AS extensions
  left JOIN '.PEM_EXT_CAT_TABLE.' AS categories
    ON extensions.id_extension = categories.idx_extension
WHERE id_extension = '.$pem_spotlight_extensions[$category['cid']].'
;';

  $result = query2array($query);
  $pem_spotlight_extensions[$category['cid']] = $result[0];

  //Set spotlighted extension
  $categories[$i]['spotlight_extension'] = null;
  if (isset($pem_spotlight_extensions[$category['cid'] ])) {
    $categories[$i]['spotlight_extension'] = $pem_spotlight_extensions[ $category['cid'] ];
    $categories[$i]['spotlight_extension']['description'] = stripslashes($categories[$i]['spotlight_extension']['description']);

    //Get screenshot info
    $screenshot_infos = get_extension_screenshot_infos(
      $pem_spotlight_extensions[$category['cid']]['eid']
    );

    if(!empty($screenshot_infos))
    {
      $categories[$i]['spotlight_extension']['screenshot_src'] = $screenshot_infos['screenshot_url'];
    }
    else
    {
      $categories[$i]['spotlight_extension']['screenshot_src'] = get_absolute_root_url() . PEM_PATH .'images/image-solid.svg';
      $categories[$i]['spotlight_extension']['screenshot_class'] = 'placeholder_image';
    }
  }

  /**
   * Get highested rated extension details
   */
  $query = '
SELECT
    id_extension AS eid,
    name,
    description,
    idx_category as cid,
    rating_score
  FROM '.PEM_EXT_TABLE.' AS extensions
    left JOIN '.PEM_EXT_CAT_TABLE.' AS categories
      ON extensions.id_extension = categories.idx_extension
      WHERE id_extension = '.$pem_highest_rated_extensions[$category['cid']].'
;';

  $result = query2array($query);
  $pem_highest_rated_extensions[$category['cid']] = $result[0];

  //Set highest rated extension
  $categories[$i]['highest_rated_extension'] = null;
  if (isset($pem_highest_rated_extensions[$category['cid'] ])) {
    $categories[$i]['highest_rated_extension'] = $pem_highest_rated_extensions[ $category['cid'] ];
    $categories[$i]['highest_rated_extension']['description'] = stripslashes($categories[$i]['highest_rated_extension']['description']);

    //Get screenshot info
    $screenshot_infos = get_extension_screenshot_infos(
      $pem_highest_rated_extensions[$category['cid']]['eid']
    );

    if(!empty($screenshot_infos))
    {
      $categories[$i]['highest_rated_extension']['screenshot_src'] = $screenshot_infos['screenshot_url'];
    }
    else
    {
      $categories[$i]['highest_rated_extension']['screenshot_src'] = get_absolute_root_url() . PEM_PATH .'images/image-solid.svg';
      $categories[$i]['highest_rated_extension']['screenshot_class'] = 'placeholder_image';
    }
  }

  /**
   * Get most downloaded extension details
   */
  $query = '
SELECT
    e.id_extension as eid,
    name,
    SUM(nb_downloads) AS download_count,
    e.description,
    c.idx_category as cid
FROM '.PEM_REV_TABLE.' AS r
  LEFT JOIN '.PEM_EXT_TABLE.' AS e
  ON r.idx_extension = e.id_extension
    left JOIN '.PEM_EXT_CAT_TABLE.' AS c
    ON e.id_extension = c.idx_extension
    WHERE e.id_extension = '.$pem_most_downloaded_extensions[$category['cid']].'
      GROUP BY r.idx_extension
      ORDER BY download_count DESC 
;';
  $result = query2array($query);
  $pem_most_downloaded_extensions[$category['cid']] = $result[0];

  //Set most downloaded
  $categories[$i]['most_downloaded_extension'] = null;
  if (isset($pem_most_downloaded_extensions[$category['cid']])) {
    $categories[$i]['most_downloaded_extension'] = $pem_most_downloaded_extensions[$category['cid']];
    $categories[$i]['most_downloaded_extension']['description'] = stripslashes($categories[$i]['most_downloaded_extension']['description']);


    //Get screenshot info
    $screenshot_infos = get_extension_screenshot_infos(
      $pem_most_downloaded_extensions[$category['cid']]['eid']
    );

    if(!empty($screenshot_infos))
    {
      $categories[$i]['most_downloaded_extension']['screenshot_src'] = $screenshot_infos['screenshot_url'];
    }
    else
    {
      $categories[$i]['most_downloaded_extension']['screenshot_src'] = get_absolute_root_url() . PEM_PATH .'images/image-solid.svg';
      $categories[$i]['most_downloaded_extension']['screenshot_class'] = 'placeholder_image';
    }
  }
  /**
   * For most recent extension that was handled previously
   */

  //Set most recent extension
  $categories[$i]['most_recent_extension'] = null;
  if (isset($pem_most_recent_extensions[$category['cid'] ])) {
    $categories[$i]['most_recent_extension'] = $pem_most_recent_extensions[ $category['cid'] ];
    $categories[$i]['most_recent_extension']['description'] = stripslashes($categories[$i]['most_recent_extension']['description']);


    //Get screenshot info
    $screenshot_infos = get_extension_screenshot_infos(
      $pem_most_recent_extensions[$category['cid']]['eid']
    );

    if(!empty($screenshot_infos))
    {
      $categories[$i]['most_recent_extension']['screenshot_src'] = $screenshot_infos['screenshot_url'];
    }
    else
    {
      $categories[$i]['most_recent_extension']['screenshot_src'] = get_absolute_root_url() . PEM_PATH .'images/image-solid.svg';
      $categories[$i]['most_recent_extension']['screenshot_class'] = 'placeholder_image';
    }
  }

}

$template->assign(
  array(
    'CATEGORIES' => $categories
  )
);
