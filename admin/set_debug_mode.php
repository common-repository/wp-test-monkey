<?php
$debug = (int)$_GET['debug'];

if ($debug == 1) {
  $_SESSION['WPTestMonkey_debug'] = 1;
} else {
  unset($_SESSION['WPTestMonkey_debug']);
}

redirect_to('?page=WPTestMonkey&action=debug');
?>