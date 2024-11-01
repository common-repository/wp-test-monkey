<?php
$tab = 'tests';
include 'tabs.php' ?>
<div class="wrap">
<?php
$id = (int)$_REQUEST['id'];//test id
$isFIrst = $_REQUEST['is_first'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// Insert the element
	$name = trim(stripslashes($_POST['name']));
	$type = stripslashes($_POST['type']);
	$variation = $_POST['variation'];
	if(strlen($name) > 0) {
		// Mark test as broken
		$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."WPTestMonkey_tests SET isBroken=%d, createdOn=now() WHERE id = %d", 1, $id));
		// Delete variation views for this test
  	$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."WPTestMonkey_variation_views WHERE test_id = %d", $id));
		// creating element
		$wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."WPTestMonkey_elements SET name=%s, type=%s", $name, $type));
		$element_id = (int)$wpdb->insert_id;
		if($element_id != 0) {
			$isVariationExists = false;
			$variationSeqNo = 1;
			// Insert variations
			foreach($variation as $individual_variation_key => $individual_variation) {
				if(strlen(stripslashes(trim(htmlspecialchars($individual_variation)))) > 0) {
					$isVariationExists = true;
					$wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."WPTestMonkey_variations SET element_id=%d, name=%s, content=%s", $element_id, $name.' '.$variationSeqNo, trim(stripslashes($individual_variation))));
					$variationSeqNo++;
				}
			}
			
			if($isVariationExists) {
				// Map element to test
				$wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."WPTestMonkey_test_elements SET test_id=%d, element_id=%d", $id, $element_id));
				
				$test_elements = $wpdb->get_results($wpdb->prepare("SELECT GROUP_CONCAT(element_id) as elements FROM ".$wpdb->prefix."WPTestMonkey_test_elements WHERE  test_id=%d GROUP BY test_id", $id));
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
				$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."WPTestMonkey_combinations WHERE test_id = %d", $id));	
				if(count($results)> 0){
					foreach ($results as $result){ 
						if(in_array(null,$result)) continue;
						foreach ($result as $resultKey => $resultVal){
							if($resultKey > 0) $combinationname .= ",";
							$combinationname .= $resultVal;
						}
						$wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."WPTestMonkey_combinations SET name =%s, test_id=%d", '[{'.trim($combinationname).'}]',(int)$id));
						$combinationname = "";
					} 
				}
				//after submission redirect to show_test Page
				redirect_to('?page=WPTestMonkey_show_test_' . $id);	
			} else {
				 // Deleting created element
				 $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."WPTestMonkey_elements WHERE id=%d", $element_id));
				 echo $mvmessages['error']['variationMissing'];
			}
		} else {
			echo $mvmessages['error']['elementCreateFaild'];
		}
	} else {
		echo $mvmessages['error']['elementNameMissing'];
	}
}  else {
	$name = '';
}
?>

  <?php 
    if(strlen($isFIrst) > 0 && $isFIrst == 'true') {
    	echo $mvmessages['information']['addFirstElementInfo'];
    } else {
  ?>
  <h3>Add a new element to test</h3>
  <?php } ?>
  <form method="post">
    <p>
      <?php if(strlen($isFIrst) > 0 && $isFIrst == 'true') { ?>
      <label for="name">Name (eg: Headline, Price, etc):</label><span class="helpPoint" title="<?php echo $mvmessages['help']['help_12']; ?>"></span><br />
      <?php } else { ?>
      <label for="name">Name:</label><span class="helpPoint" title="<?php echo $mvmessages['help']['help_12']; ?>"></span><br />
      <?php } ?>
      <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name) ?>" style="width: 300px;" />
    </p>
    <p style="display:none;">
      <label for="type">Experiment type:</label><br />
      <select id="type" name="type">
        <option value="content">Content – inserted manually into posts, pages, and widgets</option>
        <option value="stylesheet">Stylesheet – inserted automatically into the stylesheet</option>
        <option value="javascript">Javascript – inserted automatically into the javascript</option>
        <option value="theme">Theme – to switch between themes</option>
      </select>
    </p>
    <p>
      <?php for($vCount = 1; $vCount <= 20; $vCount++) { ?>
      <?php if($vCount < 3) { ?>
      <div class="variations variation<?php echo $vCount; ?>" style="width: 600px; margin-bottom: 20px;">
      <?php } else { ?>
      <div class="variations variation<?php echo $vCount; ?>" style="width: 600px; margin-bottom: 20px; display: none;">
      <?php } ?>
				<label for="content"><b>Variation <?php echo $vCount; ?>:</b><span class="helpPoint" title="<?php echo $mvmessages['help']['help_13']; ?>"></span> <?php if($vCount > 1) { ?><span class="removeVariation" style="cursor: pointer; border-bottom: 1px solid #000; color: blue;">remove</span><?php } ?></label>
				<?php wp_editor( $variation[$vCount], 'variation['.$vCount.']', $settings = array('textarea_rows'=>5) ); ?>
			</div>
			<?php } ?>
		</p>
 		 
	  <p>
      <input type="hidden" name="id" value="<?php echo $id ?>" id="submitbutton" />
      <input class="button-primary" type="submit" name="Save" value="Save Elements & Return to Test" id="submitbutton" />
      or <a href="?page=WPTestMonkey">Cancel</a>
    </p>
  </form>
</div>
 