<?php
$tab = 'tests';

$id = (int)$_GET['id'];
$proj = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."WPTestMonkey_tests WHERE id=%d", $id));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Save the test
  $name = trim(stripslashes($_POST['name']));
  $successPage = split("[|]", $_POST['successPageId']);
  $successPageId = (int)$successPage[0];
	$successPagetestPageType = $successPage[1];
  if(strlen($name) > 0) {
  	$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."WPTestMonkey_tests SET name=%s, successPageId=%d WHERE id=%d", $name, $successPageId, $id));
  	redirect_to('?page=WPTestMonkey&action=tests');
  } else {
  	echo $mvmessages['error']['testNameMissing'];
  }
} else {
  $name = $proj->name;
  $content = $proj->content;
}
?>

<?php include 'tabs.php' ?>
<div class="wrap">
  <h3>Edit test</h3>
  <form method="post">
    <p>
      <label for="name">Test name:</label><br />
      <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name) ?>" style="width: 300px;" />
    </p>
    <?php
      // Fetch wordpress page and posts
      $querystr = "SELECT $wpdb->posts.* FROM $wpdb->posts WHERE $wpdb->posts.post_status = 'publish'  ORDER BY $wpdb->posts.post_title ASC";
		  $testPages = $wpdb->get_results($wpdb->prepare($querystr)); 
		?>
    <p>
      <b>Page being tested: </b>
			<?php foreach($testPages as $testPage):
				if($testPage->ID == $proj->testForId) { 
					echo $testPage->post_title.' - '. $testPage->post_type;
				} endforeach;
			?>
    </p>
    <p>
			<b>Success URL:</b>
			<select id="successPage" name="successPageId">
				<option value="0|"> -- Choose a page for testing-- </option>
				<?php foreach($testPages as $testPage):
					if($testPage->ID == $proj->successPageId) { 
				?>
				<option selected value="<?php echo $testPage->ID.'|'. $testPage->post_type ?>"><?php echo $testPage->post_title.' - '. $testPage->post_type; ?></option>
				<?php } else { ?>
				<option value="<?php echo $testPage->ID.'|'. $testPage->post_type ?>"><?php echo $testPage->post_title.' - '. $testPage->post_type; ?></option>
				<?php } endforeach;?>
			</select>
    </p>
    <p>
      <b>Type: </b><?php echo $proj->type; ?>
    </p>
    <p>
      <input class="button-primary" type="submit" name="Save" value="Update test" id="submitbutton" />
      or <a href="?page=WPTestMonkey&amp;action=tests">Cancel</a>
    </p>
  </form>
</div>

<script type="text/javascript">
  jQuery('#name').focus();
</script>