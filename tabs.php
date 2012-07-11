<?php  // $Id: tabs.php,v 1.7 2007/10/09 21:43:29 iarenaza Exp $

	//$toolsrow = array();
    $viewtabrows = array();
    $subtabrows = array();
    $inactive = array();
    $activated = array();
    $tab_content = '';

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
           			print_error('notallowedaction', 'praxe');
           		}
           	}
           	if($tab == PRAXE_TAB_HEADM_HOME || !is_null($viewschool)) {
           		$activated[] = PRAXE_TAB_HEADM_HOME;
           		if($schools = praxe_get_schools($USER->id)) {
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
	            	$tab_content .= $OUTPUT->single_select(praxe_get_base_url(array('mode'=>$mode)), 'schoolid', $options, $viewschool, null, 'praxepop_praxeschoolselect');
           		}
           		$url = praxe_get_base_url(array('schoolid'=>$viewschool));
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
           		if($schools = praxe_get_schools(null, $USER->id)) {
	           		$options = array();
	            	$options[0] = get_string('all');
	            	foreach($schools as $sch) {
	            		$options[$sch->id] = $sch->name."($sch->city, $sch->street)";
	            	}
	           		$url = praxe_get_base_url(array('mode'=>$mode));
	           		$tab_content .= $OUTPUT->single_select($url, 'schoolid', $options, $viewschool, null, 'praxepop_praxeschoolselect');
           		}
           	}
           	if($tab == PRAXE_TAB_EXTTEACHER_HOME) {
           		$recordid = optional_param('recordid', 0, PARAM_INT);
           		$records = praxe_get_praxe_records(praxe_record::getData('id'), null, $USER->id);
           		if($records) {
	           		$options = array();
	            	$options[0] = get_string('all');
	            	foreach($records as $rec) {
	            		$options[$rec->id] = $rec->schoolname." - ".$rec->subject." - ".praxe_get_user_fullname($rec->student);
	            	}
	           		$tab_content .= $OUTPUT->single_select(praxe_get_base_url(array('mode'=>$mode)), 'recordid', $options, $recordid, null, 'praxepop_recorddetail');
           		}
           	}else if($tab == PRAXE_TAB_EXTTEACHER_MYLOCATIONS) {
	           	$filteractualloc = optional_param('factualloc', 0, PARAM_INT);
	           	$filteryearloc = optional_param('fyearloc', 0, PARAM_INT);
           	    ///filter for actual locations ///

           	    $options = array("0"=>get_string('all'), "1"=>get_string('actual','praxe'));
	            $params = array('mode'=>$mode, 'fyearloc'=>$filteryearloc);
	            $sel = new single_select(praxe_get_base_url($params), 'factualloc', $options, $filteractualloc, null, 'praxepop_praxelocationsselect');
	            $sel->label = get_string('only_actual','praxe');
	            $tab_content .= $OUTPUT->render($sel);

	            /// year filter for locations///
                $years = praxe_get_years_for_filter(null,null,$filteractualloc,$USER->id);

                $options = array("0"=>get_string('all'));
           	    foreach($years as $year) {
           	        $options[$year->year] = $year->year;
           	    }
	            $params = array('mode'=>$mode, 'factualloc'=>$filteractualloc);
	            $sel = new single_select(praxe_get_base_url($params), 'fyearloc', $options, $filteryearloc, null, 'praxepop_praxelocationsselectyear');
	            $sel->label = get_string('year','praxe');
	            $tab_content .= $OUTPUT->render($sel);
            }
        	break;
        case PRAXE_TAB_VIEW_EDITTEACHER :
            $viewschool = optional_param('schoolid', null, PARAM_INT);		/// filter for schools and subtabs
            $filteractualloc = optional_param('factualloc', 0, PARAM_INT); ///filter for locations actual
    		$filteryearloc = optional_param('fyearloc', 0, PARAM_INT);

        	$viewtabrows[] = new tabobject(PRAXE_TAB_EDITTEACHER_HOME, $CFG->wwwroot.'/mod/praxe/view.php?id='.$cm->id.'&amp;mode='.$tab_modes['editteacher'][PRAXE_TAB_EDITTEACHER_HOME], get_string('records_list', 'praxe'));
           	$viewtabrows[] = new tabobject(PRAXE_TAB_EDITTEACHER_SCHOOLS, $CFG->wwwroot.'/mod/praxe/view.php?id='.$cm->id.'&amp;mode='.$tab_modes['editteacher'][PRAXE_TAB_EDITTEACHER_SCHOOLS], get_string('schools', 'praxe'));
           	if(praxe_has_capability('manageallincourse')) {
           		$viewtabrows[] = new tabobject(PRAXE_TAB_EDITTEACHER_ADDSCHOOL, $CFG->wwwroot.'/mod/praxe/view.php?id='.$cm->id.'&amp;mode='.$tab_modes['editteacher'][PRAXE_TAB_EDITTEACHER_ADDSCHOOL], get_string('addschool', 'praxe'));
           	}
    		if($tab == PRAXE_TAB_EDITTEACHER_HOME) {
           		$recordid = optional_param('recordid', 0, PARAM_INT);
           		$records = praxe_get_praxe_records(praxe_record::getData('id'));
           		if($records) {
	           		$options = array();
	            	$options[0] = get_string('all');
	            	foreach($records as $rec) {
	            		$options[$rec->id] = $rec->schoolname." - ".praxe_get_user_fullname($rec->teacherid)." - ".$rec->subject." - ".praxe_get_user_fullname($rec->student);
	            	}
	           		$sel = new single_select(praxe_get_base_url(array('mode'=>$mode)), 'recordid', $options, $recordid, null, 'praxepop_recorddetail');
	            	$sel->label = get_string('praxe','praxe');
	            	$tab_content .= $OUTPUT->render($sel);
           		}
           	}
            if($tab == PRAXE_TAB_EDITTEACHER_SCHOOLS || !is_null($viewschool)) {
                $activated[] = PRAXE_TAB_EDITTEACHER_SCHOOLS;
            	/// school list exists ///
            	if(is_array($schools = praxe_get_schools())) {
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
            		$params = array('mode'=>$mode);

	            	$sel = new single_select(praxe_get_base_url($params), 'schoolid', $options, $viewschool, null, 'praxepop_praxeschoolselect');
	            	$sel->label = get_string('school','praxe');
	            	$tab_content .= $OUTPUT->render($sel);
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
    		    $params = array('mode'=>$mode);
    		    if(!is_null($viewschool)) {
            		$params['schoolid'] = (int)$viewschool;
            	}
            	$params1 = $params;
    		    if($filteractualloc) {
	            	$params['fyearloc'] = (int)$filteryearloc;
	            }
            	$sel = new single_select(praxe_get_base_url($params), 'factualloc', $options, $filteractualloc, null, 'praxepop_praxelocationsselect');
            	$sel->label = get_string('only_actual','praxe');
            	$tab_content .= $OUTPUT->render($sel);

    		    /// year filter for locations///
                $years = praxe_get_years_for_filter(null,$viewschool,$filteractualloc);
           	    if(count($years) > 1) {
	                $options = array("0"=>get_string('all'));
	           	    foreach($years as $year) {
	           	        $options[$year->year] = $year->year;
	           	    }
           	        if($filteractualloc) {
	            		$params1['factualloc'] = (int)$filteractualloc;
	            	}
		            $sel = new single_select(praxe_get_base_url($params1), 'fyearloc', $options, $filteryearloc, null, 'praxepop_praxelocationsselectyear');
		            $sel->label = get_string('year','praxe');
		            $tab_content .= $OUTPUT->render($sel);
           	    }
            }
           	break;
        default :
        	break;
    }

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
	if(strlen($tab_content)) {
		echo "<div class=\"filterselector\">$tab_content</div><hr />";
	}
?>
  <div class="entrybox">