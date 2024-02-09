<?php

$category_icons =  conf_get_param('categories_icon',array());

$query = '
SELECT
    id_category as cid,
    name
  FROM '.PEM_CAT_TABLE.' 
  ORDER BY cid DESC
;';
$categories = query2array($query, 'cid');

foreach ($categories as $i => $category) {
  //See about l10n the category name
  $categories[$i]['plural_name'] = l10n($category['name'].'s');
  $categories[$i]['icon_class'] = $category_icons[$i];
}

if (is_a_guest())
{
  $account_url = $pem_root_url.'identification.php';
}
else
{
  $account_url = $pem_root_url.'index.php?uid='.$user['id'];
}

$template->assign(
  array(
    'CATEGORIES_INFO' => $categories,
    'ACCOUNT_URL' => $account_url,
    'USER_STATUS' => $user['status'],
    'USER_USERNAME' => $user['username'],
  )
);


$template->set_filenames(array('navbar_pem' => realpath(PEM_PATH .'template/navbar.tpl')));
