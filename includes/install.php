<?php
function WPTestMonkey_install_on_activate() {
	global $wpdb;

  $migs = WPTestMonkey_install();
  $needed_migration = count($migs);

	for ($i = 0; $i < $needed_migration; $i++) {
		$mig = $migs[$i];
		$wpdb->query($mig);
	}
  
  add_option('WPTestMonkey_current_migration', $needed_migration);
}

//Create Tables
function WPTestMonkey_install() {
  global $wpdb;
  $migs = array();
  // Create tests table
  $migs[] = '
  CREATE TABLE IF NOT EXISTS '.$wpdb->prefix.'WPTestMonkey_tests (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `testFor` varchar(11) DEFAULT "post",
  `testForId` int(11) NOT NULL,
  `successPageId` int(11) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `status` tinyint(1) DEFAULT "0",
  `createdOn` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lastUpdated` timestamp NULL DEFAULT NULL,
  `isInclude` tinyint(1) DEFAULT "0",
  `isBroken` tinyint(1) DEFAULT "0",
  PRIMARY KEY (`id`)
  )';
  
  // Create tests element mapping table
  $migs[] = '
  CREATE TABLE IF NOT EXISTS '.$wpdb->prefix.'WPTestMonkey_test_elements (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`test_id` int(11) NOT NULL,
		`element_id` int(11) NOT NULL,
		`isInclude` TINYINT NOT NULL DEFAULT "0",
		PRIMARY KEY (`id`),
		KEY `test_id` (`test_id`),
		KEY `element_id` (`element_id`)
	)';
	
  // Create elements table
  $migs[] = '
  CREATE TABLE IF NOT EXISTS '.$wpdb->prefix.'WPTestMonkey_elements (
    id int(11) NOT NULL AUTO_INCREMENT,
    name varchar(255) NOT NULL,
    type VARCHAR( 50 ) NOT NULL DEFAULT "content",
    PRIMARY KEY (id)
  )';
  
  // Create variations table
  $migs[] = '
  CREATE TABLE IF NOT EXISTS '.$wpdb->prefix.'WPTestMonkey_variations (
    id int(11) NOT NULL AUTO_INCREMENT,
    element_id int(11) NOT NULL,
    name varchar(255) NOT NULL,
    content text NOT NULL,    
    active BOOLEAN NOT NULL DEFAULT "1",
    PRIMARY KEY (id),
    KEY element_id (element_id)
  )';
  
  // Create variation views table
  $migs[] = '
  CREATE TABLE IF NOT EXISTS '.$wpdb->prefix.'WPTestMonkey_variation_views (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`test_id` bigint(11) NOT NULL,
		`testPage_id` bigint(20) NOT NULL,
		`successPage_id` bigint(20) NOT NULL,
		`variation_combination` text,
		`session_id` varchar(40) NOT NULL,
		`ip` varchar(15) NOT NULL,
		 `isSuccess` TINYINT NOT NULL DEFAULT "0",
		`date` datetime NOT NULL,
		PRIMARY KEY (`id`),
		KEY `variation_id` (`test_id`,`session_id`)
	)';
  
  //create Combinations Table
  $migs[] = '
  CREATE TABLE IF NOT EXISTS '.$wpdb->prefix.'WPTestMonkey_combinations (
		id int(11) NOT NULL AUTO_INCREMENT,
		test_id int(11) NOT NULL,
		name varchar(250) NOT NULL,
		viewed int(11) NOT NULL DEFAULT "0",
		conversionCount int(11) NOT NULL DEFAULT "0",
		conversionEarned int(11) NOT NULL DEFAULT "0",
		`nextActive` TINYINT NOT NULL DEFAULT "0",
		createdOn timestamp NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		KEY test_id (test_id)
	)';
	  // Create IP filters table
  $migs[] = '
  CREATE TABLE IF NOT EXISTS '.$wpdb->prefix.'WPTestMonkey_ip_filters (
    id int(11) NOT NULL AUTO_INCREMENT,
    ip varchar(15) NOT NULL,
    description varchar(255) NOT NULL,
    PRIMARY KEY (id),
    KEY ip (ip)
  )';
  
  // Return the migrations
  return $migs;
}
?>