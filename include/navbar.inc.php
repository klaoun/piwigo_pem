<?php

$category_icons =  conf_get_param('categories_icon',array());

$query = '
SELECT
    id_category as cId,
    name
  FROM '.PEM_CAT_TABLE.' 
  ORDER BY cId DESC
;';
$categories = query2array($query, 'cId');

foreach ($categories as $i => $category) {
  //See about l10n the category name
  $categories[$i]['plural_name'] = l10n($category['name'].'s');
  $categories[$i]['icon_class'] = $category_icons[$i];
}

$template->assign(
  array(
    'CATEGORIES_INFO' => $categories
  )
);

$template->set_filenames(array('navbar_pem' => realpath(PEM_PATH .'template/navbar.tpl')));