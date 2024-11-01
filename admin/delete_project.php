<?php
$tab = 'projects';

$id = (int)$_GET['id'];
$var = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."WPTestMonkey_projects WHERE id=%d", $id));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Delete the experiment
  /*
  $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."WPTestMonkey_goal_hits WHERE goal_id IN (SELECT id FROM ".$wpdb->prefix."WPTestMonkey_goals WHERE experiment_id=%d)", $id));*/
  $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."WPTestMonkey_project_goals WHERE project_id=%d", $id));
  /*
  $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."WPTestMonkey_variation_views WHERE variation_id IN (SELECT id FROM ".$wpdb->prefix."WPTestMonkey_variations WHERE experiment_id=%d)", $id));
  $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."WPTestMonkey_variations WHERE experiment_id=%d", $id));
  
  $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."WPTestMonkey_experiments WHERE id=%d", $id));
*/
  redirect_to('?page=WPTestMonkey');
}
?>

<?php include 'tabs.php' ?>
<div class="wrap">
  <h3>Delete Project</h3>
  <form method="post">
    <p>
      Are you sure you want to delete the Project <em><?php echo htmlspecialchars($var->name) ?></em>? This can't be undone.
    </p>
    <p>
      <input class="button-primary" type="submit" name="Save" value="Delete Project" id="submitbutton" />
      or <a href="?page=WPTestMonkey&amp;action=show_project&amp;id=<?php echo $var->id ?>">Cancel</a>
    </p>
  </form>
</div>