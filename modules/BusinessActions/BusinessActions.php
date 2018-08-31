<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'data/CRMEntity.php';
require_once 'data/Tracker.php';

class BusinessActions extends CRMEntity {
	public $db;
	public $log;

	public $table_name = 'vtiger_businessactions';
	public $table_index = 'businessactionsid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
        const IGNORE_MODULE = -1;
	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_businessactionscf', 'businessactionsid');
	// related_tables variable should define the association (relation) between dependent tables
	// FORMAT: related_tablename => array(related_tablename_column[, base_tablename, base_tablename_column[, related_module]] )
	// Here base_tablename_column should establish relation with related_tablename_column
	// NOTE: If base_tablename and base_tablename_column are not specified, it will default to modules (table_name, related_tablename_column)
	// Uncomment the line below to support custom field columns on related lists
	// var $related_tables = array('vtiger_MODULE_NAME_LOWERCASEcf' => array('MODULE_NAME_LOWERCASEid', 'vtiger_MODULE_NAME_LOWERCASE',
	// 'MODULE_NAME_LOWERCASEid', 'MODULE_NAME_LOWERCASE'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_businessactions', 'vtiger_businessactionscf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_businessactions' => 'businessactionsid',
		'vtiger_businessactionscf' => 'businessactionsid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'businessactions_no' => array('businessactions' => 'businessactions_no'),
		'linklabel' => array('businessactions' => 'linklabel'),
		'linktype' => array('businessactions' => 'elementtype_action'),
		'module_list' => array('businessactions' => 'module_list'),
		'active' => array('businessactions' => 'active'),
		'Assigned To' => array('crmentity' => 'smownerid'),
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'businessactions_no' => 'businessactions_no',
		'linklabel' => 'linklabel',
		'linktype' => 'elementtype_action',
		'module_list' => 'module_list',
		'active' => 'active',
		'Assigned To' => 'assigned_user_id',
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'businessactions_no';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'businessactions_no' => array('businessactions' => 'businessactions_no'),
		'linklabel' => array('businessactions' => 'linklabel'),
		'linktype' => array('businessactions' => 'elementtype_action'),
		'module_list' => array('businessactions' => 'module_list'),
		'active' => array('businessactions' => 'active'),
		'Assigned To' => array('crmentity' => 'smownerid'),
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'businessactions_no' => 'businessactions_no',
		'linklabel' => 'linklabel',
		'linktype' => 'elementtype_action',
		'module_list' => 'module_list',
		'active' => 'active',
		'Assigned To' => 'assigned_user_id',
	);

	// For Popup window record selection
	public $popup_fields = array('businessactions_no');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'businessactions_no';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'businessactions_no';

	// Required Information for enabling Import feature
	public $required_fields = array('businessactions_no' => 1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'businessactions_no';
	public $default_sort_order = 'ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'businessactions_no');

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	public function vtlib_handler($modulename, $event_type) {
		if ($event_type == 'module.postinstall') {
			// TODO Handle post installation actions
			$this->setModuleSeqNumber('configure', $modulename, $modulename.'bact-', '0000001');
		} elseif ($event_type == 'module.disabled') {
			// TODO Handle actions when this module is disabled.
		} elseif ($event_type == 'module.enabled') {
			// TODO Handle actions when this module is enabled.
		} elseif ($event_type == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} elseif ($event_type == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} elseif ($event_type == 'module.postupdate') {
			// TODO Handle actions after this module is updated.
		}
	}
        /**
	 * Implode the prefix and suffix as string for given number of times
	 * @param String prefix to use
	 * @param Integer Number of times 
	 * @param String suffix to use (optional)
	 */
        static function implodestr($prefix, $count, $suffix=false) {
		$strvalue = '';
		for($index = 0; $index < $count; ++$index) {
			$strvalue .= $prefix;
			if($suffix && $index != ($count-1)) {
				$strvalue .= $suffix;
			}
		}
		return $strvalue;
	}
        public static function do_filter($filtername, $parameter) {
		$startTime = microtime(true);

		// load the Cache for this Filter
		if(self::$_filterCache === false || !isset(self::$_filterCache[$filtername])) {
			self::_loadFilterCache($filtername);
		}

		// if no filter is registerd only return $parameter
		if(!isset(self::$_filterCache[$filtername]) || count(self::$_filterCache[$filtername]) == 0) {
			return $parameter;
		}

		foreach(self::$_filterCache[$filtername] as $filter) {
			self::$numCounter[1]++;

			// if not used before this, create the Handler Class
			if(!isset(self::$_objectCache[$filter['handler_path'].'/'.$filter['handler_class']])) {
				require_once($filter['handler_path']);
				$className = $filter['handler_class'];
				self::$_objectCache[$filter['handler_path'].'#'.$filter['handler_class']] = new $className();
			}

			$obj = self::$_objectCache[$filter['handler_path'].'#'.$filter['handler_class']];

			$startTime2 = microtime(true);
			// call the filter and set the return value again to $parameter
			$parameter = $obj->handleFilter($filtername, $parameter);
			self::$CounterInternal += (microtime(true) - $startTime2);
		}

		self::$Counter += (microtime(true) - $startTime);

		return $parameter;
	}
        /**
	 * Get all the link related to module based on type
	 * @param Integer Module ID
	 * @param mixed String or List of types to select
	 * @param Map Key-Value pair to use for formating the link url
	 */
                static function getAllByType($tabid, $type=false, $parameters=false) {
                
		global $adb,$log,$current_user;
                $module=  $parameters['MODULE'];
                $record=  $parameters['RECORD'];
		$multitype = false;
                $orderby = ' order by elementtype_action, sequence'; //MSL
                if($type) {
			// Multiple link type selection?
			if(is_array($type)) { 
				$multitype = true;
				if($tabid === self::IGNORE_MODULE) {
					$sql = 'SELECT * FROM vtiger_businessactions'
                                                . ' INNER JOIN vtiger_crmentity ce ON ce.crmid=vtiger_businessactions.businessactionsid'
                                                . ' where ce.deleted=0'
                                                . ' and elementtype_action IN ('.
						Vtiger_Utils::implodestr('?', count($type), ',') .') ';
					$params = $type;
					$permittedModuleList = getPermittedModuleNames();
					if(count($permittedModuleList) > 0 && $current_user->is_admin !== 'on') {
						$sql .= ' and module_list IN ('.
							Vtiger_Utils::implodestr('?', count($permittedModuleList), ',').')';
						$params[] = $permittedModuleList;
					}
					$result = $adb->pquery($sql . $orderby, Array($adb->flatten_array($params)));
				} else {
					$result = $adb->pquery('SELECT * FROM vtiger_businessactions'
                                                . ' INNER JOIN vtiger_crmentity ce ON ce.crmid=vtiger_businessactions.businessactionsid'
                                                . ' where ce.deleted=0'
                                                . ' and module_list=? and elementtype_action IN ('.
						Vtiger_Utils::implodestr('?', count($type), ',') .')' . $orderby,
							Array($module, $adb->flatten_array($type)));
				}
			} else {
				// Single link type selection
				if($tabid === self::IGNORE_MODULE) {
					$result = $adb->pquery('SELECT * FROM vtiger_businessactions'
                                                . ' INNER JOIN vtiger_crmentity ce ON ce.crmid=vtiger_businessactions.businessactionsid'
                                                . ' where ce.deleted=0'
                                                . ' and elementtype_action =?' . $orderby, Array($type));
				} else {
					$result = $adb->pquery('SELECT * FROM vtiger_businessactions'
                                                . ' INNER JOIN vtiger_crmentity ce ON ce.crmid=vtiger_businessactions.businessactionsid'
                                                . ' where ce.deleted=0  and active="1"'
                                                . ' and module_list=? and elementtype_action=? ' . $orderby , Array($module, $type));				
				}
			}
		} else {
//                    echo $orderby;exit;
			$result = $adb->pquery('SELECT * FROM vtiger_businessactions'
                                                . ' INNER JOIN vtiger_crmentity ce ON ce.crmid=vtiger_businessactions.businessactionsid'
                                                . ' where ce.deleted=0 '
                                                . ' and module_list=? ' . $orderby, Array($module));
		}
		$strtemplate = new Vtiger_StringTemplate();
		if($parameters) {
			foreach($parameters as $key=>$value) {
                            $strtemplate->assign($key, $value);
                        }
		}

		$instances = Array();
                $instance_block = Array();
		if($multitype) {
			foreach($type as $t) $instances[$t] = Array();
		}

		while($row = $adb->fetch_array($result)){
			/** Should the widget be shown */
			$return = cbEventHandler::do_filter('corebos.filter.link.show', array($row, $type, $parameters));
			if($return == false) continue;
			$instance = new self();
			$instance->initialize($row);
			if (!empty($row['handler_path']) && isInsideApplication($row['handler_path'])) {
				checkFileAccessForInclusion($row['handler_path']);
				require_once $row['handler_path'];
				$linkData = new Vtiger_LinkData($instance, $current_user);
				$ignore = call_user_func(array($row['handler_class'], $row['handler']), $linkData);
				if(!$ignore) {
					self::log("Ignoring Link ... ".var_export($row, true));
					continue;
				}
			}
			if($parameters) {
				$instance->linkurl = $strtemplate->merge($instance->linkurl);
				$instance->linkicon= $strtemplate->merge($instance->linkicon);
			}
			if($multitype) {
				$instances[$instance->linktype][] = $instance;
			} else {
				$instances[$instance->linktype] = $instance;
			}
		}
		return $instances;
	}
	/**
	 * Handle saving related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	// public function save_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle deleting related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//public function delete_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle getting related list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//public function get_related_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

	/**
	 * Handle getting dependents list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//public function get_dependents_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }
}
//BusinessActions:: getAllByType(65,false,false);
