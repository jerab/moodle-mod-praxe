<?php  // $Id: tabs.php,v 1.7 2007/10/09 21:43:29 iarenaza Exp $

	//$toolsrow = array();
    $viewtabrows = array();
    $subtabrows = array();
    $inactive = array();
    $activated = array();
    $tab_content = '';
	
/*
    if (!has_capability('mod/glossary:approve', $context) && $tab == GLOSSARY_APPROVAL_VIEW) {
    /// Non-teachers going to approval view go to defaulttab
        $tab = $defaulttab;
    }
	*/
    switch ($tabview) {
        case PRAXE_TAB_VIEW_STUDENT :
            $viewtabrows[] = new tabobject(PRAXE_TAB_STUDENT_HOME, $CFG->wwwroot.'/mod/praxe/view.php?id='.$cm->id.'&amp;mode='.$tab_modes['student'][PRAXE_TAB_STUDENT_HOME], get_string('my_praxe', 'praxe'));
            
            /// selected location ///
            if(praxe_record::getData('rec_id')) {
            	$status = praxe_record::getData('rec_status');
            	if( $status == PRAXE_STATUS_REFUSED 
            		|| ($tab == PRAXE_TAB_STUDENT_MYSCHOOL && $status < PRAXE_STATUS_CONFIRMED) ) {
            			$subtabrows[] = new tabobject(PRAXE_TAB_STUDENT_EDITLOC, $CFG->wwwroot.'/mod/praxe/view.php?id='.$cm->id.'&amp;mode='.$tab_modes['student'][PRAXE_TAB_STUDENT_EDITLOC], get_string('changelocation','praxe'));
            	} 
            	$viewtabrows[] = new tabobject(PRAXE_TAB_STUDENT_MYSCHOOL, $CFG->wwwroot.'/mod/praxe/view.php?id='.$cm->id.'&amp;mode='.$tab_modes['student'][PRAXE_TAB_STUDENT_MYSCHOOL], get_string('my_selected_location', 'praxe'));
            	            
            	if($status >= PRAXE_STATUS_CONFIRMED) {    
            		$viewtabrows[] = new tabobject(PRAXE_TAB_STUDENT_SCHEDULE, $CFG->wwwroot.'/mod/praxe/view.php?id='.$cm->id.'&amp;mode='.$tab_modes['student'][PRAXE_TAB_STUDENT_SCHEDULE], get_string('my_schedule', 'praxe'));
            		if($tab == PRAXE_TAB_STUDENT_SCHEDULE) {
            			$activated[] = PRAXE_TAB_STUDENT_SCHEDULE;
            			$subtabrows[] = new tabobject(PRAXE_TAB_STUDENT_ADDSCHEDULE, $CFG->wwwroot.'/mod/praxe/view.php?id='.$cm->id.'&amp;mode='.$tab_modes['student'][PRAXE_TAB_STUDENT_ADDSCHEDULE], get_string('addtoschedule', 'praxe'));
            		}
            	}            
            }
            
            
           
        	break;
        case PRAXE_TAB_VIEW_HEADM :
			$viewtabrows[] = new tabobject(PRAXE_TAB_HEADM_HOME, $CFG->wwwroot.'/mod/praxe/view.php?id='.$cm->id.'&amp;mode='.$tab_modes['headm'][PRAXE_TAB_HEADM_HOME], get_string('my_schools', 'praxe'));
           	$viewtabrows[] = new tabobject(PRAXE_TAB_HEADM_ADDSCHOOL, $CFG->wwwroot.'/mod/praxe/view.php?id='.$cm->id.'&amp;mode='.$tab_modes['headm'][PRAXE_TAB_HEADM_ADDSCHOOL], get_string('addschool', 'praxe'));
           	
           	$viewschool = optional_param('schoolid', null, PARAM_INT);           	
           	/// schoolid is set and user is not headmaster of this school ///
           	if(!empty($viewschool)){
           		$school = praxe_get_school($viewschool);
           		if(!$school || $school->headmaster != $USER->id) {           	
           			error(get_string('notallowedaction','praxe'));
           		}           		
           	}
           	
           	if($tab == PRAXE_TAB_HEADM_HOME || !is_null($viewschool)) {           		
           		$activated[] = PRAXE_TAB_HEADM_HOME;
           		           		
           		if(is_array($schools = praxe_get_schools($USER->id))) {
    	        	//print_object($schools);        	    	 		
           			/// schoolid is not valid(for this user at least) or viewschool is null => set to default view of schools///
	            	if(!array_key_exists($viewschool, $schools)) {
	            		$viewschool = 0;	            		
	            		if($mode != $tab_modes['headm'][PRAXE_TAB_HEADM_TEACHERS] && $mode != $tab_modes['headm'][PRAXE_TAB_HEADM_LOCATIONS]) {
	        				$tab = PRAXE_TAB_HEADM_HOME;
	            			$mode = $tab_modes['headm'][PRAXE_TAB_HEADM_HOME];
	        			}
	            	}
	            	
           			$options = array(0=>get_string('all'));
            		foreach($schools as $sch) {
            			$options[$sch->id] = $sch->name."($sch->city, $sch->street)";
            		}
	            	 
	            	$url = $CFG->wwwroot."/mod/praxe/view.php?id=$cm->id&amp;mode=".$mode."&amp;schoolid=";	            	
	           		$tab_content .= popup_form($url, $options, 'praxepop_praxeschoolselect', $viewschool, '', '', '', true, 'self', get_string('school','praxe'));
	            	$tab_content .= '<hr>';
           		}
           		$url = $CFG->wwwroot.'/mod/praxe/view.php?id='.$cm->id.'&amp;schoolid='.$viewschool;
           		if($schools) {
	           		$subtabrows[] = new tabobject(PRAXE_TAB_HEADM_TEACHERS, $url.'&amp;mode='.$tab_modes['headm'][PRAXE_TAB_HEADM_TEACHERS], get_string('teachers', 'praxe'));
	           		$subtabrows[] = new tabobject(PRAXE_TAB_HEADM_LOCATIONS, $url.'&amp;mode='.$tab_modes['headm'][PRAXE_TAB_HEADM_LOCATIONS], get_string('locations', 'praxe'));
	           		if(!empty($viewschool)) {
	           			$subtabrows[] = new tabobject(PRAXE_TAB_HEADM_EDITSCHOOL, $url.'&amp;mode='.$tab_modes['headm'][PRAXE_TAB_HEADM_EDITSCHOOL], get_string('editschool', 'praxe'));
	           			$subtabrows[] = new tabobject(PRAXE_TAB_HEADM_ASSIGNTEACHERS, $url.'&amp;mode='.$tab_modes['headm'][PRAXE_TAB_HEADM_ASSIGNTEACHERS], get_string('assignteachers', 'praxe'));
	           			$subtabrows[] = new tabobject(PRAXE_TAB_HEADM_ADDLOCATION, $url.'&amp;mode='.$tab_modes['headm'][PRAXE_TAB_HEADM_ADDLOCATION], get_string('addlocation', 'praxe'));
	           		}
           		}
           	}
        	break;
        case PRAXE_TAB_VIEW_EXTTEACHER :
            $viewtabrows[] = new tabobject(PRAXE_TAB_EXTTEACHER_HOME, $CFG->wwwroot.'/mod/praxe/view.php?id='.$cm->id.'&amp;mode='.$tab_modes['extteacher'][PRAXE_TAB_EXTTEACHER_HOME], get_string('my_praxes', 'praxe'));
           	$viewtabrows[] = new tabobject(PRAXE_TAB_EXTTEACHER_MYLOCATIONS, $CFG->wwwroot.'/mod/praxe/view.php?id='.$cm->id.'&amp;mode='.$tab_modes['extteacher'][PRAXE_TAB_EXTTEACHER_MYLOCATIONS], get_string('my_locations', 'praxe'));
           	$viewtabrows[] = new tabobject(PRAXE_TAB_EXTTEACHER_MYSCHOOLS, $CFG->wwwroot.'/mod/praxe/view.php?id='.$cm->id.'&amp;mode='.$tab_modes['extteacher'][PRAXE_TAB_EXTTEACHER_MYSCHOOLS], get_string('my_schools', 'praxe'));
           	if($tab == PRAXE_TAB_EXTTEACHER_MYSCHOOLS) {
           		$viewschool = optional_param('schoolid', null, PARAM_INT);
           		if(is_array($schools = praxe_get_schools(null, $USER->id))) {	
	           		$options = array();
	            	$options[0] = get_string('all');
	            	foreach($schools as $sch) {
	            		$options[$sch->id] = $sch->name."($sch->city, $sch->street)";
	            	}
		            	
	           		$url = $CFG->wwwroot."/mod/praxe/view.php?id=$cm->id&amp;mode=".$mode."&amp;schoolid=";
		            $tab_content .= popup_form($url, $options, 'praxepop_praxeschoolselect', $viewschool, '', '', '', true, 'self', get_string('school','praxe'));
		            $tab_content .= '<hr>';
           		}	
           	}
           	
           	if($tab == PRAXE_TAB_EXTTEACHER_HOME) {
           		$recordid = optional_param('recordid', 0, PARAM_INT);           		
           		$records = praxe_get_praxe_records(praxe_record::getData('id'), null, $USER->id);           		     		
           		if(is_array($records)) {	
	           		$options = array();
	            	$options[0] = get_string('all');
	            	foreach($records as $rec) {
	            		$options[$rec->id] = $rec->schoolname." - ".$rec->subject." - ".praxe_get_user_fullname($rec->student);
	            	}
		            	
	           		$url = praxe_get_base_url(array("mode=$mode","recordid="));
		            $tab_content .= popup_form($url, $options, 'praxepop_recorddetail', $recordid, '', '', '', true, 'self', get_string('praxe','praxe'));
		            $tab_content .= '<hr>';
           		}
           	}
           	
        	break;
        case PRAXE_TAB_VIEW_EDITTEACHER :
            $viewschool = optional_param('schoolid', null, PARAM_INT);		/// filter for schools and subtabs
           	$filteractualloc = optional_param('factualloc', 0, PARAM_INT); ///filter for locations
        	
        	$viewtabrows[] = new tabobject(PRAXE_TAB_EDITTEACHER_HOME, $CFG->wwwroot.'/mod/praxe/view.php?id='.$cm->id.'&amp;mode='.$tab_modes['editteacher'][PRAXE_TAB_EDITTEACHER_HOME], get_string('records_list', 'praxe'));
           	$viewtabrows[] = new tabobject(PRAXE_TAB_EDITTEACHER_SCHOOLS, $CFG->wwwroot.'/mod/praxe/view.php?id='.$cm->id.'&amp;mode='.$tab_modes['editteacher'][PRAXE_TAB_EDITTEACHER_SCHOOLS], get_string('schools', 'praxe'));            
           	if(praxe_has_capability('manageallincourse')) {
           		$viewtabrows[] = new tabobject(PRAXE_TAB_EDITTEACHER_ADDSCHOOL, $CFG->wwwroot.'/mod/praxe/view.php?id='.$cm->id.'&amp;mode='.$tab_modes['editteacher'][PRAXE_TAB_EDITTEACHER_ADDSCHOOL], get_string('addschool', 'praxe'));
           	}
    		if($tab == PRAXE_TAB_EDITTEACHER_HOME) {
           		$recordid = optional_param('recordid', 0, PARAM_INT);           		
           		$records = praxe_get_praxe_records(praxe_record::getData('id'));           		     		
           		if(is_array($records)) {	
	           		$options = array();
	            	$options[0] = get_string('all');
	            	foreach($records as $rec) {
	            		$options[$rec->id] = $rec->schoolname." - ".praxe_get_user_fullname($rec->teacherid)." - ".$rec->subject." - ".praxe_get_user_fullname($rec->student);
	            	}
		            	
	           		$url = praxe_get_base_url(array("mode=$mode","recordid="));
		            $tab_content .= popup_form($url, $options, 'praxepop_recorddetail', $recordid, '', '', '', true, 'self', get_string('praxe','praxe'));
		            $tab_content .= '<hr>';
           		}
           	}           	
           	             
            if($tab == PRAXE_TAB_EDITTEACHER_SCHOOLS || !is_null($viewschool)){
            	$activated[] = PRAXE_TAB_EDITTEACHER_SCHOOLS;            	
            	/// school list exists ///
            	if(is_array($schools = praxe_get_schools())) {
	            	//print_object($schools);
            			            	
	            	/// schoolid is not valid(for this user at least) or viewschool is null => set to default view of schools///
	            	if(!array_key_exists($viewschool, $schools)) {
	            		$viewschool = 0;	            		
	            		if($mode != $tab_modes['editteacher'][PRAXE_TAB_EDITTEACHER_TEACHERS]
	            			&& $mode != $tab_modes['editteacher'][PRAXE_TAB_EDITTEACHER_LOCATIONS]) {
		        				$mode = $tab_modes['editteacher'][PRAXE_TAB_EDITTEACHER_SCHOOLS];
		        				$tab = PRAXE_TAB_EDITTEACHER_SCHOOLS;
	        			}
	            	}
	            	
            		$options = array();
            		$options[0] = get_string('all');
	            	foreach($schools as $sch) {
	            		$options[$sch->id] = $sch->name."($sch->city, $sch->street)";
	            	}
	            		            	            	 
            		$filterurl = $CFG->wwwroot."/mod/praxe/view.php?id=$cm->id&amp;mode=".$mode;
	            	if($filteractualloc) {
	            		$filterurl .= "&amp;factualloc=".(int)$filteractualloc;
	            	}
	            	$filterurl .= "&amp;schoolid=";
	            	$tab_content .= popup_form($filterurl, $options, 'praxepop_praxeschoolselect', $viewschool, '', '', '', true, 'self', get_string('school','praxe'));
	            		            	
            	}  
            
            	$url = $CFG->wwwroot.'/mod/praxe/view.php?id='.$cm->id.'&amp;schoolid='.$viewschool;
	            $subtabrows[] = new tabobject(PRAXE_TAB_EDITTEACHER_VIEWSCHOOL, $url.'&amp;mode='.$tab_modes['editteacher'][PRAXE_TAB_EDITTEACHER_SCHOOLS], get_string('school_detail', 'praxe'));	        			        		
	        	$subtabrows[] = new tabobject(PRAXE_TAB_EDITTEACHER_TEACHERS, $url.'&amp;mode='.$tab_modes['editteacher'][PRAXE_TAB_EDITTEACHER_TEACHERS], get_string('teachers', 'praxe'));
	        	$subtabrows[] = new tabobject(PRAXE_TAB_EDITTEACHER_LOCATIONS, $url.'&amp;mode='.$tab_modes['editteacher'][PRAXE_TAB_EDITTEACHER_LOCATIONS], get_string('locations', 'praxe'));
            	if(!empty($viewschool)) {	            		            		            		           		
	            	//$subtabrows[] = new tabobject(PRAXE_TAB_EDITTEACHER_VIEWSCHOOL, $url.'&amp;mode='.$tab_modes['editteacher'][PRAXE_TAB_EDITTEACHER_SCHOOLS], get_string('school_info', 'praxe'));
	        		if(praxe_has_capability('manageallincourse')) {
            			$subtabrows[] = new tabobject(PRAXE_TAB_EDITTEACHER_EDITSCHOOL, $url.'&amp;mode='.$tab_modes['editteacher'][PRAXE_TAB_EDITTEACHER_EDITSCHOOL], get_string('editschool', 'praxe'));
	           			//$subtabrows[] = new tabobject(PRAXE_TAB_EDITTEACHER_TEACHERS, $url.'&amp;mode='.$tab_modes['editteacher'][PRAXE_TAB_EDITTEACHER_TEACHERS], get_string('teachers', 'praxe'));
	           			$subtabrows[] = new tabobject(PRAXE_TAB_EDITTEACHER_ASSIGNTEACHERS, $url.'&amp;mode='.$tab_modes['editteacher'][PRAXE_TAB_EDITTEACHER_ASSIGNTEACHERS], get_string('assignteachers', 'praxe'));
	           			$subtabrows[] = new tabobject(PRAXE_TAB_EDITTEACHER_ADDLOCATION, $url.'&amp;mode='.$tab_modes['editteacher'][PRAXE_TAB_EDITTEACHER_ADDLOCATION], get_string('addlocation', 'praxe'));
	        		}	           		
	        	}
            	if($tab == PRAXE_TAB_EDITTEACHER_SCHOOLS) {
	           		$activated[] = PRAXE_TAB_EDITTEACHER_VIEWSCHOOL;
	           	}	           	
            }

    		if($tab == PRAXE_TAB_EDITTEACHER_LOCATIONS) {
           		$options = array("0"=>get_string('all'), "1"=>get_string('actual','praxe'));	            	
            	$filterurl = $CFG->wwwroot."/mod/praxe/view.php?id=$cm->id&amp;mode=".$mode;
            	if(!is_null($viewschool)) {
            		$filterurl .= "&amp;schoolid=".(int)$viewschool;
            	}
            	$filterurl .= "&amp;factualloc=";
            	$tab_content .= popup_form($filterurl, $options, 'praxepop_praxelocationsselect', $filteractualloc, '', '', '', true, 'self', get_string('filter','praxe'));
           	}
           	
           	if(isset($filterurl)) {
           		$tab_content .= "<hr />";
           	}
            
        	
           	break;
        default :
        	break;
    }
	/*
    print_object($viewtabrows);
    print_object($subtabrows);
    var_dump(praxe_object_search($tab, $viewtabrows, 'id'));
    var_dump(praxe_object_search($tab, $subtabrows, 'id'));
    */
    if(false === praxe_object_search($tab, $viewtabrows, 'id')) {
    	if(false === praxe_object_search($tab, $subtabrows, 'id')) {
    		$msg = get_string('notallowedaction','praxe');
    		//redirect($CFG->wwwroot.'/mod/praxe/view.php?id='.$cm->id.'&amp;mode='.$tab_modes[strtolower($viewrole)][0], $msg);
    	}
    }
    
/// Put all this info together

    $tabrows = array();
    $tabrows[] = $viewtabrows;     // Always put these at the top
    
    if (count($subtabrows)) {
        $tabrows[] = $subtabrows;
    }
	

?>
  <div class="praxedisplay">


<?php //if (count($viewtabrows)) { 
	print_tabs($tabrows, $tab, $inactive, $activated);
	echo $tab_content; 
//} 
?>
  <div class="entrybox">