<?php
$tab = 'projects';

$id = (int)$_GET['id'];
$var = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."WPTestMonkey_projects WHERE id=%d", $id));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Save the project
  $name = stripslashes($_POST['name']);
  
  $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."WPTestMonkey_projects SET name=%s WHERE id=%d", $name, $id));
  
  redirect_to('?page=WPTestMonkey&action=show_project&id=' . $id);
} else {
  $name = $var->name;
  $content = $var->content;
}
?>

<?php include 'tabs.php' ?>
<div class="wrap">
  <h3>Edit project</h3>
  <form method="post">
    <p>
      <label for="name">Project name:</label><br />
      <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name) ?>" style="width: 300px;" />
    </p>
    <p>
      <input class="button-primary" type="submit" name="Save" value="Update project" id="submitbutton" />
      or <a href="?page=WPTestMonkey&amp;action=show_project&amp;id=<?php echo $id ?>">Cancel</a>
    </p>
  </form>
</div>

<script type="text/javascript">
  jQuery('#name').focus();
</script>