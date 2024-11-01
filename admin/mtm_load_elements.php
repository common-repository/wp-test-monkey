<?php
	if(is_single() || is_page()) {
  	$hasTest = $wpdb->get_results( $wpdb->prepare("SELECT id FROM ".$wpdb->prefix."WPTestMonkey_tests WHERE status = 1 AND testForId=%d", get_the_ID()) );
		if(count($hasTest) > 0) {
			$hasViewd = $wpdb->get_results( $wpdb->prepare("SELECT id FROM ".$wpdb->prefix."WPTestMonkey_variation_views WHERE session_id = %s AND test_id=%d AND testPage_id=%d", session_id(), $hasTest[0]->id, get_the_ID()));
		  if(count($hasViewd) == 0) {
		  	$elements = $wpdb->get_results($wpdb->prepare("SELECT 
																		mtmte.element_id,
																		mtmv.id,
																		mtmv.name,
																		mtmv.content
																		FROM 
																		".$wpdb->prefix."WPTestMonkey_tests as mtmt,
																		".$wpdb->prefix."WPTestMonkey_test_elements as mtmte,
																		".$wpdb->prefix."WPTestMonkey_variations as mtmv
																		where 
																		mtmt.status = %d
																		AND mtmt.id = mtmte.test_id
																		AND mtmte.element_id = mtmv.element_id
																		AND mtmt.id = %d
																		AND mtmt.testForId = %d
																		", 1, $hasTest[0]->id, get_the_ID()));
				if($elements && count($elements) > 0) {
					$combinations = $wpdb->get_results($wpdb->prepare("SELECT id, name, nextActive
																			FROM 
																			".$wpdb->prefix."WPTestMonkey_combinations as mtmc
																			where 
																			mtmc.test_id = %d
																			ORDER BY id
																			", $hasTest[0]->id));
					if($combinations && count($combinations) > 1) {
					  //print_r($combinations);
						$doActive = getDoAtiveCombination($combinations);
						//print_r($doActive);
						$nextActiveID = getNextAtiveCombination($doActive, $combinations);
						//echo "a:".$nextActiveID.":bb";
						$eleVariations = getEleVariation($elements, json_decode($doActive[name]));
						//print_r($eleVariations);
						$wpdb->query( $wpdb->prepare("UPDATE ".$wpdb->prefix."WPTestMonkey_combinations SET nextActive=%d WHERE id=%d", 0, (int)$doActive[id]) );
						$wpdb->query( $wpdb->prepare("UPDATE ".$wpdb->prefix."WPTestMonkey_combinations SET nextActive=%d WHERE id=%d", 1, (int)$nextActiveID) );
						foreach ($eleVariations as $elvKey => $eleVariation ) {
							if (!isset($_SESSION['WPTestMonkey_element_'.$eleVariation['ele'].'_variation']) || $_SESSION['WPTestMonkey_debug']) {
								// Get variation
								//Array ( [0] => Array ( [ele] => 27 [id] => 53 [name] => Heading 2 [content] => rejt portjtpre ) ) 
								// Set session
								$_SESSION['WPTestMonkey_element_'.$eleVariation['ele'].'_id'] = $eleVariation['id'];
								$_SESSION['WPTestMonkey_element_'.$eleVariation['ele'].'_variation'] = $eleVariation['content'];
								$_SESSION['WPTestMonkey_element_'.$eleVariation['ele'].'_name'] = $eleVariation['name'];
								$_SESSION['WPTestMonkey_variation_'.$eleVariation['id'].'_name'] = $eleVariation['name'];
								
							}
						}
					}
				}
			}
		}
		//print_r($_SESSION);
	}
	
  function getDoAtiveCombination($combinations) {
  	$doActive = array();
  	$isFirst = true;
  	foreach($combinations as $comKey => $combination) {
  		if($isFirst) {
  			$isFirst = false;
  			$doActive['id'] = $combination->id;
  			$doActive['name'] = $combination->name;
  		}
  		if($combination->nextActive == 1) {
  			$doActive['id'] = $combination->id;
  			$doActive['name'] = $combination->name;
  			break;
  		}
		}
		return $doActive;
  }
  
  function getNextAtiveCombination($doActive, $combinations) {
  	$nextActiveID = 0;
  	$isFirst = true;
  	foreach($combinations as $comKey => $combination) {
  		if($isFirst) {
  			$isFirst = false;
  			$nextActiveID = $combination->id;
  		}
  		if($combination->id > $doActive['id']) {
  			$nextActiveID = $combination->id;
  			break;
  		}
  	}
  	return $nextActiveID;
  }
  
  function getEleVariation($elements, $doActiveCom) {
  	$eleVariations = array();
  	$index = 0;
  	foreach($doActiveCom[0] as $varID => $varName) {
  		foreach($elements as $eleKey => $element) {
  			if(($element->id == $varID) && ($element->name == $varName)) {
					$eleVariations[$index]['ele'] = $element->element_id;
					$eleVariations[$index]['id'] =  $varID;
					$eleVariations[$index]['name'] = $varName;
					$eleVariations[$index]['content'] = $element->content;
					$index++;
					break;
				}
			}
  	}
  	return $eleVariations;
  }
?>