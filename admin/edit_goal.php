<?php
$tab = 'experiments';

$id = (int)$_GET['id'];
$var = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."WPTestMonkey_goals WHERE id=%d", $id));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Save the goal
  $name = stripslashes($_POST['name']);
  
  $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."WPTestMonkey_goals SET name=%s WHERE id=%d", $name, $id));
  
  redirect_to('?page=WPTestMonkey&action=show_experiment&id=' . $var->experiment_id);
} else {
  $name = $var->name;
}
?>

<?php include 'tabs.php' ?>
<div class="wrap">
  <h3>Edit goal</h3>
  <form method="post">
    <p>
      <label for="name">Goal name:</label><br />
      <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name) ?>" style="width: 300px;" />
    </p>
    <p>
      <input class="button-primary" type="submit" name="Save" value="Update goal" id="submitbutton" />
      or <a href="?page=WPTestMonkey&amp;action=show_experiment&amp;id=<?php echo $var->experiment_id ?>">Cancel</a>
    </p>
  </form>
</div>

<script type="text/javascript">
  jQuery('#name').focus();
</script>