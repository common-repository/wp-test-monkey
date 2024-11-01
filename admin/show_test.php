<?php
$tab = 'tests';
$wpdb->show_errors();
if(isset($_GET['id'])) {
	$id = (int)$_GET['id'];
} else if (isset($_GET['page'])) {
	$slugPart = split('_', $_GET['page']);
	$id =  (int)$slugPart[count($slugPart) - 1];
} else {
	redirect_to('?page=WPTestMonkey');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// Updating test name
	if($_POST['Savetestname']) {
		$name = trim(stripslashes($_POST['name']));
		if(strlen($name) > 0) {
			$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."WPTestMonkey_tests SET name=%s, createdOn=now() WHERE id=%d", $name, $id));
		} else {
			echo $mvmessages['error']['testNameMissing'];
		}
	} else {
		// Updating conversion Earned
		$conversionEarned = $_POST['conversionEarned'];
		foreach($conversionEarned as $conversionEarned_key => $conversionEarned_value) {
			$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."WPTestMonkey_combinations SET conversionEarned=%d WHERE id = %d", (int)$conversionEarned_value, (int)$conversionEarned_key));
		}
	}
}

$proj  = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."WPTestMonkey_tests WHERE id=%d", $id));

$listAllExperientWithVariant = $wpdb->get_results($wpdb->prepare(
"SELECT count(".$wpdb->prefix."WPTestMonkey_variations.id) as variation_count,
".$wpdb->prefix."WPTestMonkey_elements.id,
".$wpdb->prefix."WPTestMonkey_elements.name as elements,
".$wpdb->prefix."WPTestMonkey_test_elements.isInclude as isInclude
FROM 
".$wpdb->prefix."WPTestMonkey_test_elements
LEFT OUTER JOIN ".$wpdb->prefix."WPTestMonkey_elements on ".$wpdb->prefix."WPTestMonkey_test_elements.element_id  = ".$wpdb->prefix."WPTestMonkey_elements.id
LEFT OUTER JOIN ".$wpdb->prefix."WPTestMonkey_variations on ".$wpdb->prefix."WPTestMonkey_test_elements.element_id = ".$wpdb->prefix."WPTestMonkey_variations.element_id
WHERE 
".$wpdb->prefix."WPTestMonkey_test_elements.test_id = %d
GROUP BY ".$wpdb->prefix."WPTestMonkey_variations.element_id
", $id));

$listAllVariations = $wpdb->get_results($wpdb->prepare(
"SELECT 
".$wpdb->prefix."WPTestMonkey_variations.id as id,
".$wpdb->prefix."WPTestMonkey_variations.content as variation
FROM 
".$wpdb->prefix."WPTestMonkey_test_elements
LEFT OUTER JOIN ".$wpdb->prefix."WPTestMonkey_elements on ".$wpdb->prefix."WPTestMonkey_test_elements.element_id  = ".$wpdb->prefix."WPTestMonkey_elements.id 
LEFT OUTER JOIN ".$wpdb->prefix."WPTestMonkey_variations on ".$wpdb->prefix."WPTestMonkey_test_elements.element_id = ".$wpdb->prefix."WPTestMonkey_variations.element_id
WHERE 
".$wpdb->prefix."WPTestMonkey_test_elements.test_id = %d
", $id));
?>
<?php include 'tabs.php' ?>

<?
if(isset($_GET['id'])) {
	$id = (int)$_GET['id'];
} else if (isset($_GET['page'])) {
	$slugPart = split('_', $_GET['page']);
	$id =  (int)$slugPart[count($slugPart) - 1];
} else {
	redirect_to('?page=WPTestMonkey');
}
?>

<style>
.clear { clear: both; }
table {border:1px}
</style>
<div class="wrap">
  <h3>
    <span style="float: left; font-weight: bold; margin-right: 5px; font-size: 15px;">Editing test: </span>
    <span id="testName" style="cursor: pointer;"><?php echo $proj->name ?></span>
    <span id="testNameEdit" style="display: none; float: left;">
      <form method="post">
      	<input type="test" name="name" value="<?php echo $proj->name; ?>" />
      	<input class="button-primary" type="submit" name="Savetestname" value="Save" id="submitbutton" />
     </form>
   </span>
  </h3>
  <div style="clear: both;"></div>
  <script>
    jQuery(document).ready(function() {
      jQuery('#testName').click(function() {
      	jQuery(this).hide();
      	jQuery('#testNameEdit').show();
      });
    });
  </script>
  
    <?php
      // Fetch wordpress page and posts
      $querystr = "SELECT $wpdb->posts.* FROM $wpdb->posts WHERE $wpdb->posts.post_status = 'publish'  ORDER BY $wpdb->posts.post_title ASC";
		  $testPages = $wpdb->get_results($wpdb->prepare($querystr)); 
		?>
		<p>
		  <label style="width: 120px; display: block; float: left;">Page being tested:</label>&nbsp;&nbsp;&nbsp;
			<?php foreach($testPages as $testPage):
				if($testPage->ID == $proj->testForId) { 
					echo '<a target="_blank" href="'.get_site_url().'?p='. $proj->testForId .'">'.$testPage->post_title.' - '. $testPage->post_type.'</a>';
				} endforeach;
			?>
		</p>
		<p>
		  <label style="width: 120px; display: block; float: left;">Success page:</label>&nbsp;&nbsp;&nbsp;
			<?php foreach($testPages as $testPage):
				if($testPage->ID == $proj->successPageId) { 
					echo '<a target="_blank" href="'.get_site_url().'?p='. $proj->successPageId .'">'.$testPage->post_title.' - '. $testPage->post_type.'</a>';
				} endforeach;
			?>
		</p>
		<p>
			<label style="width: 120px; display: block; float: left;">Test type:</label>&nbsp;&nbsp;
      <?php echo $mvmessages['testTypes'][$proj->type]; ?>
    </p>
    <?php $variationCountForOne = 0; if(count($listAllExperientWithVariant) > 0) { ?>
		 <h3>This test contains the following elements<span class="helpPoint" title="<?php echo $mvmessages['help']['help_1']; ?>"></span></h3>
		 <table class="wp-list-table widefat" cellspacing="0">
			<thead>
				<tr>
					<th>Element</th>
					<th>Variations to test<span class="helpPoint" title="<?php echo $mvmessages['help']['help_14']; ?>"></span></th>
					<th>Short Code<span class="helpPoint" title="<?php echo $mvmessages['help']['help_2']; ?>"></span></th>
					<th style="text-align:center;">Element on page<span class="helpPoint" title="<?php echo $mvmessages['help']['help_3']; ?>"></span></th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
				</tr>
			</thead>
	
			<tbody>
				<?php foreach ($listAllExperientWithVariant as $listElement) { ?>
					<tr>
						<td>
							<strong><?php echo $listElement->elements ?></strong><br/>
						</td>
						<td>          
							<?php $variationCountForOne = $listElement->variation_count; echo $listElement->variation_count; ?>
						</td>
						<td>          
							<?php echo '[MTM element="'.$listElement->id.'"]' ?>
						</td>
						<td align="center">
						  <?php if($listElement->isInclude == 0) { ?>
						  	<img src="<?php echo WP_PLUGIN_URL. '/wp-test-monkey/images/notinclude.jpg'; ?>" />
						  <?php } else { ?>
						  	<img src="<?php echo WP_PLUGIN_URL. '/wp-test-monkey/images/include.jpg'; ?>" />
						  <?php } ?>
						</td>
						<td>   
							<a href="?page=WPTestMonkey&amp;action=edit_element&amp;id=<?php echo $listElement->id ?>&amp;test_id=<?php echo $id; ?>">Edit</a>
						</td>          
						<td>   
							<a href="?page=WPTestMonkey&amp;action=delete_element&amp;id=<?php echo $listElement->id ?>&amp;test_id=<?php echo $id; ?>">Delete</a>       
						</td>
					</tr>
				<?php } ?>
				<?php if(count($listAllExperientWithVariant) == 1) { if($variationCountForOne == 1) { ?>
				<tr>
					<td colspan=6 style="color: red;">
						<?php echo str_replace('[%elid%]', $listAllExperientWithVariant[0]->id, str_replace('[%id%]', $proj->id, $mvmessages['warnning']['testHaveOnlyOneElement'])); ?>
					</td>
				</tr>
				<?php } } ?>
			</tbody>
		</table>
	 <?php 
	    $listAllCombinations = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."WPTestMonkey_combinations WHERE test_id=%d ORDER BY id", $id));
	 ?>
	 <p>
	 	<a href="?page=WPTestMonkey&amp;action=create_element&amp;id=<?php echo $proj->id ?>" style="color: #c00;">Add an element to test</a>
	 	<span class="helpPoint" title="<?php echo $mvmessages['help']['help_4']; ?>"></span>
	 	&nbsp;&nbsp;
	 	<a href="post.php?post=<?php echo $proj->testForId; ?>&action=edit" style="color: #c00;">Add these elements to your page</a>
	 	<span class="helpPoint" title="<?php echo $mvmessages['help']['help_5']; ?>"></span>
	 </p>
	 <?php
	  } else {
	  	echo $mvmessages['information']['createElementWhenNoElement'];
	?>
	<p>
  <a href="?page=WPTestMonkey&amp;action=create_element&amp;is_first=true&amp;id=<?php echo $proj->id ?>" style="color: #c00;">Add an element to test</a>
  <span class="helpPoint" title="<?php echo $mvmessages['help']['help_6']; ?>"></span>
  </p>
  <?php } ?>
  <?php if(count($listAllCombinations) > 0) { ?>
   <form method="post">
		 <div>
			<h3>Combinations that will be tested<span class="helpPoint" title="<?php echo $mvmessages['help']['help_7']; ?>"></span></h3>
			<?php 
				$countElement = json_decode($listAllCombinations[0]->name,true);
				$countElement = count($countElement[0]);
			?>
			<?php if($countElement>0):?>
			<table class="wp-list-table widefat" cellspacing="0">	
				<thead>
					<tr>
						<th>#</th>
						<?php 
							for($i = 0; $i<$countElement;$i++ ) {
								echo '<th> Element'.($i+1).'</th>';
							}
						?>
						<th style="text-align:center;">Views</th>
						<th style="text-align:center;">Conversions</th>
						<th style="text-align:center;">Conversion Goal Amount?</th>
					</tr>
				</thead>
				<tbody>
					<?php if(count($listAllCombinations)> 0) {
					$serialNo =1;
					foreach ($listAllCombinations as $listAllCombination) { 
					?>		
							<tr>
								<td class="col-serial"><?php echo $serialNo++?></td>
								<?php 
								 $combinationElement = json_decode($listAllCombination->name,true);
								 foreach ($combinationElement[0] as $variationID => $listName):
								?> 
								<td class="col-variation" title="<?php echo getVariationContent($listAllVariations, $variationID); ?>">
								<?php echo $listName?>
								</td>
								<?php endforeach;?> 
								<td align="center" class="col-views"><?php echo $listAllCombination->viewed; ?></td>
								<td align="center" class="col-goalhit"><?php echo $listAllCombination->conversionCount; ?></td>
								<td align="center">$ <input style="width: 80px;" type="text" name="conversionEarned[<?php echo $listAllCombination->id; ?>]" value="<?php echo $listAllCombination->conversionEarned; ?>" /></td>
							</tr>	
					<?php   } 
					}?>
				</tbody>
			</table>
			<?php endif;?>
	  </div>
	  <p>
      <input class="button-primary" type="submit" name="Save" value="Save" id="submitbutton" />
    </p>
	</form>
  <?php } ?>
</div>

<?php

function getVariationContent($listAllVariations, $variationID) {
	if(count($listAllVariations) > 0) {
		foreach($listAllVariations as $listAllVariation) {
			if($listAllVariation->id == $variationID) return strip_tags($listAllVariation->variation);
		}
	}
	
}
?>