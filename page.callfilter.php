<?php

// Ensure user is logged in
if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); }

// Page variables
$display = 'callfilter';
$baseURL = $_SERVER['PHP_SELF'] . '?display=' . urlencode($display);
?>

<h2><?php echo _("Call Filter"); ?></h2>

<a href="<?php echo $baseURL . '_whitelist'; ?>"><?php echo _("Manage Whitelist"); ?></a>

<br /><br />

<a href="<?php echo $baseURL . '_blacklist'; ?>"><?php echo _("Manage Blacklist"); ?></a>