<?php
  if(is_single() || is_page()) {
  	$hasIPFilter = $wpdb->get_results("SELECT GROUP_CONCAT(ip) as ips FROM ".$wpdb->prefix."WPTMsplittesting_ip_filters");
		$ipFilters = split(',', $hasIPFilter[0]->ips);
		if(!in_array($_SERVER['REMOTE_ADDR'], $ipFilters)) {
			$hasTest = $wpdb->get_results( $wpdb->prepare("SELECT id FROM ".$wpdb->prefix."WPTestMonkey_tests WHERE status = 1 AND successPageId=%d", get_the_ID()) );
			if(count($hasTest) > 0) {
				$views = $wpdb->get_results( $wpdb->prepare("SELECT 
					                        mtmvv.id as id, mtmvv.test_id as test_id, mtmvv.testPage_id as testPage_id, variation_combination
					                        FROM 
					                        ".$wpdb->prefix."WPTestMonkey_tests as mtmt,
					                        ".$wpdb->prefix."WPTestMonkey_variation_views as mtmvv 
					                        WHERE 
					                        mtmt.id = mtmvv.test_id  
					                        AND mtmt.status = 1
					                        AND mtmvv.isSuccess = 0 
					                        AND mtmvv.session_id = %s 
					                        AND mtmvv.successPage_id=%d"
					                        , session_id(), get_the_ID()));
				if(count($views) > 0) {
					foreach($views as $viewKey => $viewValue) {
						$wpdb->query( $wpdb->prepare("UPDATE ".$wpdb->prefix."WPTestMonkey_combinations SET conversionCount=conversionCount+1 WHERE test_id=%d AND name='%s'", $viewValue->test_id, stripslashes($viewValue->variation_combination)));
						$wpdb->query( $wpdb->prepare("UPDATE ".$wpdb->prefix."WPTestMonkey_variation_views SET isSuccess=%d WHERE test_id=%d AND testPage_id=%d AND successPage_id=%d AND session_id =%s", 1, $viewValue->test_id, $viewValue->testPage_id, get_the_ID(), session_id()));
					}
				}
			}
		}
	}
?>
