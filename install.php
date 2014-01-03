<?php

// Ensure user is logged in
if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); }

// Create a new table
$sql = "CREATE TABLE IF NOT EXISTS callfilter (
	`extension` varchar(255) NOT NULL,
	`description` varchar(255) NOT NULL default '',
	`type` enum('whitelist', 'blacklist', 'unknownCID') NOT NULL,
	PRIMARY KEY (`extension`, `type`)
);";
sql($sql);

// Add initial record for unknownCID set to allow
$sql = "INSERT INTO callfilter VALUES ('unknownCID','allow','unknownCID');";
sql($sql);

?>
