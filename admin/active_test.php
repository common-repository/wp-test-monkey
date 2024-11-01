<?php
$tab = 'tests';

$id = (int)$_GET['id'];
$pageId = (int)$_GET['page_id'];
$var = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."WPTestMonkey_tests WHERE status = 1 AND testForId=%d", $pageId));
include 'tabs.php';
if($var) {
?>
	<div class="wrap">
		<h3><?php  echo htmlspecialchars($var->name) ?></h3>
		<?php echo $mvmessages['warnning']['testActivationNotAllowd']; ?>
		<p><a href="?page=WPTestMonkey&amp;action=tests">Back</a></p>
	</div>
<?php
} else {
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		// Deactivate the test
		$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."WPTestMonkey_tests SET status=%d WHERE id=%d", 1, $id));
		redirect_to('?page=WPTestMonkey&action=tests');
	}
	?>
	
	
	<div class="wrap">
		<h3>Active Test: <?php  echo htmlspecialchars($var->name) ?></h3>
		<?php echo $mvmessages['information']['testActivateConfirmation']; ?>
		<form method="post">
			<p>
				<input class="button-primary" type="submit" name="Save" value="Active" id="submitbutton" />
				or <a href="?page=WPTestMonkey&amp;action=tests">Cancel</a>
			</p>
		</form>
	</div>
<?php } ?>
