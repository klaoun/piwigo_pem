<?php
// +-----------------------------------------------------------------------+
// |                               functions                               |
// +-----------------------------------------------------------------------+

function do_error($code, $str) {
  set_status_header($code);
  echo $str;
  exit();
}

// +-----------------------------------------------------------------------+
// |                           Common includes                             |
// +-----------------------------------------------------------------------+

define('INTERNAL', true);
define('PHPWG_ROOT_PATH','../../');
require_once(PHPWG_ROOT_PATH.'include/common.inc.php');

// +-----------------------------------------------------------------------+
// |                             Input checks                              |
// +-----------------------------------------------------------------------+

$page['revision_id'] = null;

if (isset($_GET['rid'])) {
  if (is_numeric($_GET['rid'])) {
    $page['revision_id'] = abs(intval($_GET['rid']));
  }
  else {
    do_error(400, 'Invalid request, revision id must be numeric');
  }
}
elseif (isset($_GET['eid'])) {
  if (!is_numeric($_GET['eid'])) {
    do_error(400, 'Invalid request, extension id must be numeric');
  }

  if (isset($_GET['version'])) {
    $version_id_of = array_flip(get_version_name_of());
    if (isset($version_id_of[ $_GET['version'] ])) {
      $version = $version_id_of[ $_GET['version'] ];
    }
    else {
      do_error(400, 'Invalid request, this version does not exist');
    }

    $get_rid_query = '
SELECT
    MAX(id_revision)
  FROM '.PEM_REV_TABLE.'
    JOIN '.PEM_COMP_TABLE.' c ON c.idx_revision = id_revision
  WHERE idx_extension = '.$_GET['eid'].'
    AND idx_version = '.$version.'
;';
  }
  else {
    // we provide the most recent revision of the extension, that is
    // compatible the given version
    $get_rid_query = '
SELECT
    MAX(id_revision)
  FROM '.PEM_REV_TABLE.'
  WHERE idx_extension = '.$_GET['eid'].'
;';
  }

  list($page['revision_id']) = pwg_db_fetch_row(pwg_query($get_rid_query));
}
else {
  do_error(400, 'Invalid request, missing revision id');
}

if (empty($page['revision_id'])) {
  do_error(400, 'Invalid request, no revision matches your request');
}
$revision_infos_of = get_revision_infos_of(array($page['revision_id']));

if (count($revision_infos_of) == 0)
{
  do_error(404, 'Requested revision id not found');
}

// +-----------------------------------------------------------------------+
// |                                 Log                                   |
// +-----------------------------------------------------------------------+

log_download($page['revision_id']);

// +-----------------------------------------------------------------------+
// |                         HTTP response headers                         |
// +-----------------------------------------------------------------------+

$revision_infos = $revision_infos_of[ $page['revision_id'] ];

$file = get_revision_src(
  $revision_infos['idx_extension'],
  $page['revision_id'],
  $revision_infos['url']
  );

if (!@is_readable($file)) {
  do_error(404, "Requested file not readable - $file");
}

$gmt_mtime = gmdate('D, d M Y H:i:s', filemtime($file)).' GMT';

$content_types = array(
  'zip' => 'application/zip',
  'jar' => 'application/java-archive',
);

$http_headers = array(
  'Content-Length: '.@filesize($file),
  'Last-Modified: '.$gmt_mtime,
  'Content-Type: '.$content_types[ pathinfo($file, PATHINFO_EXTENSION) ],
  'Content-Disposition: attachment; filename="'.basename($file).'";',
  'Content-Transfer-Encoding: binary',
  );

foreach ($http_headers as $header) {
  header($header);
}

// +-----------------------------------------------------------------------+
// |                   HTTP response content : raw file                    |
// +-----------------------------------------------------------------------+

@readfile($file);
?>
