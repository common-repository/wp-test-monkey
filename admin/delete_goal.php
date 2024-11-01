<?php
$tab = 'experiments';

$id = (int)$_GET['id'];
$var = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."WPTestMonkey_goals WHERE id=%d", $id));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Delete the goal
  $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."WPTestMonkey_goal_hits WHERE goal_id=%d", $id));
  $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."WPTestMonkey_goals WHERE id=%d", $id));
  
  redirect_to('?page=WPTestMonkey&action=show_experiment&id=' . $var->experiment_id);
}
?>

<?php include 'tabs.php' ?>
<div class="wrap">
  <h3>Delete goal</h3>
  <form method="post">
    <p>
      Are you sure you want to delete the goal <em><?php echo htmlspecialchars($var->name) ?></em>? This can't be undone.
    </p>
    <p>
      <input class="button-primary" type="submit" name="Save" value="Delete goal" id="submitbutton" />
      or <a href="?page=WPTestMonkey&amp;action=show_experiment&amp;id=<?php echo $var->experiment_id ?>">Cancel</a>
    </p>
  </form>
</div>