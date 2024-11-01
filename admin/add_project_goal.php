<?php
$tab = 'projects';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $project_id = (int)$_POST['project_id'];
  // Insert the goal
  $name = stripslashes($_POST['name']);
  $wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."WPTestMonkey_project_goals SET project_id=%d, name=%s", $project_id, $name));
  
  redirect_to('?page=WPTestMonkey&action=show_project&id=' . $project_id);
} else {
  $project_id = (int)$_GET['project_id'];
  $name = '';
}
?>

<?php include 'tabs.php' ?>
<div class="wrap">
  <h3>Add goal</h3>
  <form method="post">
    <input type="hidden" name="project_id" value="<?php echo $project_id ?>" />
    <p>
      <label for="name">Goal name:</label><br />
      <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name) ?>" style="width: 300px;" />
    </p>
    <p>
      <input class="button-primary" type="submit" name="Save" value="Add goal" id="submitbutton" />
      or <a href="?page=WPTestMonkey&amp;action=show_experiment&amp;id=<?php echo $project_id ?>">Cancel</a>
    </p>
  </form>
</div>

<script type="text/javascript">
  jQuery('#name').focus();
</script>