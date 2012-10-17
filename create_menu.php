<?php

/* 
 * Uses Custom Menu API: http://drupal.org/project/custom_menu
 * Download with git:
 * git clone --recursive --branch master http://git.drupal.org/project/custom_menu.git
 */
 
$theme = 'asuzen';

//$result = db_query("SELECT nid FROM content_field_landing_links WHERE field_landing_links_url IS NOT NULL GROUP BY nid");
// get nodes that have links in current revision
$result = db_query("Select l.nid, l.vid FROM content_field_landing_links l, node n WHERE l.vid=n.vid AND l.field_landing_links_url IS NOT NULL GROUP BY nid");

while ($row = db_fetch_object($result)) {
  create_page_menu($row->nid, $row->nid . '-menu', $row->nid . '_menu');
  $bid = db_result(db_query("SELECT bid FROM {blocks} WHERE menu_name=''%s' AND theme='%s'", $menu_name, $theme))
  db_query("INSERT INTO {content_field_navbar_menu} SET VALUES (%d, %d, %d)", $row->vid, $row->nid, $bid);
}

//create_page_menu(47, 'hmdp', 'HDMP');


// Validate page is assigned proper menu
// select c.field_navbar_menu_bid from content_field_navbar_menu c, node n where c.vid=n.vid AND c.field_navbar_menu_bid IS NOT NULL AND n.nid=48;


function create_page_menu($nid, $menu_name, $menu_title) {

  $menu = array(
    'title' => $menu_title,
    'description' => '',
    'menu_name' => $menu_name,
  );

  custom_menu_save($menu);
  // Only get links from active revisios 
  $result = db_query("SELECT field_landing_links_url url, field_landing_links_title title FROM {content_field_landing_links} WHERE nid=%d", $nid);


  $weight = 0;
  while ($row = db_fetch_object($result)) {
    // Change absolute URLs on local site to relative
    if (strpos($row->url, 'eoss')) {
      $row->url = rtrim($row->url, '/');
      $path = ltrim(parse_url($row->url, PHP_URL_PATH), '/');
      //echo 'Path: ' . $path . "\n";
      $link_path = db_result(db_query("SELECT src from {url_alias} WHERE dst='%s'",$path));
    }
    else {
      $link_path = $row->url;
      $router_path = NULL;
    }
    //echo 'Link_path: ';
    //var_dump($link_path);
    //echo "\n";
    $item = array(
      'menu_name' => $menu_name,
      'link_title' => $row->title,
      'router_path' => $path,
      'link_path' => $link_path,
      'mlid' => 0,
      'plid' => 0,
      'weight' => $weight,
     );

    echo 'Menu Link';
    //var_dump($item);
    menu_link_save($item);
    $weight++;
  }
}


  // Delete Menu and its links
  //$delete_links = TRUE;
  //custom_menu_delete($menu_name, $delete_links);
  /*
  // Validate if menu exists
  custom_menu_exists($menu_name);

  */
