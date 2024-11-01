<?php
$tab = 'settings';

$id = (int)$_GET['id'];
$filter = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."WPTestMonkey_ip_filters WHERE id=%d", $id));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Delete the ip_filter
  $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."WPTestMonkey_ip_filters WHERE id=%d", $id));
  
  redirect_to('?page=WPTestMonkey&action=settings');
}
?>

<?php include 'tabs.php' ?>
<div class="wrap">
  <h3>Delete IP filter</h3>
  <form method="post">
    <p>
      Are you sure you want to delete the IP filter <em><?php echo htmlspecialchars($filter->description) ?></em>? This can't be undone.
    </p>
    <p>
      <input class="button-primary" type="submit" value="Delete IP filter" id="submitbutton" />
      or <a href="?page=WPTestMonkey&amp;action=settings">Cancel</a>
    </p>
  </form>
</div>