<?php
/**
 * Plugin Name: wppa-all-in-one-wp-migration-hooks-third-party-export
 * Description: Custom hooks for all in one wp migration plugin to export only uploads directory and database.
 * Version: 1.0
 * Author: Erfan Ilyas / PressAgility.com
 */




###
#pressagility:
#This mu-plugin will only export uploads directory and Database.
#please set the following "enabled_all_in_one_wp_migration" constant to true.
###


define( 'enabled_all_in_one_wp_migration', true );
#if you have a license for S3 client plugin set the following constant to true.
define( 'enabled_all_in_one_wp_migration_s3_client_extension', true );


//set your variables here
//you can find these values on your control panel.
define( 'WPPA_CLOUDFLARE_R2_ROOT_DIR_NAME', '' ); //set the root directory. prefixing the directory with _ will help sort the list on the top.
define( 'WPPA_CLOUDFLARE_R2_ENDPOINT', '' );
define( 'WPPA_CLOUDFLARE_R2_BUCKET', '' );
define( 'WPPA_CLOUDFLARE_R2_API_KEY', '' );
define( 'WPPA_CLOUDFLARE_R2_API_VALUE_S3', '' );


  




###
#Run the following when main all-in-one-wp-migration plugin is loaded
###
if( defined('enabled_all_in_one_wp_migration') && enabled_all_in_one_wp_migration ){
}else{
  return;
}





###
#show all-in-one-wp-migration menu only to a superduper user
###
add_action( 'admin_init', function(){
  
  //hide for all users - even for superduper user
  remove_submenu_page( 'ai1wm_export', 'ai1wm_schedules' );
  remove_submenu_page( 'ai1wm_export', 'ai1wm_reset' );
  remove_submenu_page( 'ai1wm_export', 'ai1wmve_reset' );
  remove_submenu_page( 'ai1wm_export', 'ai1wmve_schedules' );
  
  //if you want to show the all in one migration menu to a specific user. Use the following
  //remove_menu_page( 'ai1wm_export' );
  
}, 9999, 1 ); //add_action( 'admin_init', function(){




###
#harden security for non superduper user
###
add_action( 'init', function(){
  
  //if you want to show the all in one migration menu to a specific user. Use the following
  return;
  
	global $pagenow;
	if( !isset($pagenow) ){ //make sure $pageNow is set
		return;
	}
  
  
  $restrictedSubMenu = array(
    'ai1wmne_settings', 'ai1wm_backups', 'ai1wm_import', 'ai1wm_export',
  );

  if( $pagenow == 'admin.php' ){
    if( isset($_GET['page']) ){
			if( in_array($_GET['page'], $restrictedSubMenu) ) {
				wp_redirect( admin_url() );
			}
		}
  }

}, 9999 ); //add_action( 'init', function(){










###
#ovewrite/default settings main plugin - Do not allow to export any content except database and media that is not in yearly format.
###

#Main plugin hook - ai1wm_exclude_themes_from_export
add_filter( 'ai1wm_exclude_themes_from_export', function( $options ){
  
  $allInstalledThemes = wp_get_themes();

	//skip disabled themes
	foreach( $allInstalledThemes as $installedTheme ){
    $options[] = $installedTheme->template;
	}
  
  return $options;
});




#Main plugin hook - ai1wm_exclude_plugins_from_export
add_filter( 'ai1wm_exclude_plugins_from_export', function( $options ){
  
  // Check if get_plugins() function exists. This is required on the front end of the
	// site, since it is in a file that is normally only loaded in the admin.
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
  
	$allInstalledPlugins 	= get_plugins();
  foreach( $allInstalledPlugins as $pluginFullName=>$allInstalledPlugin ){
		$exploded = explode( '/', $pluginFullName );
		$options[] = $exploded[0];
	}
  
  return $options;
});




#Main plugin hook - ai1wm_exclude_content_from_export
add_filter( 'ai1wm_exclude_content_from_export', function( $options ){
  
  //add any other directory we do not need to backup
  $options[] = 'ai1wm-storage';
  $options[] = 'cache';
  $options[] = 'mu-plugins';
  $options[] = 'logs';
  $options[] = 'languages';
  $options[] = 'object-cache.php';

  return $options;
});




#Main plugin hook - ai1wm_exclude_media_from_export
add_filter( 'ai1wm_exclude_media_from_export', function( $options ){
  return $options;
});











###
#Run the following when all-in-one-wp-migration-s3-client-extension is loaded
###
if( defined( 'enabled_all_in_one_wp_migration_s3_client_extension' ) &&  enabled_all_in_one_wp_migration_s3_client_extension ){
  
  ###
  #ovewrite/default settings for s3 client extension
  ###

  //** set how many backups you want to keep at all times.
  add_filter( 'pre_option_ai1wmne_s3_backups', function($default){ return '8'; }); //***
  //**

  add_filter( 'pre_option_ai1wmne_s3_api_endpoint', function($default){ return WPPA_CLOUDFLARE_R2_ENDPOINT; });
  add_filter( 'pre_option_ai1wmne_s3_bucket_template', function($default){ return WPPA_CLOUDFLARE_R2_BUCKET.'.'.WPPA_CLOUDFLARE_R2_ENDPOINT; });
  add_filter( 'pre_option_ai1wmne_s3_region_name', function($default){ return 'auto'; });
  add_filter( 'pre_option_ai1wmne_s3_access_key', function($default){ return WPPA_CLOUDFLARE_R2_API_KEY; });
  add_filter( 'pre_option_ai1wmne_s3_secret_key', function($default){ return WPPA_CLOUDFLARE_R2_API_VALUE_S3; });
  add_filter( 'pre_option_ai1wmne_s3_https_protocol', function($default){ return '1'; });

  add_filter( 'pre_option_ai1wmne_s3_bucket_name', function($default){ return WPPA_CLOUDFLARE_R2_BUCKET; });
  add_filter( 'pre_option_ai1wmne_s3_folder_name', function($default){ return WPPA_CLOUDFLARE_R2_ROOT_DIR_NAME; });
  add_filter( 'pre_option_ai1wmne_s3_storage_class', function($default){ return 'STANDARD'; });
  add_filter( 'pre_option_ai1wmne_s3_encryption', function($default){ return '0'; });
  add_filter( 'pre_option_ai1wmne_s3_cron', function($default){ return array(); });
  add_filter( 'pre_option_ai1wmne_s3_incremental', function($default){ return '0'; });
  add_filter( 'pre_option_ai1wmne_s3_notify_toggle', function($default){ return '0'; });
  add_filter( 'pre_option_ai1wmne_s3_notify_error_toggle', function($default){ return '0'; });
  add_filter( 'pre_option_ai1wmne_s3_notify_email', function($default){ return ''; });
  add_filter( 'pre_option_ai1wmne_s3_days', function($default){ return '0'; });
  add_filter( 'pre_option_ai1wmne_s3_total', function($default){ return '0'; });
  add_filter( 'pre_option_ai1wmne_s3_total_unit', function($default){ return 'GB'; });
  add_filter( 'pre_option_ai1wmne_s3_file_chunk_size', function($default){ return '20971520'; });
  
 
} //if( REQUIRED_FILE_all_in_one_wp_migration_s3_client_extension ){