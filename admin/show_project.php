<?php
$tab = 'projects';
$wpdb->show_errors();
$id = (int)$_GET['id'];
$exp = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."WPTestMonkey_projects WHERE id=%d", $id));
$variation_stats = $wpdb->get_results($wpdb->prepare("
  SELECT *,
  (SELECT COUNT(DISTINCT session_id) FROM ".$wpdb->prefix."WPTestMonkey_variation_views WHERE variation_id=".$wpdb->prefix."WPTestMonkey_variations.id AND ip NOT IN (SELECT ip FROM ".$wpdb->prefix."WPTestMonkey_ip_filters)) AS views,
  (SELECT COUNT(DISTINCT ".$wpdb->prefix."WPTestMonkey_variation_views.session_id)
   FROM ".$wpdb->prefix."WPTestMonkey_variation_views
   INNER JOIN ".$wpdb->prefix."WPTestMonkey_goal_hits ON ".$wpdb->prefix."WPTestMonkey_goal_hits.session_id=".$wpdb->prefix."WPTestMonkey_variation_views.session_id
   INNER JOIN ".$wpdb->prefix."WPTestMonkey_goals ON ".$wpdb->prefix."WPTestMonkey_goals.id=".$wpdb->prefix."WPTestMonkey_goal_hits.goal_id
   WHERE ".$wpdb->prefix."WPTestMonkey_variation_views.variation_id=".$wpdb->prefix."WPTestMonkey_variations.id AND ".$wpdb->prefix."WPTestMonkey_goals.experiment_id=".$wpdb->prefix."WPTestMonkey_variations.experiment_id AND ".$wpdb->prefix."WPTestMonkey_variation_views.ip NOT IN (SELECT ip FROM ".$wpdb->prefix."WPTestMonkey_ip_filters)
  ) AS goal_hits
  FROM ".$wpdb->prefix."WPTestMonkey_variations
  WHERE experiment_id=%d
  ORDER BY name
", $id));

$experiments = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."WPTestMonkey_experiments"));
//$project_expreriments = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."WPTestMonkey_project_experiments WHERE  project_id=%d", $id));
$goals = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."WPTestMonkey_project_goals WHERE project_id=%d", $id));
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."WPTestMonkey_project_experiments WHERE project_id=%d", $id));
	foreach( $_POST['experiment_id'] as  $experimentId ){
		if($experimentId == -1) { continue; }
		$wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."WPTestMonkey_project_experiments SET project_id=%d, experiment_id=%d", $id, (int)trim($experimentId)));
	 /* $experiment_id = (int)$_POST['experiment_id'];
	  $name = stripslashes($_POST['name']);
	  $wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."WPTestMonkey_goals SET experiment_id=%d, name=%s", $experiment_id, $name));
	  
	  redirect_to('?page=WPTestMonkey&action=show_experiment&id=' . $experiment_id);*/
	}
}
$project_expreriments = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."WPTestMonkey_project_experiments WHERE  project_id=%d", $id));
$listCombinatons = $wpdb->get_results($wpdb->prepare(
	"SELECT ".$wpdb->prefix."WPTestMonkey_project_experiments.experiment_id,".$wpdb->prefix."WPTestMonkey_variations.active,".$wpdb->prefix."WPTestMonkey_variations.name,".$wpdb->prefix."WPTestMonkey_variations.id as SID
	from
	".$wpdb->prefix."WPTestMonkey_variations,".$wpdb->prefix."WPTestMonkey_experiments , ".$wpdb->prefix."WPTestMonkey_project_experiments 
	where 
	".$wpdb->prefix."WPTestMonkey_project_experiments.experiment_id = ".$wpdb->prefix."WPTestMonkey_experiments.id 
	and 
	".$wpdb->prefix."WPTestMonkey_variations.experiment_id = ".$wpdb->prefix."WPTestMonkey_project_experiments.experiment_id"
));
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $combination=array();
  $combinationJson=array();
  foreach ($listCombinatons as $combinationKey => $listCombinaton){
	$combination[$listCombinaton->experiment_id][] = '"'.trim($listCombinaton->SID).'":"'.trim($listCombinaton->name).'"';
  }
	$serialId = 1;
	$combinationname= "";
        $results = array(array()); 
        
	foreach ($combination as $arr){
	    array_push($arr,null); 
	    $new_result = array();
	    foreach ($results as $old_element)
	        foreach ($arr as $el)
	            $new_result []= array_merge($old_element,array($el));
	    $results = $new_result;
	}

	$wpdb->query($wpdb->prepare("TRUNCATE TABLE ".$wpdb->prefix."WPTestMonkey_combinations "));	
	if(count($results)> 0){
		foreach ($results as $result){ 
		 if(in_array(null,$result)) continue;
			foreach ($result as $resultKey => $resultVal){
				   if($resultKey > 0) $combinationname .= ",";
					  $combinationname .= $resultVal;
			}
			$wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."WPTestMonkey_combinations SET name =%s, project_id=%d,  id=%d", '[{'.trim($combinationname).'}]',(int)$id,(int)$serialId++));
			$combinationname = "";
		} 
	}
	
}
$listAllCombinations = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."WPTestMonkey_combinations WHERE  project_id=%d", $id));
?>
<?php include 'tabs.php' ?>
<style>
.clear { clear: both; }
.experimentMappingCombo {
	width: 40%;
	text-align: center;
	float: left;
}
.experimentMappingCombo select{
	width: 250px;
}
.removeExperimentElement {
	width: 60px;
	height:60px;
	background-color: white;
	cursor: pointer;
	display: inline-block;
	margin-top: 5px;
	width: 30px;
}
.addExperimentElement{
	width: 60px;
	height:60px;
	background-color: white;
	cursor: pointer;
	display: inline-block;
	margin-top: 5px;
	width: 30px;
}
#experimentMappingBox {
	width: 80%;
	height:auto;
	clear: both;
}

.experimentMappingElements {
	width: 100%;
	height: auto;
	clear: both;
}

.experimentMappingElement {
	width: 100%;
	height: 20px;
	margin-bottom: 2px;
	clear: both;
}
.experimentMappingElement{padding-top: 20px;
}
</style>
<div class="wrap">
  <h3>Project</h3>
  <p>
    This is an overview of your <em><?php echo $exp->name ?></em> project.<br />
    <a href="?page=WPTestMonkey&amp;action=edit_project&amp;id=<?php echo $exp->id ?>">Edit project</a>
    <small>|</small>
    <small><a href="?page=WPTestMonkey&amp;action=delete_project&amp;id=<?php echo $exp->id ?>" style="color: #c00;">Delete project</a></small>
  </p>
	<div style="background: #eee; border: 1px solid #ccc; padding: 5px 20px; border-radius: 5px;">
		<h3>Current statistics</h3>
		<table class="wp-list-table widefat" cellspacing="0">	
			<thead>
				<tr>
					<th>Serial</th>
					<th>Variation["VARIATION_ID":"VARIATION_NAME"]</th>
					<th>Views</th>
					<th>Goal hits</th>
					<th>Conversion rate</th>		
				</tr>
			</thead>
			<tbody>
			  <?php if(count($listAllCombinations)> 0){
				foreach ($listAllCombinations as $listAllCombination){ 
				?>
						<tr>
						  <td class="col-serial"><?php echo $listAllCombination->id?></td>
						  <td class="col-variation">
						  <?php echo $listAllCombination->name?></td>
						  <td class="col-views"><?php echo $listAllCombination->viewed?></td>
						  <td class="col-goalhit"></td>
						  <td class="col-convrate"></td>
						</tr>	
					
		<?php   } 
			}?>
			</tbody>
		</table>
</div>
  
  <h3>How to use this project</h3>
  <p>
    This is a <strong><?php echo $exp->type ?></strong> project.
  </p>
  <?php if ($exp->type == 'content') { ?>
    <p>
      In <strong>posts, pages, or widgets</strong>, insert this code: <code>[WPTestMonkey project="<?php echo $id ?>"]</code><br />
    </p>
  <?php } elseif ($exp->type == 'stylesheet') { ?>
    <p>
      This project is automatically inserted into the <strong>stylesheet</strong>.
    </p>
  <?php } elseif ($exp->type == 'javascript') { ?>
    <p>
      This project is automatically inserted into the <strong>javascript</strong>.
    </p>
  <?php } ?>
  <p>
    To insert the <strong>name of the variation</strong> currently being displayed (e.g. in tracking etc.), insert this code:
    <code>[WPTestMonkey project="<?php echo $id ?>" ]</code><br />
  </p>
  <p>
    For information on tracking goals, click <em>Get tracking code</em> below each goal below.
  </p>
  
  <?php //print_r($project_expreriments);
  //echo count($project_expreriments);?>
 <h3>Experiments</h3>
  <form action ="" method="post">
	<div class="clear"></div>
		<div class="experimentMappingElements">
	<?php if(count($project_expreriments)> 0){
				foreach ($project_expreriments as $project_expreriment){ ?>
					<div class="experimentMappingElement">
						<div class="experimentMappingCombo">
							<select name="experiment_id[]">
								<option name="experiment1" value="-1">--Select Category--</option>
								<?php foreach ($experiments as $experiment){ 
								echo '<option ';
								if($project_expreriment->experiment_id == $experiment->id ){
									echo "selected";
								}
								echo ' value="'.$experiment->id.'">'.$experiment->name.'</option>';
								}?>
							</select>
						</div>
						<span class="removeExperimentElement">X</span>
					</div>
			<?php } 
			} else {?>
				<div class="experimentMappingElement">
					<div class="experimentMappingCombo">
						<select name="experiment_id[]">
							<option name="experiment1" value="-1">--Select Category--</option>
							<?php foreach ($experiments as $experiment){ 
							echo '<option value="'.$experiment->id.'">'.$experiment->name.'</option>';
							
							}?>
							
						</select>
					</div>
					<span class="removeExperimentElement" style="">X</span>
				</div>
			
			<?php
			
			}
			
			?>
			<span class="addExperimentElement" >+Add</span>
		</div>
   <div class="clear"></div>  
   <p>
     <input type="submit" value="Save Combination" class="button-secondary" />
   </p>
  </form>
  <h3>Goals</h3>
  <p>
    The WordPress WPTM Split Testing Test plugin currently only supports showing statistics for one goal. You can, however, add more goals and these will be tracked for future use.
  </p>
  <table class="wp-list-table widefat" cellspacing="0">
  	<thead>
    	<tr>
    		<th>Goal</th>
    	</tr>
  	</thead>

  	<tbody>
      <?php foreach ($goals as $goal) { ?>
        <tr>
          <td>
            <a href="?page=WPTestMonkey&amp;action=edit_project_goal&amp;id=<?php echo $goal->id ?>"><strong><?php echo $goal->name ?></strong></a><br />
            <small>
              <a href="?page=WPTestMonkey&amp;action=get_project_tracking_code&amp;id=<?php echo $goal->id ?>">Get tracking code</a>
              |
              <a href="?page=WPTestMonkey&amp;action=edit_project_goal&amp;id=<?php echo $goal->id ?>">Edit</a>
              |
              <a href="?page=WPTestMonkey&amp;action=delete_project_goal&amp;id=<?php echo $goal->id ?>">Delete</a>
            </small>
          </td>
        </tr>
      <?php } ?>
  	</tbody>
  </table>
  <p>
    <input type="button" value="Add new goal" class="button-secondary" onclick="document.location = '?page=WPTestMonkey&amp;action=add_project_goal&amp;project_id=<?php echo $id ?>';" />
  </p>
  
  <p>
    &laquo; <a href="?page=WPTestMonkey">Back to Projects</a>
  </p>

</div>
<script>
	jQuery(document).ready(function() {
	 var catElementCount = 2;
		var addBT =  jQuery('.addExperimentElement');
			if(addBT.length > 0) {
				addBT.each(function() {
				   jQuery(this).click(function() {
				    var catElement =  jQuery(this).parent().find('.experimentMappingElement:first').clone(true);
				    catElement.find('.removeExperimentElement').css('display', 'inline-block');
				    catElement.find('select[name=experiment1]').attr('name', 'experiment'+catElementCount).val('-1');
				    jQuery(this).before(catElement);
				    if(jQuery('.experimentMappingElement').length > 1) jQuery('.removeExperimentElement').show();
				    catElementCount++;
				  });
				});
			}
			
			var removeBT =  jQuery('.removeExperimentElement');
			if(removeBT.length > 0) {
				removeBT.each(function() {
				   jQuery(this).click(function() {
				    jQuery(this).parent().remove();
				    if(jQuery('.experimentMappingElement').length == 1) jQuery('.removeExperimentElement').hide();
				    else jQuery('.removeExperimentElement').show();
				  });
				});
			}
			
			 jQuery('.experimentMappingCombo select').change(function(e) {
			  var catVal =  jQuery(this).val();
			  if(catVal != -1) {
					var catValCount = 0;
					 jQuery('.experimentMappingCombo select').each(function() {
						if(( jQuery(this).val() != -1) && ( jQuery(this).val() == catVal)) catValCount++;
					});
					if(catValCount > 1)  jQuery(this).val('');
				}
			});
		});
</script>