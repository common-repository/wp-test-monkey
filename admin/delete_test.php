<?php
$tab = 'tests';

$id = (int)$_GET['id'];
$var = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."WPTestMonkey_tests WHERE id=%d", $id));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Deactivate the test
  $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."WPTestMonkey_tests SET status=%d WHERE id=%d", 0, $id));
  redirect_to('?page=WPTestMonkey&action=tests');
}
?>

<?php include 'tabs.php' ?>
<div class="wrap">
  <h3>Delete Test: <?php  echo htmlspecialchars($var->name) ?></h3>
  <?php echo $mvmessages['information']['testDeactivateConfirmation']; ?>
  <form method="post">
    <p>
      <input class="button-primary" type="submit" name="Save" value="Deactivate" id="submitbutton" />
      or <a href="?page=WPTestMonkey&amp;action=tests">Cancel</a>
    </p>
  </form>
</div>