<?php
$url = 'http'.('https' == $_SERVER['REQUEST_SCHEME'] ? 's' : '').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$url = str_replace('extension_view', 'index', $url);

header('Request-URI: '.$url);
header('Content-Location: '.$url);
header('Location: '.$url);
exit();