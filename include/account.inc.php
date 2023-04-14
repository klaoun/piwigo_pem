<?php
if (isset($_GET['uId']))
{
  check_status(ACCESS_CLASSIC);

  if (!empty($_POST))
  {
    check_pwg_token();
  }
  
  $current_extension_page_id = $_GET['uId'];

  $template->set_filename('pem_page', realpath(PEM_PATH . 'template/account.tpl'));

  $template->assign(
    array(
    'PEM_PATH' => PEM_PATH,
    )
  );
}
else
{
  http_response_code(404);
  $template->set_filenames(array('pem_page' => realpath(PEM_PATH . 'template/404.tpl')));
}