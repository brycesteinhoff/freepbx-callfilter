<?php

// Ensure user is logged in
if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); }

// Drop the table
$sql = "DROP TABLE `callfilter`;";
sql($sql);

?>
