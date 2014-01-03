<?php

// Ensure user is logged in
if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); }

// Page variables
$list = 'whitelist';
$display = 'callfilter_' . $list;
$baseURL = $_SERVER['PHP_SELF'] . '?display=' . urlencode($display);
$extension = isset($_REQUEST['extension']) ? $_REQUEST['extension'] : '';
$oldextension = isset($_REQUEST['oldextension']) ? $_REQUEST['oldextension'] : '';

// Do the right thing
switch ($action) {
	case "add":
		callfilter_add($_POST,$list);
		redirect_standard();
		break;
	case "delete":
		callfilter_delete($extension,$list);
		redirect_standard();
		break;
	case "edit":
		callfilter_delete($oldextension,$list);
		callfilter_add($_POST,$list);
		redirect_standard();
		break;
	default:
		break;
}

?>

<h2><?php echo _("Add Whitelist Entry"); ?></h2>
<a href="<?php echo $_SERVER['PHP_SELF']; ?>?display=callfilter_blacklist">Goto Manage Blacklist</a>

<form autocomplete="off" name="callfilterEdit" action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
	<input type="hidden" name="action" value="add">
	<input type="hidden" name="oldextension" value="<?php echo $extension; ?>">

	<table>
		<tr>
			<td colspan="2">
				<h5><?php echo _("Settings"); ?><hr></h5>
			</td>
		</tr>
		<tr>
			<td>
				<a href=# class="info">
					<?php echo _("Caller ID"); ?>
					<span>
						<?php echo _("This incoming number will always be allowed. To use the pattern matching rules below, prefix with an underscore."); ?>
						<br /><br />
						<b><?php echo _("Rules:"); ?></b>
						<br />
						<b>X</b>&nbsp;&nbsp;&nbsp; <?php echo _("matches any digit from 0-9"); ?>
						<br />
						<b>Z</b>&nbsp;&nbsp;&nbsp; <?php echo _("matches any digit from 1-9"); ?>
						<br />
						<b>N</b>&nbsp;&nbsp;&nbsp; <?php echo _("matches any digit from 2-9"); ?>
						<br />
						<b>[1237-9]</b>&nbsp; <?php echo _("matches any digit in the brackets (example: 1,2,3,7,8,9)"); ?>
						<br />
						<b>.</b>&nbsp;&nbsp;&nbsp; <?php echo _("wildcard, matches one or more dialed digits"); ?>
					</span>
				</a>:
			</td>
			<td>
				<input type="text" name="extension">
			</td>
		</tr>
		<tr>
			<td>
				<a href=# class="info">
					<?php echo _("Description"); ?>
					<span>
						<?php echo _("Name of this whitelist entry."); ?>
					</span>
				</a>:
			</td>
			<td>
				<input type="text" name="description">
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<h6><input name="submit" type="submit" value="<?php echo _("Submit"); ?>"></h6>
			</td>
		</tr>
	</table>
</form>

<?php
// Get existing whitelist entries
$entries = callfilter_list($list);

// If they exist, display them
if (count($entries) > 0) {
?>
<table cellpadding="5">
	<tr>
		<td colspan="4">
			<h5><?php echo _("Entries") ?><hr></h5>
		</td>
	</tr>
	<tr>
		<td>
			<b><?php echo _("Caller ID"); ?></b>
		</td>
		<td>
			<b><?php echo _("Description"); ?></b>
		</td>
		<td>
			<b><?php echo _("Delete"); ?></b>
		</td>
		<td>
			<b><?php echo _("Edit"); ?></b>
		</td>
	</tr>

	<?php
	foreach ($entries as $entry) {
		?>
		<tr>
			<td><?php echo $entry[0]; ?></td>
			<td><?php echo $entry[1]; ?></td>
			<td><a href="<?php echo $baseURL; ?>&extension=<?php echo $entry[0]; ?>&action=delete"><?php echo _("Delete"); ?></a></td>
			<td><a href="#" onClick="document.callfilterEdit.action.value='edit'; document.callfilterEdit.oldextension.value='<?php echo $entry[0]; ?>'; document.callfilterEdit.extension.value='<?php echo $entry[0]; ?>'; document.callfilterEdit.description.value='<?php echo $entry[1]; ?>';"><?php echo _("Edit"); ?></a></td>
		</tr>
		<?php
	}
	?>
</table>

<?php } ?>