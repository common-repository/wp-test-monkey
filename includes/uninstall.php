<?php
function WPTestMonkey_uninstall_on_deactivate() {
	global $wpdb;

  $migs = WPTestMonkey_uninstall();
  $needed_migration = count($migs);

	for ($i = 0; $i < $needed_migration; $i++) {
		$mig = $migs[$i];
		$wpdb->query($mig);
	}
	
	delete_option('WPTestMonkey_current_migration');
}

//Drop Tables
function WPTestMonkey_uninstall() {
  global $wpdb;
  $migs = array();
  // Create tests table
  $migs[] = '
  DROP TABLE IF EXISTS '.$wpdb->prefix.'WPTestMonkey_tests';
  
  // Create tests element mapping table
  $migs[] = '
  DROP TABLE IF EXISTS '.$wpdb->prefix.'WPTestMonkey_test_elements';
		
  // Create elements table
  $migs[] = '
  DROP TABLE IF EXISTS '.$wpdb->prefix.'WPTestMonkey_elements';
  
  // Create variations table
  $migs[] = '
  DROP TABLE IF EXISTS '.$wpdb->prefix.'WPTestMonkey_variations';
  
  // Create variation views table
  $migs[] = '
  DROP TABLE IF EXISTS '.$wpdb->prefix.'WPTestMonkey_variation_views';
  
  //create Combinations Table
  $migs[] = '
  DROP TABLE IF EXISTS '.$wpdb->prefix.'WPTestMonkey_combinations';
		
	// Create IP filters table
  $migs[] = '
  DROP TABLE IF EXISTS '.$wpdb->prefix.'WPTestMonkey_ip_filters';
  
  // Return the migrations
  return $migs;
}
?>
