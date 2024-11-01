<?php
$tab = 'settings';

$filters = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."WPTestMonkey_ip_filters");
?>
<?php include 'tabs.php' ?>
<div class="wrap">
  <h3>IP filters</h3>
  
  <p>
    Here you can set up IP filters, e.g. to <strong>disable tracking</strong> for your own IP. Data will still be tracked but won't appear in measurements or statistics.
  </p>

  <table class="wp-list-table widefat" cellspacing="0">
  	<thead>
    	<tr>
    		<th>Description</th>
    		<th>IP</th>
    	</tr>
  	</thead>

  	<tbody>
  	  <?php
  	  if (count($filters) > 0) {
    	  foreach ($filters as $filter) {
    	    ?>
    			<tr>
    			  <td>
    			    <a href="?page=WPTestMonkey&amp;action=edit_ip_filter&amp;id=<?php echo $filter->id ?>"><strong><?php echo $filter->description ?></strong></a><br />
    			    <small>
    			      <a href="?page=WPTestMonkey&amp;action=edit_ip_filter&amp;id=<?php echo $filter->id ?>">Edit</a>
    			      |
    			      <a href="?page=WPTestMonkey&amp;action=delete_ip_filter&amp;id=<?php echo $filter->id ?>">Delete</a>
    			  </td>
    			  <td>
    			    <?php echo $filter->ip ?>
    			  </td>
    			</tr>
    	    <?php
    	  }
  	  } else {
  	    ?>
  	    <tr>
  	      <td colspan="2">You have no filters.</td>
  	    </tr>
  	    <?php
  	  }
  	  ?>
  	</tbody>
  </table>

  <p>
    <input type="button" value="Add an IP filter" class="button-secondary" onclick="document.location = '?page=WPTestMonkey&amp;action=add_ip_filter';" />
  </p>
  
  <p>
    Normally, when a user first sees a variation to an element, this variation is locked for this user so that – in this session – she always sees the same variation.<br />
    If you want to test your elements without this variation lock taking place for you (and only you), you can enable debug mode. Also, when entering debug mode, all tracking will be disabled for your session.
  </p>
  <?php
  if ($_SESSION['WPTestMonkey_debug']) {
    ?>
    <p>
      <strong>Debug mode is on.</strong>
    </p>
    <p>
      <input type="button" value="Exit debug mode" class="button-secondary" onclick="document.location = '?page=WPTestMonkey&amp;action=set_debug_mode&amp;debug=0';" />
    </p>
    <?php
  } else {
    ?>
    <p>
      <input type="button" value="Enter debug mode" class="button-secondary" onclick="document.location = '?page=WPTestMonkey&amp;action=set_debug_mode&amp;debug=1';" />
    </p>
    <?php
  }
  ?>
</div>