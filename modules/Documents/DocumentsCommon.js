/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

var DocumentsCommon = {
	OVERLAYID : '__DocumentsCommonOverlay__',

	initOverlay : function () {
		if (document.getElementById(DocumentsCommon.OVERLAYID)) {
			return;
		}
		var overlaynode = document.createElement('div');
		overlaynode.id = DocumentsCommon.OVERLAYID;
		overlaynode.style.width = '550px';
		overlaynode.style.display = 'none';
		document.body.appendChild(overlaynode);
	},

	showdiff : function (record, atpoint, highlight) {
		DocumentsCommon.initOverlay();

		if ( typeof (atpoint) == 'undefined') {
			atpoint = 0;
		}
		if ( typeof (highlight) == 'undefined') {
			highlight = true;
		}

		document.getElementById('status').style.display='inline';
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?module=Documents&action=DocumentsAjax&file=ShowDiff&id=' + encodeURIComponent(record) + '&atpoint=' + encodeURIComponent(atpoint) + '&highlight=' + encodeURIComponent(highlight),
		}).done(function (response) {
			document.getElementById('status').style.display='none';
			document.getElementById(DocumentsCommon.OVERLAYID).style.display='inline';
			document.getElementById(DocumentsCommon.OVERLAYID).innerHTML = response;
			document.getElementById(DocumentsCommon.OVERLAYID).style.display = 'block';
			placeAtCenter(document.getElementById(DocumentsCommon.OVERLAYID));
		});
	},

	showhistory : function (record, atpoint, highlight) {
		DocumentsCommon.initOverlay();

		if ( typeof (atpoint) == 'undefined') {
			atpoint = 0;
		}
		if ( typeof (highlight) == 'undefined') {
			highlight = false;
		}

		document.getElementById('status').style.display='inline';
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?module=Documents&action=DocumentsAjax&file=ShowDiff&mode=history&id=' + encodeURIComponent(record) + '&atpoint=' + encodeURIComponent(atpoint) + '&highlight=' + encodeURIComponent(highlight),
		}).done(function (response) {
			document.getElementById('status').style.display='none';
			document.getElementById(DocumentsCommon.OVERLAYID).innerHTML = response;
			document.getElementById(DocumentsCommon.OVERLAYID).style.display = 'block';
			placeAtCenter(document.getElementById(DocumentsCommon.OVERLAYID));
		});
	},
	hide : function () {
		document.getElementById(DocumentsCommon.OVERLAYID).style.display='none';
	}
};
