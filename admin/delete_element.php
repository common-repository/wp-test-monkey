<?php
$tab = 'tests';

$id = (int)$_GET['id'];
$testId = (int)$_GET['test_id'];
$var = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."WPTestMonkey_elements WHERE id=%d", $id));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// Broken the test
  $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."WPTestMonkey_tests SET isBroken=%d, createdOn=now() WHERE id=%d", 1, $testId));
  // Delete the element
  $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."WPTestMonkey_elements WHERE id=%d", $id));
  // Delete test element association
  $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."WPTestMonkey_test_elements WHERE element_id=%d", $id));
  // Delete element variations
  $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."WPTestMonkey_variations WHERE element_id=%d", $id));
  // delete variation views for this test
  $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."WPTestMonkey_variation_views WHERE test_id = %d", $testId));
  
  // Update Test variation combination
  $test_elements = $wpdb->get_results($wpdb->prepare("SELECT GROUP_CONCAT(element_id) as elements FROM ".$wpdb->prefix."WPTestMonkey_test_elements WHERE  test_id=%d GROUP BY test_id", $testId));
	$listCombinatons = $wpdb->get_results("SELECT * from ".$wpdb->prefix."WPTestMonkey_variations where element_id in (".$test_elements[0]->elements.")");
	$combination=array();
	$combinationJson=array();
	foreach ($listCombinatons as $combinationKey => $listCombinaton){
		$combination[$listCombinaton->element_id][] = '"'.trim($listCombinaton->id).'":"'.trim($listCombinaton->name).'"';
	}
	$serialId = 1;
	$combinationname= "";
	$results = array(array()); 
	foreach ($combination as $arr){
		array_push($arr,null); 
		$new_result = array();
		foreach ($results as $old_element)
			foreach ($arr as $el)
				$new_result []= array_merge($old_element,array($el));
			$results = $new_result;
	}
	
	$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."WPTestMonkey_combinations WHERE test_id = %d", $testId));	
	if(count($results)> 0){
		foreach ($results as $result){ 
			if(in_array(null,$result)) continue;
			foreach ($result as $resultKey => $resultVal){
				if($resultKey > 0) $combinationname .= ",";
				$combinationname .= $resultVal;
			}
			$wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."WPTestMonkey_combinations SET name =%s, test_id=%d", '[{'.trim($combinationname).'}]',(int)$testId));
			$combinationname = "";
		} 
	}
  
  redirect_to('?page=WPTestMonkey_show_test_'. $testId);
}
?>

<?php include 'tabs.php' ?>
<div class="wrap">
  <h3>Delete Element: <?php echo htmlspecialchars($var->name) ?></h3>
  <?php echo $mvmessages['information']['elementDeactivateConfirmation']; ?>
  <form method="post">
    <p>
      <input class="button-primary" type="submit" name="Save" value="Delete" id="submitbutton" />
      or <a href="?page=WPTestMonkey&amp;action=show_test&amp;id=<?php echo $testId; ?>">Cancel</a>
    </p>
  </form>
</div>