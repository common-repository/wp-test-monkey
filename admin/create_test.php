<?php $tab = 'tests'; ?>
<?php include 'tabs.php' ?>
<div class="wrap">
<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Insert the element
  $name = trim(stripslashes($_POST['name']));
  $description = trim(stripslashes($_POST['description']));
  if(strlen($name) > 0) {
  	if(strlen($description) > 0) {
			$type = stripslashes($_POST['testType']);
			$testPage = split("[|]", $_POST['testForId']);
			$testPageId = (int)$testPage[0];
			if($testPageId != 0) {
				$testPageType = $testPage[1];
				$successPage = split("[|]", $_POST['successPageId']);
				$successPageId = (int)$successPage[0];
				$successPagetestPageType = $successPage[1];
				if($testPageId != $successPageId) {
					$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."WPTestMonkey_tests SET status=%d where testForId = %d",0, $testPageId));
					
					$wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."WPTestMonkey_tests SET name='%s', description='%s', testForId=%d, testFor=%s, successPageId=%d, type=%s, status = 1",$name, $description, $testPageId, $testPageType, $successPageId, $type));
					$id = (int)$wpdb->insert_id; 
					
					if($id != 0) {
						// On success redirect to add first element
						redirect_to('?page=WPTestMonkey&action=create_element&is_first=true&id=' . $id);
					} else {
						echo $mvmessages['error']['testCreateFaild'];
					}
				} else {
					echo $mvmessages['error']['testPageSuccessPageSame'];
				}
			} else {
				echo $mvmessages['error']['testPageMissing'];
			}
		} else {
			echo $mvmessages['error']['testDescriptionMissing'];
		}
	} else {
  	echo $mvmessages['error']['testNameMissing'];
  }
} else {
  $name = '';
}
?>

  <h3>Add New Test</h3>
  <?php echo $mvmessages['information']['addTestHeadInfo']; ?>
  <form method="post">
    <p>
      <label for="name">Test name:</label><span class="helpPoint" title="<?php echo $mvmessages['help']['help_7']; ?>"></span><br />
      <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name) ?>" style="width: 300px;" />
    </p>
    <p>
      <label for="name">Test description:</label><span class="helpPoint" title="<?php echo $mvmessages['help']['help_8']; ?>"></span><br />
      <textarea id="description" name="description" cols="40"><?php echo htmlspecialchars($description) ?></textarea>
    </p>
    <?php
      // Fetch wordpress pages
      $querystr = "SELECT $wpdb->posts.* FROM $wpdb->posts WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'page'  ORDER BY $wpdb->posts.post_title ASC";
		  $testPages = $wpdb->get_results($wpdb->prepare($querystr)); 
		  // Fetch wordpress posts
		  $querystr = "SELECT $wpdb->posts.* FROM $wpdb->posts WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'post' ORDER BY $wpdb->posts.post_title ASC";
		  $testPosts = $wpdb->get_results($wpdb->prepare($querystr)); 
		  // Fetching pages have active tests
		  $querystr = "SELECT GROUP_CONCAT(testForId) as testIDs FROM ".$wpdb->prefix."WPTestMonkey_tests WHERE status = 1";
		  $actveTestPages = $wpdb->get_results($wpdb->prepare($querystr)); 
		  $actveTestPages = explode(",", $actveTestPages[0]->testIDs);
		?>
    <p>
      <label for="name">Page being tested:</label><span class="helpPoint" title="<?php echo $mvmessages['help']['help_9']; ?>"></span><br />
			<select id="testPage" name="testForId">
				<option value="0|"> -- Choose a page for testing-- </option>
				<?php if(count($testPages) > 0) { ?>
					<option value="" disabled></option>
					<optgroup label="Pages">
						<?php foreach($testPages as $testPage):?>
						<?php if(!in_array($testPage->ID, $actveTestPages)) { ?>
						<option value="<?php echo $testPage->ID.'|'. $testPage->post_type ?>"><?php echo $testPage->post_title; ?></option>
						<?php } endforeach;?>
					</optgroup>
				<?php } ?>
				<?php if(count($testPosts) > 0) { ?>
					<option value="" disabled></option>
					<optgroup label="Posts">
						<?php foreach($testPosts as $testPost):?>
						<?php if(!in_array($testPost->ID, $actveTestPages)) { ?>
						<option value="<?php echo $testPost->ID.'|'. $testPost->post_type ?>"><?php echo $testPost->post_title; ?></option>
					  <?php } endforeach;?>
					</optgroup>
				<?php } ?>
      </select>
    </p>
    <p>
			<label for="name">Success page:</label><span class="helpPoint" title="<?php echo $mvmessages['help']['help_10']; ?>"></span><br />
			<select id="successPage" name="successPageId">
				<option value="0|"> -- Choose a page for success-- </option>
				<?php if(count($testPages) > 0) { ?>
					<optgroup label="Pages">
						<?php foreach($testPages as $testPage):?>
						<?php if(!in_array($testPage->ID, $actveTestPages)) { ?>
						<option value="<?php echo $testPage->ID.'|'. $testPage->post_type ?>"><?php echo $testPage->post_title; ?></option>
						<?php } endforeach;?>
					</optgroup>
				<?php } ?>
				<?php if(count($testPosts) > 0) { ?>
					<option value="" disabled></option>
					<optgroup label="Posts">
						<?php foreach($testPosts as $testPost):?>
						<?php if(!in_array($testPost->ID, $actveTestPages)) { ?>
						<option value="<?php echo $testPost->ID.'|'. $testPost->post_type ?>"><?php echo $testPost->post_title; ?></option>
					  <?php } endforeach;?>
					</optgroup>
				<?php } ?>
			</select>
    </p>
    <script>
      jQuery(document).ready(function() {
        jQuery('#successPage').change(function(e) {
          if(jQuery(this).val() != '') {
          	if(jQuery('#testPage').val() != '') {
          		if(jQuery(this).val() == jQuery('#testPage').val()) jQuery(this).val('0|');
          	}
          }
        });
        jQuery('#testPage').change(function(e) {
          if(jQuery(this).val() != '') {
          	if(jQuery('#successPage').val() != '') {
          		if(jQuery(this).val() == jQuery('#successPage').val()) jQuery(this).val('0|');
          	}
          }
        });
      });
    </script>
    <div style='display:none'>
    <p>
			<label for="type">Test type:</label><span class="helpPoint" title="<?php echo $mvmessages['help']['help_11']; ?>"></span><br />
			  <select id="testType" name="testType">
			    <?php
			      $typeCount = 1;
			      foreach($mvmessages['testTypes'] as $typeKey => $typeVal) {
			      	if($typeCount == 1) echo '<option value="'.$typeKey.'">'.$typeVal.'</option>';
			      	else echo '<option disabled value="'.$typeKey.'">'.$typeVal.'</option>';
			      	$typeCount++;
			      }
			    ?>
			  </select>
    </p>
    </div>
    <p>
      <input class="button-primary" type="submit" name="Save" value="Create Test" id="submitbutton" />
      or <a href="?page=WPTestMonkey">Cancel</a>
    </p>
  </form>
</div>

<script type="text/javascript">
  jQuery('#name').focus();
</script>
 