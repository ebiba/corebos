<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache'); // recommended to prevent caching of event data.
set_time_limit(0);
global $app_strings;

function send_message($id, $message, $progress, $processed, $total, $app_strings) {
	$d = array('message' => $message , 'progress' => $progress, 'processed' => $processed, 'total' => $total);
	echo $app_strings['id'].": $id" . PHP_EOL;
	echo $app_strings['data'].":". json_encode($d) . PHP_EOL;
	echo PHP_EOL;
	ob_flush();
	flush();
}
$params = json_decode(vtlib_purify($_REQUEST['params']), true);

global $currentModule, $rstart;
$nonSupportedMassEdit = array('Emails');

$focus = CRMEntity::getInstance($currentModule);

$idlist= vtlib_purify($params['massedit_recordids']);
$viewid = isset($params['viewname']) ? vtlib_purify($params['viewname']) : '';
$return_module = urlencode(vtlib_purify($params['massedit_module']));
$return_action = 'index';

$url = getBasic_Advance_SearchURL();

if (isset($params['start']) && $params['start']!='') {
	$rstart = '&start=' . urlencode(vtlib_purify($params['start']));
}

if (isset($idlist)) {
	$recordids = explode(';', $idlist);
		$recordcount = count($recordids)-1;
		$id = 1;
	for ($index = 0; $index < $recordcount; ++$index) {
		$recordid = $recordids[$index];
		if ($recordid == '' || in_array(getSalesEntityType($recordid), $nonSupportedMassEdit)) {
			continue;
		}
		if (isPermitted($currentModule, 'EditView', $recordid) == 'yes') {
			// Save each module record with update value.
			$focus->retrieve_entity_info($recordid, $currentModule);
			$focus->mode = 'edit';
			$focus->id = $recordid;
			foreach ($focus->column_fields as $fieldname => $val) {
								$fldname = $fieldname.'_mass_edit_check';
				if (isset($params[$fldname])) {
					if ($fieldname == 'assigned_user_id') {
						if ($params['assigntype'] == 'U') {
							$value = vtlib_purify($params['assigned_user_id']);
						} elseif ($params['assigntype'] == 'T') {
							$value = vtlib_purify($params['assigned_group_id']);
						}
					} else {
						if (!isset($params[$fieldname])) {
							$value = '';
						} elseif (is_array($params[$fieldname])) {
							$value = vtlib_purify($params[$fieldname]);
						} else {
							$value = trim(vtlib_purify($params[$fieldname]));
						}
					}
					$focus->column_fields[$fieldname] = $value;
				} else {
					$focus->column_fields[$fieldname] = decode_html($focus->column_fields[$fieldname]);
				}
			}
			list($saveerror,$errormessage,$error_action,$returnvalues) = $focus->preSaveCheck($params);
			if (!$saveerror) { // if there is an error we ignore this record
				$msg = $app_strings['record'].' '.$recordid.' '.$app_strings['saved'];
				$focus->save($currentModule);
			} else {
				$msg = $app_strings['record'].' '.$recordid.' '.$app_strings['notsaved'].$errormessage;
			}
		}
		$recordprocessed++;
		$progress = $recordprocessed / $recordcount * 100;
		send_message($id++, $msg, $progress, $recordprocessed, $recordcount, $app_strings);
	}
}
send_message('CLOSE', $app_strings['processcomplete'], 100, $recordcount, $recordcount, $app_strings);
?>