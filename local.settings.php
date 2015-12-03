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
  // Get other condo variables
  $ws_response = file_get_contents($conf['condo_idp_host'] . '/api/views/provisioning_service?args=' . $conf['condo_site_id_md5']);
  if (! empty($ws_response) ) {
      $ws_response = json_decode( $ws_response );
  }
  $conf['condo_site_info'] = array_shift( $ws_response );
  if (is_object($conf['condo_site_info']) && isset($conf['condo_site_info']->uuid)) {
    $cnd_memcache->set($key, $conf['condo_site_info'], MEMCACHE_COMPRESSED, 60);
  }
  else {
    die('Cannot get info for condo: ' . $conf['condo_site_id']);
  }
}

// Amazon S3 configuration
// -----------------------------------------------------------------------------
$conf['s3fs_awssdk2_access_key'] = 'AKIAIYSKPW7OREHDAHNA';
$conf['s3fs_awssdk2_secret_key'] = '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';
$conf['s3fs_awssdk2_default_cache_config'] = '';
$conf['s3fs_awssdk2_use_instance_profile'] = 0;
$conf['s3fs_bucket'] = $conf['condo_site_info']->amz_bucket;
$conf['s3fs_cache_control_header'] = '';
$conf['s3fs_domain'] = '';
$conf['s3fs_encryption'] = '';
$conf['s3fs_hostname'] = '';
$conf['s3fs_ignore_cache'] = 0;
$conf['s3fs_no_rewrite_cssjs'] = 0;
$conf['s3fs_presigned_urls'] = '';
$conf['s3fs_region'] = '';
//$conf['s3fs_root_folder'] = $conf['condo_site_id_md5'];
$conf['s3fs_root_folder'] = $conf['condo_site_id'];
$conf['s3fs_saveas'] = '';
$conf['s3fs_torrents'] = '';
$conf['s3fs_use_cname'] = 0;
$conf['s3fs_use_customhost'] = 0;
$conf['s3fs_use_https'] = 1;
$conf['s3fs_use_s3_for_private'] = 1;
$conf['s3fs_use_s3_for_public'] = 1;
