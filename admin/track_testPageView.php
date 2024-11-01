<?php
  if(is_single() || is_page()) {
  	if(count($WPTestMonkey_viewed_variations) > 0) {
  		$hasIPFilter = $wpdb->get_results("SELECT GROUP_CONCAT(ip) as ips FROM ".$wpdb->prefix."WPTMsplittesting_ip_filters");
  		$ipFilters = split(',', $hasIPFilter[0]->ips);
  		if(!in_array($_SERVER['REMOTE_ADDR'], $ipFilters)) {
  			$hasTest = $wpdb->get_results( $wpdb->prepare("SELECT id FROM ".$wpdb->prefix."WPTestMonkey_tests WHERE status = 1 AND testForId=%d", get_the_ID()) );
				if(count($hasTest) > 0) {
					$hasViewd = $wpdb->get_results( $wpdb->prepare("SELECT id FROM ".$wpdb->prefix."WPTestMonkey_variation_views WHERE session_id = %s AND test_id=%d AND testPage_id=%d", session_id(), $hasTest[0]->id, get_the_ID()));
					if(count($hasViewd) == 0) {
						$variationList = $wpdb->get_results($wpdb->prepare(
							"SELECT 
								GROUP_CONCAT(mtmv.id) as variations
							FROM 
							 ".$wpdb->prefix."WPTestMonkey_tests as mtmt,
							 ".$wpdb->prefix."WPTestMonkey_test_elements as mtmte,
							 ".$wpdb->prefix."WPTestMonkey_variations mtmv
							WHERE
								mtmt.id = mtmte.test_id 
							AND 
								mtmv.element_id = mtmte.element_id 
							AND
								mtmt.id = %d 
							GROUP BY mtmt.id"
							, $hasTest[0]->id));
						if(count($variationList) > 0) {
							$variationList = explode(",", $variationList[0]->variations);
							$searchCombination = '[{';
							asort($WPTestMonkey_viewed_variations);
							foreach($WPTestMonkey_viewed_variations as $k => $id) {
								if(in_array($id, $variationList)) {
									if($searchCombination != '[{')
										$searchCombination .= ',';
										$searchCombination .='"'.$id.'":"'.$_SESSION['WPTestMonkey_variation_'.$id.'_name'].'"';
								} else {
									unset($WPTestMonkey_viewed_variations[$k]);
								}
							}
							$searchCombination .= '}]';
							$neededVaruation = $wpdb->get_results($wpdb->prepare("SELECT ".$wpdb->prefix."WPTestMonkey_tests.id as testID, ".$wpdb->prefix."WPTestMonkey_tests.successPageId as successPage, count(".$wpdb->prefix."WPTestMonkey_test_elements.id) as counts FROM ".$wpdb->prefix."WPTestMonkey_tests, ".$wpdb->prefix."WPTestMonkey_test_elements WHERE ".$wpdb->prefix."WPTestMonkey_tests.status = 1 AND ".$wpdb->prefix."WPTestMonkey_test_elements.test_id=".$wpdb->prefix."WPTestMonkey_tests.id AND ".$wpdb->prefix."WPTestMonkey_tests.testForId=%d",get_the_ID()));
							if($neededVaruation[0]->counts == count($WPTestMonkey_viewed_variations)) {
								// Mark test as running
								$wpdb->query( $wpdb->prepare("UPDATE ".$wpdb->prefix."WPTestMonkey_tests SET isInclude = 1, isBroken=0 WHERE status = 1 AND testForId=%d", get_the_ID()) );
								// Update combination view count
								$wpdb->query( $wpdb->prepare("UPDATE ".$wpdb->prefix."WPTestMonkey_combinations SET viewed=viewed+1 WHERE name='%s'", trim($searchCombination)) );
								// Update variation views
								$wpdb->query( $wpdb->prepare("
									INSERT INTO ".$wpdb->prefix."WPTestMonkey_variation_views(test_id, testPage_id, successPage_id, variation_combination, session_id, ip, date)
									VALUES(%d, %d, %d, %s, %s, %s, NOW())", $neededVaruation[0]->testID, get_the_ID(), $neededVaruation[0]->successPage, trim($searchCombination), session_id(), $_SERVER['REMOTE_ADDR'] ) );
							} else {
								// Mark test as broken
								$wpdb->query( $wpdb->prepare("UPDATE ".$wpdb->prefix."WPTestMonkey_tests SET isInclude = 1, isBroken=1 WHERE status = 1 AND testForId=%d", get_the_ID()) );
							}
						}
					}
				}
			}
  	}
  ?>
  <script>
    jQuery(document).ready(function() {
    	var title = document.title;
    	if(title.indexOf('[MTM element=') != -1) {
    		var re1='(\\[MTM element=".*"\\])';
    		var p = new RegExp(re1,["i"]);
    		var m = p.exec(title);
    		if (m != null) {
          title = title.replace(m[1], jQuery('.entry-title').text());
        }
        
    		document.title = title;
    	}
    });
  </script>
  <?php
  }
?>
