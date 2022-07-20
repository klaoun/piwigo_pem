<?php

$template->assign('PEM_PATH', PEM_PATH);

$template->set_filename('list_view_pem', realpath(PEM_PATH . 'template/list_view.tpl'));