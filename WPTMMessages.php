<?php

$mvmessages = array('success' => array(),
										'warnning' => array(
																		 'testNotIncludeInPage' => 'To start this test, please add this elements to your page. <a href="post.php?post=[%pgid%]&amp;action=edit" >Click to do that now.</a> <span class="helpPoint"></span>',
																		 'testIsBroken' => 'Some testing elements aren\'t on the page being tested yet. <a href="post.php?post=[%pgid%]&amp;action=edit" >Don\'t worry - just go and add them now!</a> <span class="helpPoint"></span>',
																		 'testHaveNoElement' => 'You have not added any elements to test yet. <a href="?page=WPTestMonkey&amp;action=create_element&amp;is_first=true&amp;id=[%id%]" >Click here to add some.</a>',
																		 'testHaveOnlyOneElement' => 'You\'ve only entered 1 variation to test. Please add at least 1 more to start this test. <a href="?page=WPTestMonkey&amp;action=edit_element&amp;id=[%elid%]&amp;test_id=[%id%]" >Click here to add more now.</a>',
																		 'testActivationNotAllowd' => '<p>Sorry!! You are not allowed to active this test. Associate page with this test has already another active test.</p>'
																			),
										'error' => array(
											    					'testNameMissing' => '<div class="error below-h2">Please insert test name</div>',
											    					'testDescriptionMissing' => '<div class="error below-h2">Please insert test description</div>',
											    					'testPageMissing' => '<div class="error below-h2">Please select a page for test</div>',
											    					'testCreateFaild' => '<div class="error below-h2">Sorry!! Unable to create test.</div>',
											    					'testPageSuccessPageSame' => '<div class="error below-h2">Sorry!! You have choose same page for test and success.</div>',
																		'elementCreateFaild' => '<div class="error below-h2">Sorry!! Unable to create element.</div>',
																		'elementNameMissing' => '<div class="error below-h2">Please insert element name</div>',
																		'variationMissing' => '<div class="error below-h2">Please insert atleast one variation for the element</div>'
																		),
										'information' => array(
																					'createTestWhenNoTest' => '<h2>Welcome to WP Test Monkey (Free version)</h2><p><img src="'.WP_PLUGIN_URL.'/wp-test-monkey/images/wptestmonkey_logo.gif" align="right" />This is the easiest way to test headlines, offers, prices, etc.. on your site without having to do any coding or do battle with complex 3rd party systems. Simply create a test and BOOYA! – you\'ve won the internet marketing lottery.</p><p>You\'ll instantly have a winning combination of headline, copy, and price that will deliver a windfall of sales and profits from your site, day in, day out.</p><p>So what\'s the difference between this and other testing plugins? Don\'t confuse the WP Test Monkey with basic split testing software. With those other plugins you have to laboriously create multiple versions of the same page for each test you want to run. This is the free version of WP Test Monkey which makes it drop-dead simple to test up to 2 elements - if you would like to test unlimited elements of your page at the same time, you can grab the premium version at the <a href=\'https://cz112.infusionsoft.com/go/wptm/wp/\'>WP Test Monkey website</a>.</p><p>Shall we get started?</p>',
																					'addTestHeadInfo' => '<p>Welcome to the WP Test Monkey plugin -- where even a monkey can test his way to 6 figures.</p><p>In comparison to normal split testing plugins, we make it easy for you to test multiple elements of your page at the same time.</p><p>Why is that useful? Let\'s say you have 2 headlines you like and 2 different price points for your product. With this plugin you can test them all at the same time by showing a different and unique variation to each visitor. After enough traffic, you\'ll be  able to see clearly which version of the elements worked best in terms of conversion.. most importantly, which earned you the most money.</p>',
																					'setTestSuccessURL' => '<p>First of all, we need to know when a test has \'succeeded\' or \'converted\' a visitor into taking action. What is the URL we should monitor to determine whether a test has been successful?</p>',
																					'createElementWhenNoElement' => '<p><h4>Which page elements do you want to test?</h4></p><p>Click the button below to add your first element (Most people start by testing their headlines as that\'s the point of greatest leverage on any webpage)</p>',
																					'addFirstElementInfo' => '<p><h3>What\'s the first element you want to test for this page?</h3></p><p>HINT: Most people start by testing their headlines because that\'s the point of greatest leverage on any webpage!</p>',
																					'testDeactivateConfirmation' => '<p> Do you really want to deactivate this Test? This can\'t be undone.</p>',
																					'elementDeactivateConfirmation' => '<p> Do you really want to delete this Element? This can\'t be undone. Your test will be broken.</p>',
																					'testActivateConfirmation' => '<p>Do you really want to active this Test?</p>',
																					'pageBeingTestLabelInfo' => '<span>Please choose the page you\'d like to run a test on. This can be any page or blog post in the WordPress database.</span>',
																					'successPageLabelInfo' => 'Please choose a page to use as your "success" page to determine whether a given test combination has worked.'
																					),
										'help' => array(
																		'help_1' => 'default help text for help_1',
																		'help_2' => 'This is the \'shortcode\' snippet you will put onto your testing page. The Monkey is looking out for these shortcodes and replaces them with the text you\'re testing.',
																		'help_3' => 'This is The Monkey\'s way of letting you know whether the shortcode for each element is on the page you want to test yet. A tick is good. A cross means you need to click the \'Add these elements to your page\' link below and add them!',
																		'help_4' => 'If you want to add a further element to your test, click here',
																		'help_5' => 'Each shortcode needs to be added to your page to be tested before the test can start. Click here to go to your test page to add the shortcode.',
																		'help_6' => 'default help text for help_6',
																		'help_7' => 'Enter a name for your new test (eg: \'Sales page test\')',
																		'help_8' => 'Enter a short description for your test',
																		'help_9' => 'Choose the post or page you want to test (Note: it needs to exist already before setting up this test)',
																		'help_10' => 'Select your \'success page\' -- the page that will signify a winning combination (Note: this page needs to exist as well)',
																		'help_11' => 'default help text for help_11',
																		'help_12' => 'default help text for help_12',
																		'help_13' => 'default help text for help_13',
																		'help_14' => 'This is the count of variations of each element Test Monkey is testing for you.'
																		),
										'testTypes' => array(
																					'content' => 'Content inserted manually into posts, pages, and widgets',
																					'stylesheet' => 'Stylesheet inserted automatically into the stylesheet',
																					'javascript' => 'Javascript inserted automatically into the javascript',
																					'theme' => 'Theme to switch between themes'
																				)
																					
										);

?>
