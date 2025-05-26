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
  
  
  $request_uri = strtok($_SERVER['REQUEST_URI'], '?');

  // Strip query strings
  $request_uri = strtok($_SERVER['REQUEST_URI'], '?');

  // Check if request matches the loader.io token
  if ($request_uri === '/' . $allowedSiteIdsArray[$selectedKey]['loaderiokey']) {
      header('Content-Type: text/html');
      echo $allowedSiteIdsArray[$selectedKey]['loaderiokey'];
      exit;
  }
    
    
    
});