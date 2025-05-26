<?php
/**
 * Plugin Name: Loader.io HTML Verification
 * Description: Serve loader.io verification HTML without needing physical files.
 * Version: 1.0
 * Author: Erfan Ilyas / PressAgility.com
 */
 
 
add_action('init', function () {
  
  //set your variables here
  $allowedSiteIdsArray = [
    ['siteID'=>'', 'loaderiokey'=>''],
  ];

  $allowToRun = false;
  $selectedKey = false;

  foreach( $allowedSiteIdsArray as $key=>$siteData ){
    if( WPPA_SITE_ID === $siteData['siteID'] ){
      $selectedKey = $key;
      $allowToRun = true;
      break;
    }
  }


  if( !$allowToRun ){
    return; //just return.
  }
  
  
  // Strip query strings and Normalize URI: remove query string and trailing slash
  $request_uri = strtok($_SERVER['REQUEST_URI'], '?');
  $request_uri = rtrim($request_uri, '/');

  // Check if request matches the loader.io token
  if ($request_uri === '/' . $allowedSiteIdsArray[$selectedKey]['loaderiokey']) {
      header('Content-Type: text/html');
      echo $allowedSiteIdsArray[$selectedKey]['loaderiokey'];
      exit;
  }
    
    
    
});