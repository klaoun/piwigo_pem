<?php
/**
 * List view display page with list of extensions
 * For example the different category pages
 */

if (isset($_GET['cId']))
{
  $current_category_page_id = $_GET['cId'];

// Get count of extensions
  $query = '
SELECT
    idx_category AS cId,
    COUNT(*) AS count
  FROM '.PEM_EXT_CAT_TABLE.'
  WHERE idx_category  = '.$current_category_page_id.'
  GROUP BY idx_category
;';

  $nb_ext_of_category = query2array($query, 'cId', 'count');

  // Get list of extension ids for this category
  $query = '
  SELECT
      idx_category AS cId,
      idx_extension
    FROM '.PEM_EXT_CAT_TABLE.'
    WHERE idx_category  = '.$current_category_page_id.'
  ;';

  $extensions_ids = query2array($query, null, 'idx_extension');

  // Get category name with Id and count
  $query = '
SELECT
      id_category as cId,
      name
  FROM '.PEM_CAT_TABLE.'
    WHERE id_category  = '.$current_category_page_id.'
;';
      
  $current_category_page_info = query2array($query);
  $current_category_page_info = $current_category_page_info[0];

  $current_category_page_info['extension_count'] = $nb_ext_of_category[$current_category_page_id];

  $current_category_page_info['name_plural_EN'] = $current_category_page_info['name'].'s';

  //Get spotlighted extension, exclude languages
  $pem_spotlight_extensions_ids = conf_get_param('pem_spotlight_extensions',array());

  if ( 8 != $current_category_page_id)
  {
    $query = '
SELECT
    id_extension AS eId,
    name,
    description
  FROM '.PEM_EXT_TABLE.'
  WHERE id_extension = ('.$pem_spotlight_extensions_ids[$current_category_page_id].')
;';
    
    $result= query2array($query);
    
    $current_category_page_info['spotlight_extension'] = $result[0];

    //Get screenshot info
    $screenshot_infos = get_extension_screenshot_infos(
      $pem_spotlight_extensions_ids[$current_category_page_id]
    );

    !empty($screenshot_infos) ? $current_category_page_info['spotlight_extension']['screenshot'] = $screenshot_infos['screenshot_url']: null;
    
  }

  //Get List of authors for filter
  $query = '
SELECT DISTINCT
    aT.idx_user as uId,
    uT.username,
    ecT.idx_category as cId,
    aT.idx_extension as eId
  FROM '.PEM_AUTHORS_TABLE.' as aT
    JOIN '.USERS_TABLE.' as uT on id = aT.idx_user
    JOIN '.PEM_EXT_CAT_TABLE.' as ecT on ecT.idx_extension = aT.idx_extension
  WHERE ecT.idx_category = '.$current_category_page_id.'
;';
  $authors= query2array($query, 'uId');

  //Get List of authors for filter
  $query = '
SELECT 
    id_version,
    version
  FROM '.PEM_VER_TABLE.'
  ORDER BY id_version DESC
;';
  $versions= query2array($query, 'id_version');

  //Get List of tags for filter, 
  // Get specific tags for this category
  $query = '
  SELECT 
      etT.idx_extension as eId,
      pT.id_tag as tId,
      pT.name
    FROM '.PEM_EXT_TAG_TABLE.' as etT
      JOIN '.PEM_TAG_TABLE.' as pT on pT.id_tag = etT.idx_tag
    WHERE etT.idx_extension IN ('.implode(',', $extensions_ids).')
    ORDER BY name ASC
  ;';

  $tags = query2array($query, 'tId');

  $template->set_filename('pem_page', realpath(PEM_PATH . 'template/list_view.tpl'));

  // Check if on languages page and hide spotlighted
  if (8 != $_GET['cId'])
  {
    $template->assign(
      array(
      'SPOTLIGHTED' => true,
      )
    );
  }

  $template->assign(
    array(
    'PEM_PATH' => PEM_PATH,
    'CATEGORY' => $current_category_page_info,
    'AUTHORS' => $authors,
    'VERSIONS' => $versions,
    'TAGS' => $tags,
    )
  );
  
}
else
{
  http_response_code(404);
  $template->set_filenames(array('pem_page' => realpath(PEM_PATH . 'template/404.tpl')));
}