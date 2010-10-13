<?php  // $Id: view.php,v 1.6.2.3 2009/04/17 22:06:25 skodak Exp $

/**
 * This page prints a particular instance of praxe_record(s) for external_headmasters
 * and allows them to manage their schools and locations
 *
 * @author  Your Name <your@email.address>
 * @version $Id: view.php,v 1.6.2.3 2009/04/17 22:06:25 skodak Exp $
 * @package mod/praxe
 */
/*
if(!has_capability('mod/praxe:editownlocation',$context)) {
	error("You don't have rights for this action!");
}
*/
/// extending class for classes "praxe_view_[role]" ///
require_once($CFG->dirroot . '/mod/praxe/c_praxe_view.php');

class praxe_view_extteacher extends praxe_view {
	
	function praxe_view_extteacher() {
		global $CFG, $tab, $USER, $tab_modes, $context;		
		
		switch($tab) {
			case PRAXE_TAB_EXTTEACHER_HOME :				
				require_capability('mod/praxe:viewrecordstoownlocation',$context);
				$detail = optional_param('recordid',0,PARAM_INT);
				if($detail > 0 && ($record = praxe_get_record($detail))) {
					if($record->teacherid != $USER->id) {
						error(get_string('notallowedaction','praxe'));
					}					
										
					$schid = optional_param('scheduleid',0,PARAM_INT);
					if($schid > 0 && ($schedule = praxe_get_schedule($schid))) {
						$this->content .= self::show_schedule_detail($schedule)."<hr>";	
					}
					$this->content .= self::show_record_detail($record);
				}else {
					$this->content .= self::show_records();
				}
				
				break;
			case PRAXE_TAB_EXTTEACHER_MYLOCATIONS :
				$this->content .= self::show_all_my_locations();
				
				break;
			case PRAXE_TAB_EXTTEACHER_MYSCHOOLS :				
				$schoolid = optional_param('schoolid', 0, PARAM_INT);								
				if(empty($schoolid)) {
					$schools = praxe_get_schools(null, $USER->id);
					$this->content .= self::show_schools($schools);
				}else{
					$schools = praxe_get_schools(null, $USER->id);
					if(isset($schools[$schoolid])) {
						require_once($CFG->dirroot . '/mod/praxe/view_headm.php');
						$this->content .= praxe_view_headm::show_school($schoolid);
					}else {					
						redirect(praxe_get_base_url(),get_string('notallowedaction','praxe'));
					}
				}
				
				break;
			case PRAXE_TAB_EXTTEACHER_EDITLOCATION :
				$locid = required_param('locationid',PARAM_INT);
				if(!$loc = praxe_get_location($locid, $USER->id)) {
					error(get_string('notallowedaction','praxe'));
				}
				require_once($CFG->dirroot . '/mod/praxe/c_addlocation.php');
				$this->form = new praxe_addlocation($loc->school);
				$this->form->set_redirect_url(null, array('mode'=>$tab_modes['extteacher'][PRAXE_TAB_EXTTEACHER_MYLOCATIONS]));
				$this->form->set_form_to_edit($loc);				
				
				break;
			case PRAXE_TAB_EXTTEACHER_COPYLOCATION :
				/*
				$locid = required_param('locationid',PARAM_INT);
				if(!$loc = praxe_get_location($locid, $USER->id)) {
					error(get_string('notallowedaction','praxe'));
				}
				require_once($CFG->dirroot . '/mod/praxe/c_addlocation.php');
				$this->form = new praxe_addlocation($loc->school);
				//$this->form->set_redirect_url(null, array('mode'=>$tab_modes['extteacher'][PRAXE_TAB_EXTTEACHER_MYLOCATIONS]));
				//$this->form->set_form_to_edit($loc);
				$this->form->set_form_to_copy($loc);				
				*/
				break;	
			default:				
				break;
		}			
	}

	public function active_actual_user_records($userid, $praxeid=null, $order=null) {		
		$ret = array();
		if(is_null($praxeid)) {
			$praxeid = praxe_record::getData('id');
		}
		$all = praxe_get_praxe_records($praxeid, $order);		
		if(is_array($all)) {			
			foreach($all as $k=>$rec) {
				if($rec->teacherid == $userid) {
					$ret[] = $rec;
				}
			}			
		}
		return $ret;
	}
	
	public function show_records($records = array()) {
		global $USER, $mode;		
		/// no records set and this method is called from this class ///
		if(!count($records) && get_class($this) == 'praxe_view_extteacher') {
			$records = self::active_actual_user_records($USER->id, null, array('status','name','lastname','firstname'));
		}
		if(!count($records)) {
			return false;			
		}
				
		$table = new stdClass();
		$strstud = get_string('student','praxe');
		$strstatus = get_string('status','praxe');
		$strschool = get_string('school','praxe');
		$strsubject = get_string('subject','praxe');
		$strteacher = get_string('teacher','praxe');		
		
		if(praxe_has_capability('viewrecordstoanylocation') || praxe_has_capability('manageallincourse')) {
			$viewteacher = true;
			$table->head  = array ($strstud, $strschool, $strsubject, $strteacher, $strstatus, '&nbsp;');
		}else {
			$viewteacher = false;
			$table->head  = array ($strstud, $strschool, $strsubject, $strstatus, '&nbsp;');
		}
		$table->align = array ('left', 'left', 'left', 'center', 'center');		
		foreach($records as $rec) {
			$row = array();			
			$user = (object) array('firstname'=>$rec->firstname, 'lastname'=>$rec->lastname, 'id'=>$rec->userid);
			$row[] = praxe_get_user_fullname($user);			
			$row[] = s($rec->schoolname);
			$row[] = s($rec->subject);
			if($viewteacher) {
				$row[] = praxe_get_user_fullname((object)array('id'=>$rec->teacherid, 'firstname'=>$rec->teacher_firstname, 'lastname'=>$rec->teacher_lastname));
			}			
			$stat = praxe_get_status_info($rec->status);
			if($rec->status == PRAXE_STATUS_ASSIGNED) {				
				$stat = self::confirm_location_form($rec->id);				
			}
			$row[] = $stat;
			$url = praxe_get_base_url(array("mode=$mode","recordid=$rec->id"));
			$row[] = "<a href=\"$url\" title=\"\">".get_string('detail','praxe')."</a>";
						
			$table->data[] = $row; 	
		}
		
		return print_table($table,true);		
	}
	
	private function show_all_my_locations() {		
		global $USER, $CFG, $tab_modes;
		
		if(!is_array($all = praxe_get_locations(null, null, null, true))) {
			return '';
		}
		$table = new stdClass();
		$strname = get_string('schoolname','praxe');
		$strsubject = get_string('subject','praxe');
		$strstudy = get_string('studyfield','praxe');		
		$strisced = get_string('iscedlevel','praxe');
		$stryear = get_string('year','praxe');
		$strterm = get_string('term','praxe');		
		$table->head = array($strname, $strsubject, $strisced, $strstudy, $stryear, $strterm, get_string('active','praxe'), get_string('edit')); 
		//$table->head[] = get_string('create_new_by_copy','praxe');
		$table->align = array ('left', 'left', 'left', 'left', 'center', 'center', 'center');
		//$table->align[] = 'center';
		$data = array();
		$stredit = get_string('edit');
		foreach($all as $loc) {
			if($loc->teacherid != $USER->id) {
				continue;
			}
			$row = array(s($loc->name), s($loc->subject), praxe_get_isced_text($loc->isced), s($loc->studyfieldname)." (".s($loc->shortcut).")");
			$row[] = s($loc->year);
			$row[] = praxe_get_term_text($loc->term);
			$row[] = ($loc->active == 1) ? get_string('yes') : get_string('no');
			if(praxe_has_capability('editanylocation') || (praxe_has_capability('editownlocation') && $USER->id == $loc->teacherid)
				&& !get_record('praxe_records','location',$loc->id)) {
				$par = "&amp;mode=".$tab_modes['extteacher'][PRAXE_TAB_EXTTEACHER_EDITLOCATION]."&amp;locationid=$loc->id";				
				$row[] = "<a title=\"$stredit\" href=\"".praxe_get_base_url().$par."\">"
								."<img src=\"$CFG->pixpath/t/edit.gif\" class=\"iconsmall\" alt=\"$stredit\" /></a> ";	
			}else{
				$row[] = get_string('already_used','praxe');
			}			
			//$par = "&amp;mode=".$tab_modes['extteacher'][PRAXE_TAB_EXTTEACHER_COPYLOCATION]."&amp;locationid=$loc->id";
			//$row[] = "<a title=\"".get_string('copy')."\" href=\"".praxe_get_base_url().$par."\">".get_string('copy')."</a> ";
			
			$data[] = $row;			 
		}
		if(count($data)) {
			$table->data = $data;
			return print_table($table,true);
		}
		return '';	
	}
	
	/**
	 * 
	 * @param object $rec - object of praxe record to be shown
	 * @return string
	 */
	public function show_record_detail($rec) {
		global $mode, $USER, $CFG;
		
		/// left top table ///
		$tab1 = new stdClass();
		$tab1->align = array('right', 'left');
		$tab1->width = '40%';
		$tab1->class = "floatinfotable left twocolstable";
		$tab1->data[] = array(get_string('school','praxe').": ", $rec->name);
		$tab1->data[] = array(get_string('subject','praxe').": ", $rec->subject);
		if($USER->id != $rec->teacherid) {
			$tab1->data[] = array(get_string('teacher','praxe').": ", praxe_get_user_fullname((object)array('id'=>$rec->teacherid, 'firstname'=>$rec->teacher_firstname, 'lastname'=>$rec->teacher_lastname)));
		}		
		
		/// right top table ///
		$tab2 = new stdClass();
		$tab2->align = array('right', 'left');
		$tab2->width = '40%';
		$tab2->class = "floatinfotable right twocolstable";		
		$tab2->data[] = array(get_string('student', 'praxe').": ", praxe_get_user_fullname($rec->student));
		$tab2->data[] = array(get_string('status','praxe').": ", praxe_get_status_info($rec->status));
		
		$return = print_table($tab1, true) . print_table($tab2, true) . '<div class="clearer"></div>';
		$return .= "<h3>".get_string('schedule','praxe')."</h3>";
		if(!is_array($schedules = praxe_get_schedules($rec->id))) {
			return $return . get_string('no_schedule_items','praxe');
		}		
		$sched = array();
		$cols = array();
		
		foreach($schedules as $sch) {
			if(!isset($sched[date("j_m",$sch->timestart)])) {
				$sched[date("j_m",$sch->timestart)] = array('date'=>$sch->timestart);
			}			
			$sched[date("j_m",$sch->timestart)][$sch->lesnumber] = $sch;
			if(!in_array($sch->lesnumber,$cols)) {
				$cols[] = $sch->lesnumber;
			}			   
		}
		/// schedule table ///		
		$tab3 = new stdClass();
		sort($cols);		
		$tab3->head = $cols;
		array_unshift($tab3->head, get_string('date'));
		for($i = 1; $i < count($tab3->head); $i++) {
			$tab3->head[$i] = s($tab3->head[$i]).".".get_string('lesson','praxe');
		}

		foreach($sched as $row) {
			$datetd = userdate($row['date'],get_string('strftimeday','praxe'))."<br />".userdate($row['date'],get_string('strftimedateshort'));
			$r = array($datetd);
			$r3 = "<th class=\"header first\">$datetd</th>";
			foreach($cols as $k=>$c) {
				if(isset($row[$c])) {
					//if($row[$c]->lesnumber == $cols[$c]) {
						$item = userdate($row[$c]->timestart,get_string('strftimetime'))." - ".userdate($row[$c]->timeend,get_string('strftimetime'))
								."<br>".s($row[$c]->lessubject)
								."<br>".get_string('schoolroom','praxe').": ".s($row[$c]->schoolroom);
						$item = "<div><a href=\"".praxe_get_base_url(array("mode=$mode","recordid=$rec->id","scheduleid=".$row[$c]->id))."\" title=\"".get_string('detail','praxe')."\">$item</a>"
								."</div>";
						if(count($row[$c]->inspectors)) {							
							foreach($row[$c]->inspectors as $insp) {
								$item .= "<div class=\"inspector right\"><img src=\"{$CFG->wwwroot}/mod/praxe/img/icon_inspect.gif\" alt=\"".get_string('inspection','praxe').":\" title=\"".get_string('inspection','praxe')."\" />&nbsp;".praxe_get_user_fullname($insp)."</div>";
							}
						
						}else if(praxe_has_capability('assignselftoinspection')) {
							$add_insp = "<div class=\"inspector right\"><form method=\"post\">";
							$add_insp .= '<input type="hidden" name="post_form" value="assigntoinspection" />';
							$add_insp .= '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
							$add_insp .= '<input type="hidden" name="scheduleid" value="'.$row[$c]->id.'" />';
							$add_insp .= '<input type="hidden" name="userid" value="'.$USER->id.'" />';
							$add_insp .= '<input type="hidden" name="submitbutton" value="true" />';						
							$add_insp .= '<input type="submit" name="submitconfirm" value="'.get_string('gotoinspection','praxe').'" />';
							$add_insp .= '</form></div>';
							$item .= $add_insp; 
						}						
						$r[] = $item;
						$r3 .= "<td>$item</td>";
						
					//}
				}else {
					$r[] = '&nbsp;';
					$r3 .= '<td>&nbsp;</td>';
				}
			}
			$tab3->data[] = $r3;
		}
		$t3 = "<table cellspacing=\"1\" class=\"scheduletable boxalignleft\">\n<tbody>\n";
		$t3 .= "<tr>\n";
		foreach($tab3->head as $i=>$th) {
			if($i == 0) {
				$t3 .= "<th class=\"header first\">$th</th>";
			}else {
				$t3 .= "<th class=\"header\">$th</th>";
			}
		}
		$t3 .= "</tr>\n";
		foreach($tab3->data as $tr) {		
			$t3 .= "<tr>$tr</tr>";
		}
		$t3 .= "</tbody></table>";
		return $return . $t3;
	}
	
	public function show_schedule_detail($schedule) {
		
		$tab2 = new stdClass();		
		$tab2->align = array('right', 'left');
		$tab2->size = array('150px');
		$tab2->cellpadding = '2px';
		$tab2->class = "twocolstable";
		$tab2->data[] = array(get_string('time').": ", userdate($schedule->timestart, get_string('strftimedayshort'))."&nbsp;&nbsp;".userdate($schedule->timestart,get_string('strftimetime'))." - ".userdate($schedule->timeend,get_string('strftimetime')));
		$tab2->data[] = array(get_string('schoolroom','praxe').": ", s($schedule->schoolroom));		
		$tab2->data[] = array(get_string('yearclass','praxe').": ", praxe_get_yearclass($schedule->yearclass));
		$tab2->data[] = array(get_string('subject','praxe').": ", s($schedule->lessubject));
		$tab2->data[] = array(get_string('lesson_theme','praxe').": ", format_text($schedule->lestheme));
		
		return "<h3>".get_string('lessondetail','praxe')."</h3>".print_table($tab2,true);		
	}
	
	/**
	 * 
	 * @param array $schools - array of schools objects to be displayed 
	 * @param array $editlinkparams[optional] - default parameters to url that are always added: id=$cm->id, schoolid=id of school.<br>
	 * Extra parameters to be added must be in array format as nameOfParameter=>value 
	 * @return string 
	 */
	public function show_schools($schools) {		
		global $tab_modes;
		
		$table = new stdClass();
		$strname = get_string('schoolname','praxe');
		$strtype = get_string('schooltype','praxe');
		$straddress = get_string('address','praxe');
		$strcontact = get_string('contact','praxe');
		$table->head = array($strname, $strtype, $straddress, $strcontact);
		$table->align = array ('left', 'left', 'left', 'left');		
		foreach($schools as $sch) {
			$schname = "<a href='".praxe_get_base_url()."&amp;mode=".$tab_modes['extteacher'][PRAXE_TAB_EXTTEACHER_MYSCHOOLS]."&amp;schoolid=$sch->id' title='".get_string('detail','praxe')."'>".s($sch->name)."</a>";
			$address = s($sch->street).', '.s($sch->zip).'  '.s($sch->city);			
			$contact = new stdClass();
			//$contact->head = array('');
			$contact->data = array();
			if(!empty($sch->phone)) {
				$contact->data[] = array(s($sch->phone));
			}
			if(!empty($sch->email)) {
				$contact->data[] = array(s($sch->email));
			}
			if(!empty($sch->website)) {
				$contact->data[] = array(s($sch->website));
			}
			eval('$schooltype =  PRAXE_SCHOOL_TYPE_'.(int)$sch->type.'_TEXT;');
			$contact = print_table($contact,true);
			$table->data[] = array($schname, $schooltype, $address, $contact);						 
		}		
		
		return print_table($table, true);
	}
	
	public function confirm_location_form($recordid) {
		$form = '<div>'.get_string('please_confirm_record','praxe').'</div>';
		$form .= '<form action="'.praxe_get_base_url().'" method="post">';
		$form .= '<input type="hidden" name="post_form" value="confirmlocation" />';
		$form .= '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
		$form .= '<input type="hidden" name="recordid" value="'.$recordid.'" />';
		$form .= '<input type="hidden" name="submitbutton" value="true" />';
		$form .= '<input type="submit" name="submitconfirm" value="'.get_string('confirm').'" />';
		$form .= '&nbsp;&nbsp;<input type="submit" name="submitrefuse" value="'.get_string('refuse','praxe').'" />';
		$form .= '</form>';
		return $form;
	}
	
	public function location_edit_form($locid) {
		require_once($CFG->dirroot . '/mod/praxe/c_addlocation.php');
		$this->form = new praxe_addlocation($loc->school);
		$this->form->set_redirect_url(null, array('mode'=>$tab_modes['editteacher'][PRAXE_TAB_EDITTEACHER_LOCATIONS], 'schoolid'=>$schoolid));
		$this->form->set_form_to_edit($loc);
	}
}
/// no schools assigned to user yet ///





?>
