<?php
/*
Plugin Name:  WP Test Monkey
Plugin URI:   http://wptestmonkey.com
Version:      1.0
Description:  Easily perform Multivariate Split Testing on any WordPress site.
Author:       Pachow! Marketing
Author URI:   http://pachow.com/
*/

// Start the session
session_start();

// Register hook for plugin activate
register_activation_hook( __FILE__, 'mtm_activate' );
function mtm_activate() {
	require 'includes/install.php';
	WPTestMonkey_install_on_activate();
}

// Register hook for plugin deactivate
register_deactivation_hook( __FILE__, 'mtm_deactivate' );
function mtm_deactivate() {
	require 'includes/uninstall.php';
	WPTestMonkey_uninstall_on_deactivate();
}

// Ensure that widgets can also contain WPTM SplitTesting Test shortcodes
add_filter('the_title', 'do_shortcode');

// Use the jQuery framework
wp_enqueue_script("jquery");

// Set up the viewed variations and hit goals queue
$WPTestMonkey_viewed_variations = array();

// Load elements so they are ready, e.g. if you want to track variables before the variation has been shown
add_action('wp_head', 'WPTestMonkey_load_elements');
function WPTestMonkey_load_elements() {
  global $wpdb;
  //session_destroy();
  include 'admin/mtm_load_elements.php';
}

function WPTestMonkey_element($id) {
  WPTestMonkey_get_element($id);
}

function WPTestMonkey_get_element($id) {
  global $WPTestMonkey_viewed_variations;
  $variation_id = $_SESSION['WPTestMonkey_element_'.$id.'_id'];
	if (!in_array($variation_id, $WPTestMonkey_viewed_variations)) {
		// Add to viewed variations array
		$WPTestMonkey_viewed_variations[] = $variation_id;
	}
	return $_SESSION['WPTestMonkey_element_'.$id.'_variation'];
}

function WPTestMonkey_name($element_id) {
  echo WPTestMonkey_get_name($element_id);
}

function WPTestMonkey_get_name($element_id) {
  return $_SESSION['WPTestMonkey_element_'.$element_id.'_name'];
}

add_action('wp_footer', 'WPTestMonkey_track_view');
function WPTestMonkey_track_view() {
  global $WPTestMonkey_viewed_variations;
  global $wpdb, $post;
  include 'admin/track_testPageView.php';
}

add_action('wp_head', 'WPTestMonkey_ob_start');
function WPTestMonkey_ob_start() {
  if (get_option('WPTestMonkey_do_shortcode_on_output_buffer')) {
    ob_start('WPTestMonkey_ob_callback');
  }
}

add_action('wp_footer', 'WPTestMonkey_ob_end');
function WPTestMonkey_ob_end() {
  if (get_option('WPTestMonkey_do_shortcode_on_output_buffer')) {
    ob_end();
  }
}

function WPTestMonkey_ob_callback($content) {
  return do_shortcode($content);
}

// [MTM element="123"]
add_shortcode( 'MTM', 'WPTestMonkey_handle_shortcode' );
function WPTestMonkey_handle_shortcode($atts) {
  
	extract( shortcode_atts( array(
		'element' => '',
		'variable' => '',
	), $atts ) );
  if ($element != '') {
    if ($variable == 'name') {
    	return WPTestMonkey_get_name($element);
    } else {
    	return WPTestMonkey_get_element($element);
    }
  } else {
    return "Unknown WP Test Monkey";
  }
}

// Processing shortcodes on post save
add_action( 'save_post', 'WPTestMonkey_handle_post_shortcode' );
function WPTestMonkey_handle_post_shortcode($post_id) {
	global $wpdb;
	include 'admin/track_shortrcode_on_save.php';
	return $post_id;
}

// Display message on edit post page
add_action( 'add_meta_boxes', 'WPTestMonkey_message' );  
function WPTestMonkey_message() {
	global $post, $wpdb;
	$hasTest = $wpdb->get_results( $wpdb->prepare("SELECT id FROM ".$wpdb->prefix."WPTestMonkey_tests WHERE status = 1 AND testForId=%d", $post->ID) );
	if(count($hasTest) > 0) {
		if($post->post_type == 'post') {
			add_meta_box( 'mtm_message_box', 'WP Test Monkey', 'show_mtm_message', 'post', 'normal', 'high' );
		} else {
			add_meta_box( 'mtm_message_box', 'WP Test Monkey', 'show_mtm_message', 'page', 'normal', 'high' );
		}
	}
}  

function show_mtm_message() {
	global $post, $wpdb;
  include 'admin/show_mtm_message.php';
}  
 
// Add WP Test Monkey in admin menu
add_action('admin_menu', 'WPTestMonkey_admin_menu');
function WPTestMonkey_admin_menu() {
	global $wpdb;
  add_menu_page('WP Test Monkey', 'WP Test Monkey', 'manage_options', 'WPTestMonkey', 'WPTestMonkey_admin', WP_PLUGIN_URL.'/wp-test-monkey/images/wptm.png');
  
  // Add active test as submenu
  $tests = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."WPTestMonkey_tests where status = 1 ORDER BY createdOn");
  if(count( $tests) > 0) {
  	add_submenu_page('WPTestMonkey', "All tests", "All tests", 'manage_options', 'WPTestMonkey',  'WPTestMonkey_admin');
  	foreach( $tests as $test) {
  		add_submenu_page('WPTestMonkey', $test->name, $test->name, 'manage_options', 'WPTestMonkey_show_test_'.$test->id,  'WPTestMonkey_admin_test');
  	}
  }
}

function WPTestMonkey_admin() {
  global $wpdb;
  include 'WPTestMonkey_admin.php';
}

function WPTestMonkey_admin_test() {
  global $wpdb;
  include 'WPTestMonkey_admin_test.php';
}

// Updating page conversion count
add_action('wp_head', 'WPTestMonkey_track_conversion');
function WPTestMonkey_track_conversion() {
	global $wpdb, $post;
  include 'admin/track_conversion.php';
}

//Action target that adds the "Insert Form" button to the post/page edit screen
add_action('media_buttons_context', 'add_form_multivar_button');

function add_form_multivar_button($context){
	global $wpdb;
	$title = "WPTM Test Plugin";
	$image_btn = WP_PLUGIN_URL. "/wp-test-monkey/images/wptm.png";
	$out = '<a href="#TB_inline?width=480&inlineId=WPTM_short_insert" class="thickbox" id="add_gform" title="Add WPTM Test"><img src="'.$image_btn.'" alt="Add WPTM" /></a>';
	$tesForIds = $wpdb->get_results($wpdb->prepare("SELECT testForId  FROM ".$wpdb->prefix."WPTestMonkey_tests where status = 1"));
	foreach($tesForIds as $tesForId){
		if($tesForId->testForId == $_REQUEST['post']) {
			add_WPTM_popup_content($tesForId->testForId);
			return $context . $out;
		}
	}
	return $context;
}
	
function add_WPTM_popup_content($tesForId) {
	global $wpdb;
	include 'admin/WPTM_popup.php';
}
?>