<?php

// Convert webfm redirects to redirects using path_redirect module

if (!module_exists('path_redirect')) {
  echo "\n\n Path Redirect Module must be enabled.";
  die();
}

$result = db_query("SELECT fid, fpath FROM webfm_file");
while ($row = db_fetch_object($result)) {

  $alias = '/webfm/' . $row->fid;
  $path = $row->fpath;

  $redirect = array(
    'source' => $alias,
    'redirect' => $path,
  );
  path_redirect_save($redirect);
}



