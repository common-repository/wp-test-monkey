<?php
$tab = 'projects';

$id = (int)$_GET['id'];
$goal = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."WPTestMonkey_project_goals WHERE id=%d", $id));
?>
<?php include 'tabs.php' ?>
<div class="wrap">
  <h3>Tracking code</h3>
  <p>
    Here's information on how to track the goal <strong><?php echo $goal->name ?></strong>.
  </p>
  
  <h3 style="margin-bottom: 5px;">To setup a goal page</h3>
  <p style="margin: 0;">
    If you have a <strong>destination page, post or widget</strong> for your experiment – like a product page, an order confirmation page etc. – insert the following code:<br />
  </p>
  <p style="margin-top: 5px;">
    <code>[WPTestMonkey projectgoal="<?php echo $id ?>"]</code>
  </p>
    <!--
  <h3 style="margin-bottom: 5px;">To track a link</h3>
  <p style="margin: 0;">
    If you want to track <strong>each time a user clicks on a link</strong>, insert a code on the link like this:<br />
  </p>

  <p style="margin-top: 5px;">
    <code>&lt;a href="http://www.google.com" <strong>onclick="WPTestMonkey.trackGoal(<?php echo $id ?>, this);"</strong>&gt;Go to Google&lt;/a&gt;</code>
  </p>
  
  <h3 style="margin-bottom: 5px;">To track manually</h3>
  <p style="margin: 0;">
    If you want to track the goal <strong>manually using JavaScript</strong>, insert the following code:<br />
  </p>
  <p style="margin-top: 5px;">
    <code>
      &lt;script type="text/javascript"&gt;<br />
      &nbsp;&nbsp;<strong>WPTestMonkey.trackGoal(<?php echo $id ?>);</strong><br />
      &lt;/script&gt;
    </code>
  </p>
  -->
  <p>
    &laquo; <a href="?page=WPTestMonkey&amp;action=show_project&amp;id=<?php echo $goal->project_id ?>">Back to Project</a>
  </p>
</div>