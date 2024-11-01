<?php 
 $listPostElements = $wpdb->get_results($wpdb->prepare(
		"SELECT 
		mtmte.element_id as element,
		mtmte.isInclude as isInclude
		FROM 
		".$wpdb->prefix."WPTestMonkey_tests as mtmt
		LEFT OUTER JOIN ".$wpdb->prefix."WPTestMonkey_test_elements as mtmte on mtmte.test_id  = mtmt.id
		WHERE 
		mtmt.status = 1
		AND
		mtmt.testForId = %d
		", $post->ID));

	$notIncludeCount = 0;
	if($listPostElements && count($listPostElements) > 0) {
		foreach($listPostElements as $listPostElement) {
			if($listPostElement->isInclude == 0) $notIncludeCount++;
		}
	}
	if($notIncludeCount == 0) {
		echo 'You have successfully inserted all test elements into the page.';
	} else if($notIncludeCount == 1) { 
		echo '<span style="color: red;">You still have 1 more element to insert before the test can begin.</span>';
	} else {
		echo '<span style="color: red;">You still have '.$notIncludeCount.' more elements to insert before the test can begin.</span>';
	}
?>
