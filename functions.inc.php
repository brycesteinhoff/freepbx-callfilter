<?php

// Ensure user is logged in
if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); }

/**
 * Sets up contexts for 1) checking filter and 2) storing
 * extensions to reference for blacklist and whitelist.
 */
function callfilter_get_config($engine) {
	global $ext;

	switch($engine) {
		case "asterisk":

			// ----------------------------------------
			// Setup context for checking filter.
			// ----------------------------------------
			$c = "app-callfilter-check"; // context
			$e = "s"; // extension
			
			// Check CID against white/blacklist
			$ext->add($c, $e, 'check', new ext_gotoif('$[DIALPLAN_EXISTS("app-callfilter-whitelist", "${ARG1}")]', 'allow'));
			$ext->add($c, $e, '', new ext_gotoif('$[DIALPLAN_EXISTS("app-callfilter-blacklist", "${ARG1}")]', 'deny'));

			// Block unknown CID if set to deny
			$sql = "SELECT description FROM callfilter WHERE type = 'unknownCID'";
			if (sql($sql, 'getOne') == 'deny') {
				$ext->add($c, $e, '', new ext_gotoif('$["${ARG1}" = "Unknown"]', 'deny'));
				$ext->add($c, $e, '', new ext_gotoif('$["${ARG1}" = "Unavailable"]', 'deny'));
				$ext->add($c, $e, '', new ext_gotoif('$["${ARG1}" = ""]', 'deny'));
			}

			// Handle call
			$ext->add($c, $e, 'default', new ext_noop('Call Filter: Unlisted')); // Default fallthrough
			$ext->add($c, $e, '', new ext_return(''));
			$ext->add($c, $e, 'allow', new ext_noop('Call Filter: Whitelisted')); // Not filtered; return to DID context
			$ext->add($c, $e, '', new ext_return(''));
			$ext->add($c, $e, 'deny', new ext_noop('Call Filter: Blacklisted')); // Get rid of caller
			$ext->add($c, $e, '', new ext_answer(''));
			$ext->add($c, $e, '', new ext_wait(1));
			$ext->add($c, $e, '', new ext_zapateller(''));
			$ext->add($c, $e, '', new ext_playback('ss-noservice'));
			$ext->add($c, $e, '', new ext_hangup(''));

			// ----------------------------------------
			// Setup context for storing whitelist.
			// ----------------------------------------
			$c = "app-callfilter-whitelist"; // context

			$results = callfilter_list('whitelist');

			foreach ($results as $result) {
				$ext->add($c, $result[0], '', new ext_noop($result[1]));
			}

			// ----------------------------------------
			// Setup context for storing blacklist.
			// ----------------------------------------
			$c = "app-callfilter-blacklist"; // context

			$results = callfilter_list('blacklist');

			foreach ($results as $result) {
				$ext->add($c, $result[0], '', new ext_noop($result[1]));
			}

			break;

		default:
			break;
	}
}

/**
 * Sets up call filter subroutine on inbound routes.
 */
function callfilter_hookGet_config($engine) {
	global $ext;

	switch($engine) {
		case "asterisk":

			// Code from modules/core/functions.inc.php core_get_config inbound routes
			$didlist = core_did_list();

			if (is_array($didlist)) {
				foreach ($didlist as $item) {

					$exten = trim($item['extension']);
					$cidnum = trim($item['cidnum']);
						
					if ($cidnum != '' && $exten == '') {
						$exten = 's';
						$pricid = ($item['pricid']) ? true:false;
					} else if (($cidnum != '' && $exten != '') || ($cidnum == '' && $exten == '')) {
						$pricid = true;
					} else {
						$pricid = false;
					}
					$context = ($pricid) ? "ext-did-0001":"ext-did-0002";

					$exten = (empty($exten)?"s":$exten);
					$exten = $exten.(empty($cidnum)?"":"/".$cidnum); //if a CID num is defined, add it

					$ext->splice($context, $exten, 1, new ext_gosub('1', 's', 'app-callfilter-check', '${CALLERID(num)}'));
				}
			} // else no DID's defined. Not even a catchall.

			break;

		default:
			break;
	}
}

/**
 * Gets the white/blacklist.
 */
function callfilter_list($list) {
	$sql = "SELECT extension,description FROM callfilter WHERE type = '".$list."';";
	return sql($sql, 'getAll');
}

/**
 * Adds entry to white/blacklist.
 */
function callfilter_add($post,$list) {
	if (trim($post['extension']) == '' && trim($post['description']) == '') {
		return false;
	}

	needreload();

	$sql = "INSERT INTO callfilter VALUES (
		'".$post['extension']."',
		'".$post['description']."',
		'".$list."'
	);";
	sql($sql);
}

/**
 * Deletes entry from white/blacklist.
 */
function callfilter_delete($extension,$list) {
	needreload();

	$sql = "DELETE FROM callfilter WHERE type = '".$list."' AND extension = '".$extension."';";
	sql($sql);
}

?>