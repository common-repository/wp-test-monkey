<?php
$id = (int)$_GET['id'];
$active = (bool)$_GET['active'];

$var = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."WPTestMonkey_variations WHERE id=%d", $id));
$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."WPTestMonkey_variations SET active=%d WHERE id=%d", $active, $id));

redirect_to("?page=WPTestMonkey&action=show_experiment&id=" . $var->experiment_id);
?>