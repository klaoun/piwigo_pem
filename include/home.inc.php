<?php
global $conf;

// get count of extensions by category, returns category id and count
$query = '
SELECT
    idx_category,
    COUNT(*) AS count
  FROM '.PEM_EXT_CAT_TABLE.'
  GROUP BY idx_category
;';
  $nb_ext_of_category = query2array($query, 'idx_category', 'count');

//Get list of categories with name and count of plugins
$query = '
SELECT
    id_category AS id,
    c.name AS default_name,
    ct.name    
  FROM '.PEM_CAT_TABLE.' AS c
  LEFT JOIN '.PEM_CAT_TRANS_TABLE.' AS ct
    ON c.id_category = ct.idx_category
  ORDER BY name ASC
;';

$categories = query2array($query);

$categoriy_ids = [];
  
foreach ($categories as $i => $category) {
  array_push($categoriy_ids, $category['id'] );
  // echo('<pre>');print_r($i);echo('</pre>');
  // echo('<pre>');print_r($category);echo('</pre>');

  if (empty($categories[$i]['name']))
  {
    $categories[$i]['name'] = $categories[$i]['default_name'];
  }
  unset($categories[$i]['default_name']);
    
  $categories[$i]['counter'] = 0;
  if (isset($nb_ext_of_category[ $category['id'] ])) {
    $categories[$i]['counter'] = $nb_ext_of_category[ $category['id'] ];
  }

  $categories[$i]['type'] = strtolower($categories[$i]['name']);

    /**
   * Get highest rated extension for each category
   */

  $query = '
SELECT 
      id_extension,
      name,
      description,
      rating_score
  FROM '.PEM_EXT_TABLE.' AS extensions
    left JOIN '.PEM_EXT_CAT_TABLE.' AS categories
    ON extensions.id_extension = categories.idx_extension
    WHERE categories.idx_category = '.$category['id'].'
    ORDER BY rating_score DESC
    LIMIT 1
;';

  $highest_rated = query2array($query);

  //Two plugins have a rating score of 500 this iff is to check and round to a number under 5
  //If these two plugin sratings are fixed this if can be removed
  if ($highest_rated[0]['rating_score'] > 100)
  {
    $highest_rated[0]['rating_score'] = ($highest_rated[0]['rating_score'] / 100) < 5 ? ($highest_rated[0]['rating_score'] / 100) :round(($highest_rated[0]['rating_score'] / 100), 0, PHP_ROUND_HALF_DOWN);
  }

  $categories[$i]['highest_rated_extension'] = $highest_rated[0];

  /**
   * Get most downloaded extension for each category
   */

  $query = '
SELECT
    extensions.id_extension,
    name,
    SUM(nb_downloads) AS download_count,
    extensions.description
FROM piwigo_pem_pem_revisions AS revisions
  LEFT JOIN piwigo_pem_pem_extensions AS extensions
  ON revisions.idx_extension = extensions.id_extension
    left JOIN piwigo_pem_pem_extensions_categories AS categories
    ON extensions.id_extension = categories.idx_extension
      WHERE categories.idx_category = '.$category['id'].'
      GROUP BY revisions.idx_extension
      ORDER BY download_count DESC 
      LIMIT 1
;';
  
  $most_downloaded = query2array($query);

  $categories[$i]['most_downloaded_extension'] = $most_downloaded[0];

  /**
   * Get most recent extension for each category
   */

   $query = '
SELECT
    extensions.id_extension,
    name,
    revisions.date,
    extensions.description
  FROM piwigo_pem_pem_revisions AS revisions
    LEFT JOIN piwigo_pem_pem_extensions AS extensions
    ON revisions.idx_extension = extensions.id_extension
      LEFT JOIN piwigo_pem_pem_extensions_categories AS categories
      ON extensions.id_extension = categories.idx_extension
        WHERE categories.idx_category = '.$category['id'].'
    ORDER BY revisions.date DESC 
    LIMIT 1
;';
   
    $most_recent= query2array($query);

    $most_recent[0]['formatted_date'] = format_date($most_recent[0]['date']);
    $most_recent[0]['time_since'] = time_since($most_recent[0]['date'], $stop='month');

    $categories[$i]['most_recent_extension'] = $most_recent[0];

    /**
    * Get spotlighted extension for each category
    */

    $spolight_extension_id = $conf['pem_conf']['pem_spotlight_extension'][$categories[$i]['type']];
    $query = '
SELECT
    id_extension,
    name,
    description,
    username
  FROM '.PEM_EXT_TABLE.' AS e
    JOIN '.PEM_USER_TABLE.' AS u ON u.id_user = e.idx_user
    WHERE id_extension = '.$spolight_extension_id.'
;';
    
    $spotlighted= query2array($query);

    $categories[$i]['spotlighted_extension'] = $spotlighted[0];
}

$template->assign(
  array(
    'CATEGORIES' => $categories
  )
);


