<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$sqlBA="SELECT * FROM vtiger_links INNER JOIN vtiger_crmentity ON vtiger_links.linksid=vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted=0";

$sqlBAR=$adb->pquery($sqlMistermatic1, array());


  while ($therecid = $adb -> fetch_array($sqlBAR)) {
                    $focusnew = new BusinessActions();
                    $focusnew->column_fields['assigned_user_id'] = $current_use->id;
                    $focusnew->column_fields['emerfushe']= $therecid['fushateklinks'];
                    $focusnew->save('BusinessActions');
}