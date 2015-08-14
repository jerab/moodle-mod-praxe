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
	print_error("You don't have rights for this action!");
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
						print_error('notallowedaction', 'praxe');
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
				$factual = optional_param('factualloc', 0, PARAM_INT);
				$fyear = optional_param('fyearloc', 0, PARAM_INT);
			    $this->content .= self::show_all_my_locations($factual, $fyear);
				break;
			case PRAXE_TAB_EXTTEACHER_MYSCHOOLS :
				$schoolid = optional_param('schoolid', 0, PARAM_INT);
				$schools = praxe_get_schools(null, $USER->id);
				if($schoolid == 0) {
					$this->content .= self::show_schools($schools);
				}else if(isset($schools[$schoolid])) {
					require_once($CFG->dirroot . '/mod/praxe/view_headm.php');
					$this->content .= praxe_view_headm::show_school($schoolid);
				}else {
					redirect(praxe_get_base_url(),get_string('notallowedaction','praxe'));
				}
				break;
			case PRAXE_TAB_EXTTEACHER_EDITLOCATION :
				$locid = required_param('locationid',PARAM_INT);
				if(!$loc = praxe_get_location($locid, $USER->id)) {
					print_error('notallowedaction', 'praxe');
				}
				require_once($CFG->dirroot . '/mod/praxe/c_addlocation.php');
				$this->form = new praxe_addlocation($loc->school);
				$this->form->set_redirect_url(null, array('mode'=>$tab_modes['extteacher'][PRAXE_TAB_EXTTEACHER_MYLOCATIONS]));
				$this->form->set_form_to_edit($loc);
				break;
			case PRAXE_TAB_EXTTEACHER_COPYLOCATION :
				// TODO
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
		$all = praxe_get_praxe_records($praxeid, $order, null, null, true);
		if(is_array($all)) {
			foreach($all as $k=>$rec) {
				if($rec->teacherid == $userid) {
					$ret[] = $rec;
				}
			}
		}
		return $ret;
	}

	public static function show_records($records = array()) {
		global $USER, $mode;
		/// no records set ///
		if(!count($records)) {
			$records = self::active_actual_user_records($USER->id, null, array('status','name','lastname','firstname'));
		}
		if(!count($records)) {
			return false;
		}
		$table = new html_table();
		$strstud = get_string('student','praxe');
		$strstatus = get_string('status','praxe');
		$strschool = get_string('school','praxe');
		$strsubject = get_string('subject','praxe');
		if(praxe_has_capability('viewrecordstoanylocation') || praxe_has_capability('manageallincourse')) {
			$viewteacher = true;
			$table->head  = array ('&nbsp;', $strstud, $strschool, $strsubject, get_string('teacher','praxe'), $strstatus, get_string('inspection','praxe'));
			$table->align = array ('left', 'left', 'left', 'center', 'center', 'center', 'center');
		}else {
			$viewteacher = false;
			$table->head  = array ('&nbsp;', $strstud, $strschool, $strsubject, $strstatus, get_string('inspection','praxe'));
			$table->align = array ('left', 'left', 'left', 'center', 'center', 'center');
		}

		foreach($records as $rec) {
			$row = array();
			$url = praxe_get_base_url(array('mode'=>$mode,'recordid'=>$rec->id));
			$row[] = "<a href=\"$url\" title=\"\">".get_string('detail','praxe')."</a>";

			$user = (object) array('firstname'=>$rec->firstname, 'lastname'=>$rec->lastname, 'id'=>$rec->userid);
			$row[] = praxe_get_user_fullname($user);
			$row[] = s($rec->schoolname);
			$row[] = s($rec->subject);
			if($viewteacher && $rec->teacherid) {
				$row[] = praxe_get_user_fullname((object)array('id'=>$rec->teacherid, 'firstname'=>$rec->teacher_firstname, 'lastname'=>$rec->teacher_lastname));
			}else {
				$row[] = get_string('unlisted','praxe');
			}

			if($rec->status == PRAXE_STATUS_ASSIGNED) {
				$row[] = self::confirm_location_form($rec->id);
			}else {
			    $row[] = praxe_get_status_info($rec->status);
			}

			if(isset($rec->inspections) && count($rec->inspections)) {
			    $inspText = array();
			    foreach($rec->inspections as $insp) {
			        $inspText[] = praxe_get_user_fullname((object)array('id'=>$insp->userid, 'firstname'=>$insp->firstname, 'lastname'=>$insp->lastname))
			                        . " (".userdate($insp->timestart,get_string('strftimedateshort')).")";
			    }
			    $row[] = implode('<br />',$inspText);
			}else {
			    $row[] = "---";
			}
			$table->data[] = $row;
		}
		return html_writer::table($table);
	}
	private function show_all_my_locations($bOnlyActual = 0, $year = 0) {
		global $USER, $CFG, $tab_modes, $DB, $OUTPUT;
		if(!$all = praxe_get_locations(null, null, null, $bOnlyActual, $year)) {
			return get_string('nolocationsavailable','praxe');
		}
		$table = new html_table();
		$strname = get_string('schoolname','praxe');
		$strsubject = get_string('subject','praxe');
		$strstudy = get_string('studyfield','praxe');
		$strisced = get_string('iscedlevel','praxe');
		$stryear = get_string('year','praxe');
		$strterm = get_string('term','praxe');
		$table->head = array($strname, $strsubject, $strisced, $strstudy, $stryear, $strterm, get_string('active','praxe'), get_string('edit'));
		$table->align = array ('left', 'left', 'left', 'left', 'center', 'center', 'center');
		$stredit = get_string('edit');
		foreach($all as $loc) {
			if($loc->teacherid != $USER->id) {
				continue;
			}
			$row = array(s($loc->name), s($loc->subject), praxe_get_isced_text($loc->isced), s($loc->studyfieldname)." (".s($loc->shortcut).")");
			$row[] = s($loc->year);
			$row[] = praxe_get_term_text($loc->term);
			$row[] = ($loc->active == 1) ? get_string('yes') : get_string('no');
			if(praxe_has_capability('editanylocation') || (praxe_has_capability('editownlocation') && $USER->id == $loc->teacherid)) {
				$row[] = $OUTPUT->action_icon(praxe_get_base_url(array("mode"=>$tab_modes['extteacher'][PRAXE_TAB_EXTTEACHER_EDITLOCATION],"locationid"=>$loc->id)),
                                                new pix_icon('t/edit',$stredit));
			}else{
				$row[] = get_string('already_used','praxe');
			}
			$table->data[] = $row;
		}
		if(count($table->data)) {
			return html_writer::table($table);
		}
		return get_string('nolocationsavailable','praxe');
	}
	/**
	 *
	 * @param object $rec - object of praxe record to be shown
	 * @return string
	 */
	public static function show_record_detail($rec) {
		global $mode, $USER, $CFG, $OUTPUT;
		/// left top table ///
		$tab1 = new html_table();
		$tab1->align = array('right', 'left');
		//$tab1->width = '300px';
		$tab1->attributes['class'] = "floatinfotable left twocolstable";

		$school = array($rec->name);
		if(strlen(trim($rec->city))) {
			$school[] = $rec->city;
		}
		if(strlen(trim($rec->street))) {
			$school[] = $rec->street;
		}
		if(strlen(trim($rec->phone))) {
			$school[] = $rec->phone;
		}
		if(strlen(trim($rec->email))) {
			$school[] = $rec->email;
		}
		$tab1->data[] = array(get_string('school','praxe').":", format_string(implode(', ', $school)));
		$tab1->data[] = array(get_string('subject','praxe').":", $rec->subject);
		if($rec->teacherid && $USER->id != $rec->teacherid) {
			$tab1->data[] = array(get_string('teacher','praxe').":", praxe_get_user_fullname((object)array('id'=>$rec->teacherid, 'firstname'=>$rec->teacher_firstname, 'lastname'=>$rec->teacher_lastname)));
		}
		/// right top table ///
		$tab2 = new html_table();
		$tab2->align = array('right', 'left');
		//$tab2->width = '350px';
		$tab2->attributes['class'] = "floatinfotable left twocolstable last";
		$tab2->data[] = array(get_string('student', 'praxe').":", praxe_get_user_fullname($rec->student));
		$tab2->data[] = array(get_string('status','praxe').":", praxe_get_status_info($rec->status));
	    if($rec->status == PRAXE_STATUS_ASSIGNED) {
			$tab2->data[] = array(get_string('action').':', self::confirm_location_form($rec->id));
			return html_writer::table($tab1) . html_writer::table($tab2) . '<div class="clearer"></div>';
		}

		$return = html_writer::table($tab1) . html_writer::table($tab2) . '<div class="clearer"></div>';
		$return .= "<h3>".get_string('schedule','praxe')."</h3>";
		$schedules = praxe_get_schedules($rec->id);
		if(!$schedules) {
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
		$tab3 = new html_table();
		if(count($cols)) {
			sort($cols);
		}
		$tab3->head = $cols;
		array_unshift($tab3->head, get_string('date'));
		for($i = 1; $i < count($tab3->head); $i++) {
			if(is_null($tab3->head[$i])) {
				$tab3->head[$i] = "---";
			}else {
				$tab3->head[$i] = s($tab3->head[$i]).".".get_string('lesson','praxe');
			}
		}
        $cellf = new html_table_cell();
		$cellf->attributes['class'] = "header first";
		$cellf->header = true;
		$paramsToAssing = array('post_form'=>'assigntoinspection', 'sesskey'=>sesskey(), 'submitbutton'=>'true', 'userid'=>$USER->id);
		$paramsToRemAssing = array('post_form'=>'removefrominspection', 'sesskey'=>sesskey(), 'submitbutton'=>'true');
		foreach($sched as $row) {
		    $datetd = userdate($row['date'],get_string('strftimeday','praxe'))."<br />".userdate($row['date'],get_string('strftimedateshort'));
			$cell = clone $cellf;
			$cell->text = $datetd;
			$cells = array($cell);
			foreach($cols as $k=>$c) {
				if(isset($row[$c])) {
					$item = userdate($row[$c]->timestart,get_string('strftimetime'))." - ".userdate($row[$c]->timeend,get_string('strftimetime'))
							."<br>".s($row[$c]->lessubject)
							."<br>".get_string('schoolroom','praxe').": ".s($row[$c]->schoolroom);
					$item = "<div>"
						    .$OUTPUT->action_link(praxe_get_base_url(array("mode"=>$mode,"recordid"=>$rec->id,"scheduleid"=>$row[$c]->id)),$item, null, array('title'=>get_string('detail','praxe')))
							."</div>";
					if(count($row[$c]->inspectors)) {
						foreach($row[$c]->inspectors as $insp) {
						    $item .= "<div class=\"inspector right\">"
							        .$OUTPUT->render(new pix_icon('icon_inspect',get_string('inspection','praxe'),'praxe'))
							        ."&nbsp;".praxe_get_user_fullname($insp);
						    /// remove assinging from item action ///
							if($USER->id == $insp->id || praxe_has_capability('manageallincourse')) {
							    $params = $paramsToRemAssing;
							    $params['scheduleid'] = $row[$c]->id;
							    $params['inspid'] = $insp->id;
							    $item .= $OUTPUT->action_icon(praxe_get_base_url($params),new pix_icon('t/delete',get_string('removeinspection','praxe')));
							}
							$item .= "</div>";
						}
					}else if(praxe_has_capability('assignselftoinspection')) {
						$params = $paramsToAssing;
						$params['scheduleid'] = $row[$c]->id;
						/// assign inspector button ///
						$item .= "<div class=\"inspector right\">"
						        .$OUTPUT->single_button(praxe_get_base_url($params),get_string('gotoinspection','praxe'),'post')
						        .'</div>';
					}
					$cells[] = new html_table_cell($item);
				}else {
					$cells[] = new html_table_cell('&nbsp;');
				}
			}
			$tab3->data[] = new html_table_row($cells);
		}
		return $return . html_writer::table($tab3);
	}
	public static function show_schedule_detail($schedule) {
		$tab2 = new html_table();
		$tab2->align = array('right', 'left');
		$tab2->cellpadding = '2px';
		$tab2->attributes['class'] = "twocolstable";
		$tab2->data[] = array(get_string('time').":", userdate($schedule->timestart, get_string('strftimedayshort'))."&nbsp;&nbsp;".userdate($schedule->timestart,get_string('strftimetime'))." - ".userdate($schedule->timeend,get_string('strftimetime')));
		$tab2->data[] = array(get_string('schoolroom','praxe').":", s($schedule->schoolroom));
		$tab2->data[] = array(get_string('yearclass','praxe').":", praxe_get_yearclass($schedule->yearclass));
		$tab2->data[] = array(get_string('subject','praxe').":", s($schedule->lessubject));
		$tab2->data[] = array(get_string('lesson_theme','praxe').":", format_text($schedule->lestheme));
		return "<h3>".get_string('lessondetail','praxe')."</h3>".html_writer::table($tab2);
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
		$table = new html_table();
		$strname = get_string('schoolname','praxe');
		$strtype = get_string('schooltype','praxe');
		$straddress = get_string('address','praxe');
		$strcontact = get_string('contact','praxe');
		$table->head = array($strname, $strtype, $straddress, $strcontact);
		$table->align = array ('left', 'left', 'left', 'left');
		foreach($schools as $sch) {
			$schname = "<a href=\"".praxe_get_base_url(array("mode"=>$tab_modes['extteacher'][PRAXE_TAB_EXTTEACHER_MYSCHOOLS],"schoolid"=>$sch->id))."\" title=\"".get_string('detail','praxe')."\">".s($sch->name)."</a>";
			$address = s($sch->street).', '.s($sch->zip).'  '.s($sch->city);

			//$contact->head = array('');
			$contact = array();
			if(!empty($sch->email)) {
				$contact[] = s($sch->email);
			}
			if(!empty($sch->phone)) {
				$contact[] = s($sch->phone);
			}
			if(!empty($sch->website)) {
				$contact[] = s($sch->website);
			}
			$contact = html_writer::tag('p', implode("<br>", $contact));
			$schooltype =  constant('PRAXE_SCHOOL_TYPE_'.(int)$sch->type.'_TEXT');
			$table->data[] = array($schname, $schooltype, $address, $contact);
		}
		return html_writer::table($table);
	}
	public static function confirm_location_form($recordid) {
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
