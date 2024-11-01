<?php
	// Check if not revision
	if(!wp_is_post_revision($post_id)) {
		// Get active test for that page
		$hasTest = $wpdb->get_results( $wpdb->prepare("SELECT id FROM ".$wpdb->prefix."WPTestMonkey_tests WHERE status = 1 AND testForId=%d", $post_id) );
		if(count($hasTest) > 0) {
			// Get test elements
			$elementList = $wpdb->get_results( $wpdb->prepare("SELECT GROUP_CONCAT(mtmte.element_id) as elements FROM ".$wpdb->prefix."WPTestMonkey_tests as mtmt, ".$wpdb->prefix."WPTestMonkey_test_elements as mtmte WHERE mtmt.id = mtmte.test_id AND mtmt.status = 1 AND mtmte.test_id=%d", $hasTest[0]->id));
			$elementList = split(",", $elementList[0]->elements);
			if($elementList && count($elementList) > 0) {
				$post = get_post($post_id);
				$title = $post->post_title;
				$content = $post->post_content;
				$pattern = get_shortcode_regex();
				preg_match_all("/$pattern/s", $title.":".$content, $matches);
				$shortCodeList = array();
				if($matches && count($matches[0]) > 0) {
					foreach($matches[0] as $shortcode) {
						$shortcodes = shortcode_parse_atts(str_replace("[MTM", "", str_replace("]", "",$shortcode)));
						if($shortcodes['element']) $shortCodeList[] = $shortcodes['element'];
					}
				}
				$isBroken = false;
				$remainElementCount = 0;
				foreach($elementList as $element) {
					if(!in_array($element, $shortCodeList)) {
						$isBroken = true;
						$wpdb->query( $wpdb->prepare("UPDATE ".$wpdb->prefix."WPTestMonkey_test_elements SET isInclude = 0 WHERE element_id=%d", $element) );
						$remainElementCount++;
					} else {
						$wpdb->query( $wpdb->prepare("UPDATE ".$wpdb->prefix."WPTestMonkey_test_elements SET isInclude = 1 WHERE element_id=%d", $element) );
					}
				}
				if($isBroken) {
					$wpdb->query( $wpdb->prepare("UPDATE ".$wpdb->prefix."WPTestMonkey_tests SET isInclude = 1, isBroken=1 WHERE status = 1 AND testForId=%d", $post_id) );
				} else {
					$wpdb->query( $wpdb->prepare("UPDATE ".$wpdb->prefix."WPTestMonkey_tests SET isInclude = 1, isBroken=0 WHERE status = 1 AND testForId=%d", $post_id) );
				}
			}
		}
	}
?>
