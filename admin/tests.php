<?php
$tab = 'tests';
include 'tabs.php';
$tests = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."WPTestMonkey_tests where status = 1 ORDER BY createdOn DESC");
include 'side.php';
?>
<div class="wrap">
	<?php if(count($tests) > 0) { ?>
	
  <h3>Your active tests</h3>
  <?php 
        $test = VLister($tests); 
    	if ($test)
    	{ 
  
  ?>
  <div style="margin-bottom: 20px;">
		<table class="wp-list-table widefat" cellspacing="0">
			<thead>
				<tr>
					<th>
					  <?php
							$listAllCombinations = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."WPTestMonkey_combinations WHERE  test_id=%d ORDER BY viewed DESC", $test->id));
						?>
						<?php echo $test->name; ?>
						&nbsp;&nbsp;(<?php echo count($listAllCombinations); ?> active combinations)&nbsp;&nbsp;
						<small>
							<a href="?page=WPTestMonkey_show_test_<?php echo $test->id ?>">Edit</a>
							|
							<a href="?page=WPTestMonkey&amp;action=delete_test&amp;id=<?php echo $test->id ?>">Deactivate</a>
						</small>
						&nbsp;&nbsp;
						<medium>
						  <a target="_blank" href="<?php echo get_site_url().'?p='. $test->testForId; ?>">Show test page</a>
						</medium>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<small style="float: right;">
						  Last updated:&nbsp;&nbsp;
						  <?php 
							$wpDateFormat = get_option('date_format');
						    $today = date($wpDateFormat);
						    $edited = date($wpDateFormat, strtotime($test->createdOn)); 
						    if($edited == $today) echo "Today";
						    else if(((strtotime($edited) - strtotime($today))/(24*60*60)) == 1) echo "Yesterday";
						    else echo $edited;
						  ?>
						</small>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
					  <?php echo $test->description; ?>
					</td>
				</tr>
				<?php  if(count($listAllCombinations)> 0 ) { ?>
				<?php 
					$countElement = json_decode($listAllCombinations[0]->name,true);
					$countElement = count($countElement[0]);
				?>
				<?php if($countElement>0):?>
				<tr>
					<td>
							<table class="wp-list-table widefat tablesorter sort_table" cellspacing="0">	
								<thead>
									<tr>
										<th>#</th>
										<?php 
											for($i = 0; $i<$countElement;$i++ ){
												echo '<th class="nosort"> Element'.($i+1).'</th>';
											}
											?>
										<?php if(count($listAllCombinations) > 1) { ?>
										<th style="text-align:center;">Views</th>
										<th style="text-align:center;">Conversions</th>
										<th style="text-align:center;">%</th>				
										<th style="text-align:center;">$Earned</th>
										<?php } ?>
									</tr>
								</thead>
								<tbody>
									<?php
									$serialNo =1;
									foreach ($listAllCombinations as $listAllCombination) { 
									?>		
									  <?php if($serialNo > 5) { ?> <tr style="display: none;"> <?php } else { ?> <tr> <?php } ?>
												<td class="col-serial"><?php echo $serialNo++?></td>
												<?php 
												 $combinationElement = json_decode($listAllCombination->name,true);
												 foreach ($combinationElement[0] as $listID => $listName):
												?> 
												<td class="col-variation">
												<?php echo $listName?>
												</td>
												<?php endforeach;?> 
												<?php if(count($listAllCombinations) > 1) { ?>
												<td align="center" class="col-views"><?php echo $listAllCombination->viewed; ?></td>
												<td align="center" class="col-goalhit"><?php echo $listAllCombination->conversionCount; ?></td>
												<?php if((int)$listAllCombination->viewed != 0) { ?>
												<td align="center" class="col-convrate"><?php echo round((((int)$listAllCombination->conversionCount*100) / (int)$listAllCombination->viewed), 2); ?>%</td>
												<?php } else { ?>
												<td align="center" class="col-convrate">0%</td>
												<?php } ?>
												<td align="center">$<?php echo ((int)$listAllCombination->conversionCount * (int)$listAllCombination->conversionEarned); ?></td>
												<?php } ?>
											</tr>	
									<?php
										} 
									?>
								</tbody>
							</table>
					</td>
				</tr>
				<?php endif;?>
				<?php
					if(count($listAllCombinations) == 1) {
						$testElements = $wpdb->get_results($wpdb->prepare("SELECT element_id FROM ".$wpdb->prefix."WPTestMonkey_test_elements WHERE  test_id=%d", $test->id));
				?>
				<tr>
					<td style="color: red;">
						<?php echo str_replace('[%elid%]', $testElements[0]->element_id, str_replace('[%id%]', $test->id, $mvmessages['warnning']['testHaveOnlyOneElement'])); ?>
					</td>
				</tr>
				<?php
					} else if($test->isInclude == 0) {
				?>
				<tr>
					<td style="color: red;">
						<?php echo str_replace('[%pgid%]', $test->testForId, $mvmessages['warnning']['testNotIncludeInPage']); ?>
					</td>
				</tr>
				<?php } else if($test->isBroken == 1) { ?>
				<tr>
					<td style="color: red;">
						<?php echo str_replace('[%pgid%]', $test->testForId, $mvmessages['warnning']['testIsBroken']); ?>
					</td>
				</tr>
				<?php }  } else { ?>
					<tr>
					<td style="color: red;">
						<?php echo str_replace('[%id%]', $test->id, $mvmessages['warnning']['testHaveNoElement']); ?>
					</td>
				</tr>
					
        <?php } ?>
			</tbody>
		 </table>
	 </div>
   <?php
  	}
   ?>
    <script type="text/javascript">
	  jQuery(document).ready(function() {
			jQuery(".sort_table").each(function() {
				if(jQuery(this).find('th').length > 2) {
					var falseHeader = '{"headers": {"0":{"sorter": false}';
					for(i = 1; i < jQuery(this).find('th').length - 4; i++) {
						falseHeader += ',"'+i+'":{"sorter": false}';
					}
					falseHeader += '}}';
					jQuery(this).tablesorter(jQuery.parseJSON(falseHeader));
				}
			});
	  });
	 </script>
	 <?php
		} else { 
			echo $mvmessages['information']['createTestWhenNoTest'];
		}
                 if ($sidew) echo $sidew;

 
	?>

<?
 
 
    $tests = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."WPTestMonkey_tests where status = 0 ORDER BY createdOn");
 

    if(count($tests) > 0) {
  ?>
  <h3>Deactivated tests</h3>
  <div style="margin-bottom: 20px;">
		<table class="wp-list-table widefat" cellspacing="0">
			<thead>
				<?php
					foreach ($tests as $test) {
				?>
				<tr>
					<td>
					  <b><?php echo $test->name ?></b>&nbsp;&nbsp;
					</td>
					<td>
					  Winning test combination:
					  <?php
							$listAllCombinations = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."WPTestMonkey_combinations WHERE  test_id=%d ORDER BY viewed DESC limit 1", $test->id));
							$listNames = '';
							if(count($listAllCombinations)> 0){
								foreach ($listAllCombinations as $listAllCombination) { 
									$combinationElement = json_decode($listAllCombination->name,true);
										foreach ($combinationElement[0] as $listID => $listName):
											if(strlen($listNames) > 0) $listNames .= ', ';
											$listNames .= $listName;
										endforeach;
								}
							}
							echo $listNames;
						?>
						&nbsp;&nbsp;
					</td>
					<td>
					  <small>
							<a href="?page=WPTestMonkey&amp;action=show_test&amp;id=<?php echo $test->id ?>">Edit</a>
						</small>
					</td>
				</tr>
			 <?php
				 }
			?>
		 </thead>
	  </table>
	</div>
	<?php
		}
		wp_deregister_script(array('WPTMCombinationSorter'));
		wp_deregister_style(array('WPTMCombinationSorter'));
		wp_register_script( 'WPTMCombinationSorter', WP_PLUGIN_URL.'/wp-test-monkey/js/sorter.js', false, '0.0.1');
		wp_register_style('WPTMCombinationSorter', WP_PLUGIN_URL.'/wp-test-monkey/css/sorter.css',false, '0.0.1', 'screen');
			
		//echo ' <!-- Job Costing '.$this->jobcostingversion.' --> ';
		if (function_exists('wp_enqueue_style')) {
			wp_enqueue_style('WPTMCombinationSorter');
		}
		if (function_exists('wp_enqueue_script')) {
			wp_enqueue_script('WPTMCombinationSorter');
		}
	?>
</div>