<style>
  .helpPoint { display: inline-block; float; left;cursor: pointer; margin-left: 5px; font-weight: bold; color: #AAAAAA; background-image: url("../wp-content/plugins/wp-test-monkey/images/help.png"); height: 16px; width: 16px;  }
</style>
<?php
require 'admin/functions.php';
require 'WPTMMessages.php';
if (isset($_GET['action'])) {
  include 'admin/' . $_GET['action'] . '.php';
} else {
  include 'admin/tests.php';
}
?>