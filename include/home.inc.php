<?php

$query = '
SELECT
    idx_category,
    COUNT(*) AS count
  FROM '.PEM_EXT_CAT_TABLE.'
  GROUP BY idx_category
;';
  $nb_ext_of_category = query2array($query, 'idx_category', 'count');

//TODO revert to query above for language
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
  foreach ($categories as $i => $category) {
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
  }

$template->assign(
  array(
    'CATEGORIES' => $categories
  )
);

