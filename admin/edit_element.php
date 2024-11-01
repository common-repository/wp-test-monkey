<?php
$tab = 'tests';
include 'tabs.php';
?>
<div class="wrap">
<?php
$element_id = (int)$_REQUEST['id'];
$id = (int)$_REQUEST['test_id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  // Insert the element
  $name = trim(stripslashes($_POST['name']));
  $type = stripslashes($_POST['type']);
  $variation = $_POST['variation'];
  if(strlen($name) > 0) {
  	$isVariationExists = false;
		foreach($variation as $individual_variation_key => $individual_variation) {
			if(strlen(stripslashes(trim(htmlspecialchars($individual_variation)))) > 0) {
				$isVariationExists = true;
				break;
			}
		}
		if($isVariationExists) {
			// Mark test as broken
			$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."WPTestMonkey_tests SET isBroken=%d, createdOn=now() WHERE id = %d", 1, $id));
			// delete variation views for this test
			$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."WPTestMonkey_variation_views WHERE test_id = %d", $id));
			
			// Update Element details
			$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."WPTestMonkey_elements SET name=%s, type=%s WHERE id = %d", $name, $type,$element_id));
	
			//delete all variations
			$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."WPTestMonkey_variations WHERE ".$wpdb->prefix."WPTestMonkey_variations.element_id = %d", $element_id));
			// Insert variations
			$variationSeqNo = 1;
			foreach($variation as $individual_variation_key => $individual_variation) {
				if(strlen(stripslashes(trim(htmlspecialchars($individual_variation)))) > 0) {
					$wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."WPTestMonkey_variations SET element_id=%d, name=%s, content=%s", $element_id, $name.' '.$variationSeqNo, trim(stripslashes($individual_variation))));
					$variationSeqNo++;
				}
			}
		  
			// Cfreating combination with existing variations
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
					echo $serialId;
					$wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."WPTestMonkey_combinations SET name =%s, test_id=%d", '[{'.trim($combinationname).'}]',(int)$id));
					$combinationname = "";
				} 
			}
			
			redirect_to('?page=WPTestMonkey_show_test_' . $id);
		} else {
			echo $mvmessages['error']['variationMissing'];
		}
	} else {
		echo $mvmessages['error']['elementNameMissing'];
	}
} else {
	$name = '';
	$elementName = $wpdb->get_results("SELECT name from ".$wpdb->prefix."WPTestMonkey_elements where id = ". $element_id);
	$listAllVariant = $wpdb->get_results("SELECT * from ".$wpdb->prefix."WPTestMonkey_variations where element_id = ". $element_id);
	$name = $elementName[0]->name;
}
?>

  <h3>Edit element</h3>
  <form method="post">
    <p>
      <label for="name">Name:</label><span class="helpPoint" title="<?php echo $mvmessages['help']['help_12']; ?>"></span><br />
      <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name) ?>" style="width: 300px;" />
    </p>
    <p>
      <label for="type"  style = 'display:none'>Experiment type:</label><br />
      <select id="type" name="type" style = 'display:none'>
        <option value="content">Content – inserted manually into posts, pages, and widgets</option>
        <option value="stylesheet">Stylesheet – inserted automatically into the stylesheet</option>
        <option value="javascript">Javascript – inserted automatically into the javascript</option>
        <option value="theme">Theme – to switch between themes</option>
      </select>
    </p>
    <p>
      <?php 
        if($listAllVariant && count($listAllVariant) > 0) {
					foreach($listAllVariant as $variantKey=>$variantData):?>
					<div class="variations variation<?php echo $variantKey+1; ?>" style="width: 600px; margin-bottom: 20px;">
						<label for="content"><b>Variation <?php echo $variantKey+1?></b><span class="helpPoint" title="<?php echo $mvmessages['help']['help_13']; ?>"></span> <?php if($variantKey > 0) { ?><span class="removeVariation" style="cursor: pointer; border-bottom: 1px solid #000; color: blue;">remove</span><?php } ?></label><br />
						<?php 
						wp_editor($variantData->content, 'variation['.($variantKey+1).']', $settings = array('textarea_rows'=>5) );
						?>
					</div>
				<?php 
						endforeach;
			 ?>
			 	  <div class="variations variation<?php echo $variantKey+2; ?>" style="width: 600px; margin-bottom: 20px;">
						<label for="content"><b>Variation <?php echo $variantKey+2; ?></b><span class="helpPoint" title="<?php echo $mvmessages['help']['help_13']; ?>"></span> <span class="removeVariation" style="cursor: pointer; border-bottom: 1px solid #000; color: blue;">remove</span> </label><br />
						<?php 
						wp_editor('', 'variation['.($variantKey+2).']', $settings = array('textarea_rows'=>5) );
						?>
					</div>
			 <?php
					} else {
						$variantKey = 0;
				?>
			  	<div class="variations variation<?php echo $variantKey+1; ?>" style="width: 600px; margin-bottom: 20px;">
						<label for="content"><b>Variation <?php echo $variantKey+1; ?></b> </label><br />
						<?php 
						wp_editor('', 'variation['.($variantKey+1).']', $settings = array('textarea_rows'=>5) );
						?>
					</div>
			<?php } ?>
			<?php if($variantKey < 10) { ?>
				<?php for($vCount = $variantKey+3; $vCount <= 20; $vCount++) { ?>
					<div class="variations variation<?php echo $vCount; ?>" style="width: 600px; margin-bottom: 20px;  display: none;">
						<label for="content"><b>Variation <?php echo $vCount; ?></b><span class="helpPoint" title="<?php echo $mvmessages['help']['help_13']; ?>"></span> <span class="removeVariation" style="cursor: pointer; border-bottom: 1px solid #000; color: blue;">remove</span></label>
						<?php wp_editor( $variation[$vCount], 'variation['.$vCount.']', $settings = array('textarea_rows'=>5) ); ?>
					</div>
				<?php } ?>
				<script type="text/javascript">
				  var testValueCount = <?php echo $variantKey+2; ?>;
				</script>
				<p>
					<span id="moreTestValue" style="border-bottom: 1px solid #000000; cursor: pointer; clear: both;">+ add another test value</span>
				</p>
      <?php } ?>
    </p>    
    <p>
		 <input type="hidden" name="id" value="<?php echo $element_id ?>" id="submitbutton" />
		 <input class="button-primary" type="submit" name="Save" value="Save Elements & Return to Test" id="submitbutton" />
		 or <a href="?page=WPTestMonkey&action=show_test&id=<?php echo $id; ?>">Cancel</a>
		</p>
  </form>
</div>

<script type="text/javascript">
  jQuery(document).ready(function() {
  	jQuery('#name').focus();
  	var variationInPage = testValueCount;
		jQuery('#moreTestValue').click(function() {
			testValueCount++;
			variationInPage++;
			jQuery('.variation'+testValueCount).css('display', 'block');
			if(variationInPage == 10) jQuery(this).css('display', 'none');
		});
		jQuery('.removeVariation').click(function() { jQuery(this).closest('.variations').remove(); variationInPage--; });
	});
</script>