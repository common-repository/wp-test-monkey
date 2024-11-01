<?php
$tab = 'projects';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Insert the experiment
  $name = stripslashes($_POST['name']);
  $type = stripslashes($_POST['type']);
  
  $wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."WPTestMonkey_projects SET name=%s, type=%s", $name, $type));
  $id = $wpdb->insert_id; 
  
  // Insert variations
  //$wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."WPTestMonkey_variations SET experiment_id=%d, name=%s", $id, 'Variation 1'));
  //$wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."WPTestMonkey_variations SET experiment_id=%d, name=%s", $id, 'Variation 2'));
  
  // Insert project_goal
  $wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."WPTestMonkey_project_goals SET project_id=%d, name=%s", $id, 'Project Goal 1'));
  
  redirect_to('?page=WPTestMonkey&action=show_project&id=' . $id);
} else {
  $name = '';
}
?>

<?php include 'tabs.php' ?>
<div class="wrap">
  <h3>Create Project</h3>
  <form method="post">
    <p>
      <label for="name">Project name:</label><br />
      <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name) ?>" style="width: 300px;" />
    </p>
    <p>
      <label for="type">Project type:</label><br />
      <select id="type" name="type">
        <option value="content">Content – inserted manually into posts, pages, and widgets</option>
        <option value="stylesheet">Stylesheet – inserted automatically into the stylesheet</option>
        <option value="javascript">Javascript – inserted automatically into the javascript</option>
        <option value="theme">Theme – to switch between themes</option>
      </select>
    </p>
    <p>
      <input class="button-primary" type="submit" name="Save" value="Create Project" id="submitbutton" />
      or <a href="?page=WPTestMonkey">Cancel</a>
    </p>
  </form>
</div>

<script type="text/javascript">
  jQuery('#name').focus();
</script>