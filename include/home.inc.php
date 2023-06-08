<?php
global $conf;

/**
 * Spotlighted, highest rated and most downloaded extensions are defined by admin in local config
 */
$pem_spotlight_extensions = conf_get_param('pem_spotlight_extensions',array());
$pem_highest_rated_extensions = conf_get_param('pem_highest_rated_extensions',array());
$pem_most_downloaded_extensions = conf_get_param('pem_most_downloaded_extensions',array());
$pem_most_recent_extensions = [];

/**
 * Get spotlighted extensions details
 */
$pem_spotlight_extensions_ids = implode(",", array_values($pem_spotlight_extensions));
$query = '
SELECT
    id_extension  AS eid,
    name,
    description,
    idx_category AS cid
  FROM '.PEM_EXT_TABLE.' AS extensions
    left JOIN '.PEM_EXT_CAT_TABLE.' AS categories
      ON extensions.id_extension = categories.idx_extension
  WHERE id_extension IN ('.$pem_spotlight_extensions_ids.')
;';

$result = pwg_query($query);

while($row = pwg_db_fetch_assoc($result))
{
  $pem_spotlight_extensions[$row['cid']] = $row;
}

/**
 * Get highested rated extensions details
 */
$pem_highest_rated_extensions_ids = implode(",", array_values($pem_highest_rated_extensions));

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
  WHERE id_extension IN ('.$pem_highest_rated_extensions_ids.')
;';

$result = pwg_query($query);

while($row = pwg_db_fetch_assoc($result))
{
  $pem_highest_rated_extensions[$row['cid']] = $row;
}

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

$category_id = null;
while($row = pwg_db_fetch_assoc($result))
{

  if($category_id != $row['cid'])
  {
    $pem_most_recent_extensions[$row['cid']] = $row;
    $pem_most_recent_extensions[$row['cid']]['formatted_date'] = format_date($row['date']);
    $pem_most_recent_extensions[$row['cid']]['time_since'] = time_since($row['date'], $stop='month');

    $category_id = $row['cid'];
  }
}

$query = '
SELECT
    id_extension AS eid,
    name,
    idx_category as cid
  FROM '.PEM_EXT_TABLE.' AS extensions
    left JOIN '.PEM_EXT_CAT_TABLE.' AS categories
      ON extensions.id_extension = categories.idx_extension
  WHERE id_extension IN ('.$pem_highest_rated_extensions_ids.')
;';
$result = pwg_query($query);

while($row = pwg_db_fetch_assoc($result))
{
  $pem_most_recent_extensions[$row['cid']]['name'] = $row['name'];
}

/**
 * Get most downloaded extensions details
 */
$pem_most_downloaded_extensions_ids = implode(",", array_values($pem_most_downloaded_extensions));

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
    WHERE e.id_extension IN ('.$pem_most_downloaded_extensions_ids.')
      GROUP BY r.idx_extension
      ORDER BY download_count DESC 
;';

$result = pwg_query($query);

while($row = pwg_db_fetch_assoc($result))
{
  $pem_most_downloaded_extensions[$row['cid']] = $row;
}

/**
 * get count of extensions by category, returns category id and count
 */ 

$query = '
SELECT
    idx_category AS cid,
    COUNT(*) AS count
  FROM '.PEM_EXT_CAT_TABLE.'
  GROUP BY idx_category
;';
$nb_ext_of_category = query2array($query, 'cid', 'count');

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

  //Set count of extensions per category
  $categories[$i]['nb_extensions'] = 0;
  if (isset($nb_ext_of_category[ $category['cid'] ])) {
    $categories[$i]['nb_extensions'] = $nb_ext_of_category[ $category['cid'] ];
  }

  //Set spotlighted extension
  $categories[$i]['spotlight_extension'] = null;
  if (isset($pem_spotlight_extensions[$category['cid'] ])) {
    $categories[$i]['spotlight_extension'] = $pem_spotlight_extensions[ $category['cid'] ];
  }

  //Set highest rated extension
  $categories[$i]['highest_rated_extension'] = null;
  if (isset($pem_highest_rated_extensions[$category['cid'] ])) {
    $categories[$i]['highest_rated_extension'] = $pem_highest_rated_extensions[ $category['cid'] ];
  }

  //Set most downloaded
  $categories[$i]['most_downloaded_extension'] = null;
  if (isset($pem_most_downloaded_extensions[$category['cid'] ])) {
    $categories[$i]['most_downloaded_extension'] = $pem_most_downloaded_extensions[ $category['cid'] ];
  }

  //Set most recent extension
  $categories[$i]['most_recent_extension'] = null;
  if (isset($pem_most_recent_extensions[$category['cid'] ])) {
    $categories[$i]['most_recent_extension'] = $pem_most_recent_extensions[ $category['cid'] ];
  }
}

$template->assign(
  array(
    'CATEGORIES' => $categories
  )
);
