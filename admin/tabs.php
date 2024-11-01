<?
	$maindatafile=file("../wp-content/plugins/wp-test-monkey/admin/licnotes.dat");
	$num = rand(0,count($maindatafile)-1);
	$licnote = trim($maindatafile[$num]);
 


?>
<style>
#licnote
{
	float: right; 
	font-size: 10pt; 
	color: red;
}
#licnote a
{
	color: red;
}

#verrormsg
{
	color: red;
}
</style>
<div class="wrap">
	<h2 class="nav-tab-wrapper">
		<a class="nav-tab<?php if ($tab == 'tests') echo ' nav-tab-active' ?>" href="?page=WPTestMonkey&amp;action=tests">Tests</a>
		<!--a class="nav-tab<?php if ($tab == 'elements') echo ' nav-tab-active' ?>" href="?page=WPTestMonkey">Experiments</a-->
		<a class="nav-tab<?php if ($tab == 'settings') echo ' nav-tab-active' ?>" href="?page=WPTestMonkey&amp;action=settings">Settings</a>
		<a class="nav-tab<?php if ($tab == 'help') echo ' nav-tab-active' ?>" href="?page=WPTestMonkey&amp;action=help">Help</a>
 		<div id='licnote'><? echo $licnote; ?></div>
	</h2>
</div>
 
 