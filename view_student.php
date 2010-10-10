<?php

/**
 * This page prints a particular instance of praxe_record for students
 *
 * @author  jerab 
 * @package mod/praxe
 */


/// extending class for classes "praxe_view_[role]" ///
require_once($CFG->dirroot . '/mod/praxe/c_praxe_view.php');

class praxe_view_student extends praxe_view {
	
	public $form = null;
	 
	function praxe_view_student() {
		global $CFG, $tab, $cm, $tab_modes, $context;		
		
		$viewaction = optional_param('viewaction',null);
		$schoolid = optional_param('schoolid',null,PARAM_INT);
		if($viewaction == 'viewschool' && !is_null($schoolid)) {
			require_once($CFG->dirroot . '/mod/praxe/view_headm.php');
			$this->content .= praxe_view_headm::show_school($schoolid);
			$this->content .= praxe_praxehome_buttons(false);			
		}else {
			$status = praxe_record::getData('rec_status');
			switch($tab) {
				case PRAXE_TAB_STUDENT_HOME :	
					
					$after_tab = '';					
					$strtd = get_string('todo','praxe');
					$strd = get_string('done','praxe');
					$row = array(0=>$strtd, 1=>$strtd, 2=>$strtd, 3=>$strtd);
					
					if(is_null($status)) {
						$this->content .= '<div class="before_form">'.get_string('assigntolocation_text_forstudents', 'praxe').'</div>'; 
						$this->content .= self::make_assigntolocation_form();
						$this->content .= "<hr>";
					}else if($status == PRAXE_STATUS_REFUSED) {										
						self::make_assigntolocation_form();	
					
					}else if($status == PRAXE_STATUS_ASSIGNED) {								
					
					}else if($status == PRAXE_STATUS_SCHEDULE_DONE) {					
						$row[0] = $strd;
						$row[1] = $strd;
					}else if($status == PRAXE_STATUS_CONFIRMED) {
						$row[0] = $strd;
						
						$schlink = praxe_get_base_url()."&amp;mode=".$tab_modes['student'][PRAXE_TAB_STUDENT_SCHEDULE]; 
						$after_tab .= "<div>".get_string('you_should_create_schedule','praxe').": <a href=\"$schlink\">".get_string('my_schedule','praxe')."</a></div>";				
					}else if($status == PRAXE_STATUS_EVALUATED) {
						$row[0] = $strd;
						$row[1] = $strd;
						$row[2] = $strd;
					}
					
				/// table of parts of student's practice ///
					$table = '<table cellspacing="1" cellpadding="5" width="80%" class="praxe generaltable boxaligncenter">';
					$table .= '<tbody><tr>';					
					///create head of table
					$head = array(	get_string('choosing_location','praxe'),
									get_string('schedule','praxe'), //'creating_schedule'
									get_string('evaluated','praxe'),
									get_string('praxe_completed','praxe')
								);
					foreach($head as $th) {
						$table .= '<th scope="col" class="header">'.$th.'</th>';
					}
					$table .= '</tr><tr class="lastrow">';
					/// create body of table
					foreach($row as $td) {					
						$class = 'red';
						if($td == $strd) {
							$class = 'green';
						}
						$table .= '<td class="cell center '.$class.'">'.$td.'</td>';
					}
					$table .= '</tr></tbody></table>';
				/// end of table ///
					
					if(!is_null($status)) {
						$this->content = "<div class=\"status_infobox\"><strong>".get_string('status','praxe').": </strong>".praxe_get_status_info($status)."</div>".$this->content;
					}
					$this->content .= $table;
					if(strlen($after_tab)) {
						$this->content .= "<div class=\"infobox center\">$after_tab</div>";
					}
					
					break;
				case PRAXE_TAB_STUDENT_MYSCHOOL :				
					$this->content .= self::show_location(praxe_record::getData('rec_location'));				
					
					break;
				case PRAXE_TAB_STUDENT_EDITLOC :
					$this->content .= self::change_location_form();
									
					break;
				case PRAXE_TAB_STUDENT_SCHEDULE :					
					$editlinks = array("mode=".$tab_modes['student'][PRAXE_TAB_STUDENT_ADDSCHEDULE], 'edit=editschedule');
					$schedules = praxe_get_schedules(praxe_record::getData('rec_id'),array('timestart','lesnumber'));
					self::show_schedule($schedules, false, $editlinks);
					/// schedule confirm form for validation /// 
					if(is_array($schedules)) {
						$f = '<form method="post" class="form confirmschedule center">';
						$f .= '<input type="hidden" name="sesskey" value="'.sesskey().'" />'
							.'<input type="hidden" name="praxeaction" value="confirmschedule" />';
						$f .= '<div>';
						$f .= '<strong>'.get_string('confirmschedule_sendnotice','praxe').'</strong><br />';
						$f .= get_string('sendinfotoextteacher','praxe').": ";
						$f .= '<input type="checkbox" name="mailtoextteacher" value="1" checked="checked" />';
						$f .= '</div><div class="center">';
						$f .= '<input type="submit" name="confirmsubmitbutton" value="'.get_string('confirm').'" />';						
						$f .= '</div>';
						$f .= '</form>';
						$this->content .= $f;
					}					
					//$this->content .= "<a href=\"".praxe_get_base_url()."&amp;mode=".$tab_modes['student'][PRAXE_TAB_STUDENT_ADDSCHEDULE]."\">".get_string('addtoschedule', 'praxe')."</a>";
					//require_once($CFG->dirroot . '/mod/praxe/c_makeschedule.php');
					//$this->form = new praxe_makeschedule();
					break;
				case PRAXE_TAB_STUDENT_ADDSCHEDULE :
					require_capability('mod/praxe:addstudentschedule', $context);
					require_once($CFG->dirroot . '/mod/praxe/c_makeschedule.php');
					$this->form = new praxe_makeschedule();
					$edit = optional_param('edit',null);										
					
					if($edit) {
						$scheduleid = optional_param('scheduleid',0,PARAM_INT);
						if($schedule = get_record('praxe_schedules','id',$scheduleid,'record',praxe_record::getData('rec_id'))) {							
							if(!$this->form->set_form_to_edit($schedule)) {
								$this->form->add_to_content(get_string('date_of_schedule_has_expired','praxe')."<br>".get_string('noeditableitem','praxe'), true);
							}
						}else {
							error(get_string('notallowedaction','praxe'));
						}
					}					
					
					break;							
				default:
					redirect($CFG->wwwroot.'/mod/praxe/view.php?id='.$cm->id);
					break;
			}
		}		
	}
	
	public function make_assigntolocation_form($edit=false) {
		global $CFG, $USER;
		//require_once($CFG->dirroot . '/mod/praxe/c_assigntolocation.php');
		//$this->form = new praxe_assigntolocation();
		$locations = praxe_get_available_locations($USER->id, praxe_record::getData('isced'), praxe_record::getData('studyfield'));
		if(!is_array($locations) ||!count($locations)) {
			 return get_string('nolocationsavailable', 'praxe');
		}
		$form = '<form class="mform" action="'.praxe_get_base_url().'" method="post">';
		$form .= '<input type="hidden" name="post_form" value="assigntolocation" />';
		$form .= '<input type="hidden" name="sesskey" value="'.sesskey().'" />';		
		if($edit) {
			$form .= '<input type="hidden" name="edit" value="changelocation" />';
		}
				
    	$table = new stdClass();    		
    	$table->head = array('',
    						get_string('school','praxe'),
    						get_string('subject','praxe'),
    						get_string('teacher','praxe')
    					);
		$table->align = array('center','left','center','center');    							
		foreach($locations as $loc) {  			   		    			
    		$row = array('<input id="praxe_loc_'.$loc->id.'" type="radio" name="location" value="'.$loc->id.'" />');    			
    		$sch = "<a href=\"".praxe_get_base_url()."&amp;viewaction=viewschool&amp;schoolid=$loc->school\" title=\"".get_string('school_detail','praxe')."\">".s($loc->name)."</a>";		
    		$sch .= "<div class=\"praxe_detail\">".s($loc->street).', '.s($loc->city)."</div>";
    		$row[] = $sch;
    		$row[] = s($loc->subject);
			if(!is_null($loc->teacherid)) {
    			$teacher = (object) array('id' => $loc->teacherid, 'firstname' => s($loc->teacher_name), 'lastname' => s($loc->teacher_lastname));
    			$row[] = praxe_get_user_fullname($teacher);
    		}else {
    			$row[] = '';
    		}
    		$table->data[] = $row; 
    		//$row .= '<label for="praxe_loc_'.$loc->id.'">'.$text.'</label>';
    		//$form .= "<div class=\"tr\">$row</div>";    							
    	}						
		$form .= print_table($table,true);
		$form .= '<div class="fitem center" style="margin: 10px 0;">'
					.'<input type="submit" id="id_submitbutton" value="'.get_string('submit').'" name="submitbutton" />';
		if($edit) {
			$form .=' <input type="submit" id="id_cancel" onclick="skipClientValidation = true; return true;" value="'.get_string('cancel').'" name="cancel" />';
		}
		$form .= '</div>';
		$form .= '</form>';
		return "<div>$form</div>";
		//$this->content .= "<div>$form</div>";	
	}
	
	public function change_location_form() {
		return self::make_assigntolocation_form(true);
		//$this->form->_form->addElement('hidden','edit','changelocation');
	}
		
	public function show_location($id) {
		global $CFG;			
		if($data = praxe_get_location($id)) {
			//print_object($data);
			$table = new stdClass();    		
    		$table->head = array(get_string('school','praxe'),
    							get_string('subject','praxe'),
    							get_string('teacher','praxe')
    						);
			$table->align = array('left','center','center');
			$sch = "<a href=\"".praxe_get_base_url()."&amp;viewaction=viewschool&amp;schoolid=$data->school\" title=\"".get_string('school_detail','praxe')."\">".s($data->name)."</a>";		
    		$sch .= "<table cellpadding=\"5\"><tbody>";
    		$sch .= "<tr><td style=\"text-align: right; vertical-align: top;\">".get_string('address','praxe').":</td><td>".s($data->street).', '.s($data->zip)."&nbsp;&nbsp;".s($data->city)."</td></tr>";
    		if(!is_null($data->headmaster)) {
    			$headmaster = (object)array('id'=>$data->headmaster, 'firstname'=>$data->head_name, 'lastname'=>$data->head_lastname);
    			$sch .= "<tr><td style=\"text-align: right; vertical-align: top;\">".get_string('headmaster','praxe').":</td><td>".praxe_get_user_fullname($headmaster)."</td></tr>";
    		}
    		$contact = array();
    		if(is_string($data->phone) && strlen($data->phone)) {
    			$contact[] = s($data->phone);
    		}
			if(is_string($data->email) && strlen($data->email)) {
    			$contact[] = s($data->email);
    		}
			if(is_string($data->website) && strlen($data->website)) {
    			$contact[] = format_string($data->website);
    		}
    		$sch .= "<tr><td style=\"text-align: right; vertical-align: top;\">".get_string('contact','praxe').":</td><td>".implode("<br>",$contact)."</td></tr>";
    		$sch .= "</tbody></table>";
			$row[] = $sch;
    		$row[] = s($data->subject);
			if(!is_null($data->teacherid)) {
    			$teacher = (object) array('id' => $data->teacherid, 'firstname' => s($data->teacher_name), 'lastname' => s($data->teacher_lastname));
    			$row[] = praxe_get_user_fullname($teacher);
    		}else {
    			$row[] = get_string('unlisted','praxe');
    		}
    		$table->data[] = $row;	
			
			return print_table($table,true);
		}		
		return '';
	}

	public function show_schedule($schedules, $boolReturn = true, $editlinks = array()) {
		global $CFG;
		
		if(!is_array($schedules)) {
			$ret = get_string('no_schedule_items','praxe');
			if($boolReturn) {
				return $ret;
			}else {
				$this->content .= $ret;
				return true;
			}
		}
			
		$table = new stdClass();
		$table->head = array(	get_string('date'),
								get_string('lesson_number','praxe'),
								get_string('time'),
								get_string('yearclass','praxe'),
								get_string('schoolroom','praxe'),
								get_string('subject','praxe'),
								get_string('lesson_theme','praxe'),
								get_string('edit'),
								get_string('inspection','praxe')								
							);
		$table->align = array('left', 'center', 'center', 'center', 'center', 'left', 'left', 'center', 'center');
		$table->data = array();
		$editable = praxe_has_capability('editstudentschedule') || praxe_has_capability('manageallincourse');					
		foreach($schedules as $item) {			
			$row = array(	userdate((int)$item->timestart, get_string('strftimedateshort')),
							(int)$item->lesnumber.".",
							date('G:i',(int)$item->timestart) .' - '. date('G:i',(int)$item->timeend),
							praxe_get_yearclass($item->yearclass),
							s($item->schoolroom),
							s($item->lessubject),
							format_text($item->lestheme)
						);
			if($editable && ($item->timestart-PRAXE_TIME_TO_EDIT_SCHEDULE) > mktime()) {				
				$params = $editlinks;
				$params[] = "scheduleid=$item->id";
				$delparams = array('praxeaction=deleteschedule');
				$delparams[] = "scheduleid=$item->id";						
				$row[] = "<a title=\"".get_string('edit')."\" href=\"".praxe_get_base_url($params)."\">"
							."<img src=\"$CFG->pixpath/t/edit.gif\" class=\"iconsmall\" alt=\"".get_string('edit')."\" /></a>"
						." <a title=\"".get_string('delete')."\" href=\"".praxe_get_base_url($delparams)."\">"
							."<img src=\"$CFG->pixpath/t/delete.gif\" class=\"iconsmall\" alt=\"".get_string('delete')."\" /></a> "; 
			}else {
				$row[] = "---";
			}
			
			$ins = "";
			if(count($item->inspectors)) {				
				foreach($item->inspectors as $insp) {
					$ins .= "<div class=\"inspector right\"><img src=\"{$CFG->wwwroot}/mod/praxe/img/icon_inspect.gif\" alt=\"".get_string('inspection','praxe').":\" title=\"".get_string('inspection','praxe')."\" />&nbsp;".praxe_get_user_fullname($insp)."</div>";
				}
			}
			$row[] = $ins; 
			
			$table->data[] = $row;
		}
		$ret = print_table($table, true);
		if($boolReturn) {
			return $ret;
		}else {
			$this->content .= $ret;
			return true;
		}		
	}
}

?>
