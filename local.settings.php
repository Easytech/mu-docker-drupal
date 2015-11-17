<?php

ini_set('display_error' , 1);
ini_set('memory_limit', '500M');

$databases = array (
  'default' =>  
  array (
    'default' =>  
    array (
      'database' => 'drupal',
      'username' => 'drupal',
      'password' => 'drupal',
      'host' => 'db',
      'port' => '', 
      'driver' => 'mysql',
      'prefix' => '', 
    ),  
  ),  
);

$conf['memcache_servers'] = array(
  'memcached:11211' => 'default',
);

// Global Condo configuration
$conf['condo_secret_hash'] = '';
$conf['condo_idp_host'] = 'http://condo-ip.easytech.com.ar';

// This is the site ID
// $conf['site_id'] = strtolower(array_shift(explode('.', $_SERVER['HTTP_HOST'])));
$conf['condo_site_id'] = 'ramallo-2920';
$conf['condo_site_id_md5'] = md5( $conf['condo_site_id'] . $conf['condo_secret_hash'] );

$cnd_memcache = new Memcache;
$cnd_memcache->connect('memcached', '11211');
$key = 'condo_' . $conf['condo_site_id_md5'];
$conf['condo_site_info'] = $cnd_memcache->get($key);
if ($conf['condo_site_info'] === FALSE) {
  print("Calling WS...");
  // Get other condo variables
  $conf['condo_site_info'] = array_shift(
    json_decode(
      file_get_contents($conf['condo_idp_host'] . '/api/views/provisioning_service?args=' . $conf['condo_site_id_md5'])
    )
  );
  if (is_object($conf['condo_site_info']) && isset($conf['condo_site_info']->uuid)) {
    $cnd_memcache->set($key, $conf['condo_site_info']);
  }
  else {
    die('Cannot get info for condo: ' . $conf['condo_site_id']);
  }
}
