  <style>
		.elementContainer{
			 border:1px solid gray;
		}
		.elementBody{
			display:none;
		}
	</style>
	<div id="WPTM_short_insert" style="display:none;">
		<div class="wrap">
			<?php
				$listAllElements = $wpdb->get_results($wpdb->prepare(
					"SELECT 
						mtme.id as id,
						mtme.name as element
					FROM 
						".$wpdb->prefix."WPTestMonkey_test_elements as mtmte,
						".$wpdb->prefix."WPTestMonkey_elements as mtme,
						".$wpdb->prefix."WPTestMonkey_tests as mtmt
					WHERE 
						mtmt.testForId = %d
					AND 
						mtmte.element_id  = mtme.id 
					AND 
						mtmte.test_id = mtmt.id
					AND 
						mtmt.status = 1
					", $_REQUEST['post']));
			?>
			<h3>Insert short code for WPTM Test</h3>
			<br>
			<?php 
				if(count($listAllElements) > 0) {
			?>
			<script>
				var elementCount = <?php echo count($listAllElements); ?>;
			</script>
			<?php
				foreach($listAllElements as $keyOfElement => $listElement):
			?>
			<div class='elementContainer'>
				<div class='elementHeader' style="cursor: pointer;">
					<div class="elementHeadLabel" style="float: left;" class='plus'><span>+</span>
					<?php 
						echo $listElement->element;
					?>
					</div>
					<div style="float: right;"> <button id=<?php echo $listElement->id; ?>>Insert this</button></div>
					<div style="clear: both;"></div>
				</div>
				<div class='elementBody'>
					<?php
						$listAllVariations = $wpdb->get_results($wpdb->prepare(
							"SELECT * from ".$wpdb->prefix."WPTestMonkey_variations where element_id = %d", $listElement->id));
						if(count($listAllVariations) > 0) {
							echo '<ul>';
							foreach($listAllVariations as $variationKey => $listVariation) :
					?>
						<li><?php echo $listVariation->content; ?></li>
					<?php endforeach; echo '</ul>'; }?>
				</div>
				<div style="clear: both;"></div>
			</div>  	 
		<?php endforeach; }?>
		</div>
	</div>
<?php 
	wp_deregister_script(array('WPTMElementInclude'));
	wp_register_script( 'WPTMElementInclude', WP_PLUGIN_URL.'/wp-test-monkey/js/addTestElement.js', false, '0.0.1');
	if (function_exists('wp_enqueue_script')) {
		wp_enqueue_script('WPTMElementInclude');
	}
?>
