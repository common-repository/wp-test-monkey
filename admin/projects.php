<?php
$tab = 'tests';
$tests = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."WPTestMonkey_tests ORDER BY name");
?>
<?php include 'tabs.php' ?>
<div class="wrap">
  <h3>Projects</h3>

  <table class="wp-list-table widefat" cellspacing="0">
  	<thead>
    	<tr>
    		<th>Name</th>
    	</tr>
  	</thead>

  	<tbody>
  	  <?php
  	  foreach ($tests as $test) {
  	    ?>
  			<tr>
  			  <td>
  			    <a href="?page=WPTestMonkey&amp;action=show_test&amp;id=<?php echo $test->id ?>"><strong><?php echo $test->name ?></strong></a><br />
  			    <small>
  			      <a href="?page=WPTestMonkey&amp;action=show_test&amp;id=<?php echo $test->id ?>">Show</a>
  			      |
  			      <a href="?page=WPTestMonkey&amp;action=edit_test&amp;id=<?php echo $test->id ?>">Edit</a>
  			      |
  			      <a href="?page=WPTestMonkey&amp;action=delete_test&amp;id=<?php echo $test->id ?>">Delete</a>
  			  </td>
  			</tr>
  	    <?php
  	  }
  	  ?>
  	</tbody>
  </table>

  <p>
    <input type="button" value="Create new test" class="button-secondary" onclick="document.location = '?page=WPTestMonkey&amp;action=create_test';" />
  </p>
  
</div>