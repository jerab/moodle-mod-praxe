<?php
/**
 * This page prints a particular instance of praxe_record(s) for student role
 *
 * @author  Tomas Jerabek <t.jerab@gmail.com>
 * @version
 * @package mod/praxe
 */
/// extending class for classes "praxe_view_[role]" ///
require_once($CFG->dirroot . '/mod/praxe/c_praxe_view.php');
class praxe_view_student extends praxe_view {
	public $form = null;

	function praxe_view_student() {
		global $CFG, $tab, $tab_modes, $context, $DB, $sentForm;
		$viewaction = optional_param('viewaction',null, PARAM_ALPHA);
		$schoolid = optional_param('schoolid',0,PARAM_INT);
		if($viewaction == 'viewschool' && $schoolid) {
			require_once($CFG->dirroot . '/mod/praxe/view_headm.php');
			$this->content .= praxe_view_headm::show_school($schoolid);
			$this->content .= praxe_praxehome_buttons(false);
		}else {
			$status = praxe_record::getData('rec_status');
			switch($tab) {
				case PRAXE_TAB_STUDENT_HOME :
				    $after_tab = '';
					$strd = get_string('done','praxe');
					$strinpr = get_string('inprocess','praxe');
					$cell = new html_table_cell(get_string('todo','praxe'));
					$cell->attributes['class'] = 'cell center';
					$cell1 = clone $cell;
					$cell2 = clone $cell;
					if(is_null($status)) {
						$this->content .= '<div class="before_form">'.get_string('assigntolocation_text_forstudents', 'praxe').'</div>';
						$this->content .= self::make_assigntolocation_form();
						$this->content .= "<hr>";
					}else {
						$this->content .= "<div class=\"status_infobox\"><strong>".get_string('status','praxe').": </strong>".praxe_get_status_info($status, 'student')."</div>";
					    if($status == PRAXE_STATUS_REFUSED) {
							$this->content .= '<div class="before_form">'.get_string('assigntolocation_text_forstudents', 'praxe').'</div>';
					        $this->content .= self::make_assigntolocation_form();
						}else if($status == PRAXE_STATUS_ASSIGNED) {
						    $cell->text = $strinpr;
						    $cell->attributes['class'] .= ' orange';
						}else if($status == PRAXE_STATUS_SCHEDULE_DONE) {
							$cell->text = $strd;
							$cell->attributes['class'] .= ' green';
							$cell1->text = $strd;
							$cell1->attributes['class'] .= ' green';
						}else if($status == PRAXE_STATUS_CONFIRMED) {
							$cell->text = $strd;
							$cell->attributes['class'] .= ' green';
							$schlink = praxe_get_base_url(array("mode"=>$tab_modes['student'][PRAXE_TAB_STUDENT_SCHEDULE]));
							$after_tab .= "<div class=\"infobox center\"><div>".get_string('you_should_create_schedule','praxe').": <a href=\"$schlink\">".get_string('my_schedule','praxe')."</a></div></div>";
						}else if($status == PRAXE_STATUS_EVALUATED) {
							$cell->text = $strd;
							$cell->attributes['class'] .= ' green';
							$cell1->text = $strd;
							$cell1->attributes['class'] .= ' green';
							$cell2->text = $strd;
							$cell2->attributes['class'] .= ' green';
						}
					}
				    /// table of parts of student's practice ///
					$table = new html_table();
					$table->attributes['class'] = 'praxe generaltable boxaligncenter status';
					$table->head = array(get_string('choosing_location','praxe'),
									    get_string('schedule','praxe'), //'creating_schedule'
									    get_string('evaluated','praxe'));
									    //get_string('praxe_completed','praxe'));
					$table->data[] = new html_table_row(array($cell,$cell1,$cell2));
					$this->content .= html_writer::table($table);
					$this->content .= $after_tab;
					break;
				case PRAXE_TAB_STUDENT_MYSCHOOL :
					$this->content .= self::show_location(praxe_record::getData('rec_location'));
					break;
				case PRAXE_TAB_STUDENT_EDITLOC :
					$this->content .= self::change_location_form();
					break;
				case PRAXE_TAB_STUDENT_SCHEDULE :
					$editlinks = array("mode"=>$tab_modes['student'][PRAXE_TAB_STUDENT_ADDSCHEDULE], 'edit'=>'editschedule');
					$schedules = praxe_get_schedules(praxe_record::getData('rec_id'),array('timestart','lesnumber'));
					self::show_schedule($schedules, false, $editlinks);
					/// schedule confirm form for validation ///
					if($schedules) {
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
					break;
				case PRAXE_TAB_STUDENT_ADDSCHEDULE :
					if(is_object($sentForm)) {
						if(!is_null(optional_param('edit',null,PARAM_TEXT))) {
							$scheduleid = optional_param('scheduleid',0,PARAM_INT);
							$sentForm->set_form_to_edit((object)array('id' => $scheduleid));
						}
						$sentForm->display_content_before();
						$sentForm->display();
					}else {
						require_capability('mod/praxe:addstudentschedule', $context);
						require_once($CFG->dirroot . '/mod/praxe/c_makeschedule.php');
						$this->form = new praxe_makeschedule();
						if(!is_null(optional_param('edit',null,PARAM_TEXT))) {
							$scheduleid = optional_param('scheduleid',0,PARAM_INT);
							if($schedule = $DB->get_record('praxe_schedules', array('id' => $scheduleid, 'record' => praxe_record::getData('rec_id')))) {
								if(!$this->form->set_form_to_edit($schedule)) {
									$this->form->add_to_content(get_string('date_of_schedule_has_expired','praxe')."<br>".get_string('noeditableitem','praxe'), true);
								}
							}else {
								print_error('notallowedaction', 'praxe');
							}
						}
					}
					break;
				default:
					redirect(praxe_get_base_url());
					break;
			}
		}
	}
	public function make_assigntolocation_form($edit=false) {
		global $CFG, $USER, $OUTPUT;
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
    	$table = new html_table();
    	$table->head = array('',
    						get_string('school','praxe'),
    						get_string('subject','praxe'),
    						get_string('teacher','praxe')
    					);
        $table->colclasses = array('praxe_cell center', 'praxe_cell left', 'praxe_cell center', 'praxe_cell center');
		foreach($locations as $loc) {
    		$sch = $OUTPUT->action_link(praxe_get_base_url(array("viewaction"=>'viewschool',"schoolid"=>$loc->school)), s($loc->name), null, array('title'=>get_string('school_detail','praxe')));
    		$sch .= "<div class=\"praxe_detail\">".s($loc->street).', '.s($loc->city)."</div>";
    		$row = array('<input id="praxe_loc_'.$loc->id.'" type="radio" name="location" value="'.$loc->id.'" />', $sch);
    		$row[] = s($loc->subject);
			if(!is_null($loc->teacherid)) {
    			$teacher = (object) array('id' => $loc->teacherid, 'firstname' => s($loc->teacher_name), 'lastname' => s($loc->teacher_lastname));
    			$row[] = praxe_get_user_fullname($teacher);
    		}else {
    			$row[] = '';
    		}
    		$table->data[] = $row;
    	}
		$form .= html_writer::table($table);
		$form .= '<div class="fitem center" style="margin: 10px 0;">'
					.'<input type="submit" id="id_submitbutton" value="'.get_string('submit').'" name="submitbutton" />';
		if($edit) {
			$form .=' <input type="submit" id="id_cancel" onclick="skipClientValidation = true; return true;" value="'.get_string('cancel').'" name="cancel" />';
		}
		$form .= '</div>';
		$form .= '</form>';
		return "<div>$form</div>";
	}
	public function change_location_form() {
		return self::make_assigntolocation_form(true);
		//$this->form->_form->addElement('hidden','edit','changelocation');
	}
	public function show_location($id) {
		global $OUTPUT;
		if($data = praxe_get_location($id)) {
			//print_object($data);
			$table = new html_table();
    		$table->head = array(get_string('school','praxe'),
    							get_string('subject','praxe'),
    							get_string('teacher','praxe')
    						);
			$table->align = array('left','center','center');
    		$sch = new html_table();
			$sch->data[] = array(get_string('address','praxe').":", s($data->street).', '.s($data->zip)."&nbsp;&nbsp;".s($data->city));
    		if($data->headmaster > 0) {
    			$headmaster = (object)array('id'=>$data->headmaster, 'firstname'=>$data->head_name, 'lastname'=>$data->head_lastname);
    			$sch->data[] = array(get_string('headmaster','praxe').":", praxe_get_user_fullname($headmaster));
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
    		$sch->data[] = array(get_string('contact','praxe').":",implode("<br />",$contact));
			$schlink = $OUTPUT->action_link(praxe_get_base_url(array("viewaction"=>'viewschool',"schoolid"=>$data->school)),s($data->name),null,array('title'=>get_string('school_detail','praxe')));
			$row = array($schlink . html_writer::table($sch), s($data->subject));
			if($data->teacherid > 0) {
    			$teacher = (object) array('id' => $data->teacherid, 'firstname' => s($data->teacher_name), 'lastname' => s($data->teacher_lastname));
    			$row[] = praxe_get_user_fullname($teacher);
    		}else {
    			$row[] = get_string('unlisted','praxe');
    		}
    		$table->data[] = $row;
			return html_writer::table($table);
		}
		return '';
	}

	public function show_schedule($schedules, $boolReturn = true, $editlinks = array()) {
		global $CFG, $OUTPUT;
		if(!$schedules) {
			$ret = get_string('no_schedule_items','praxe');
			if($boolReturn) {
				return $ret;
			}else {
				$this->content .= $ret;
				return true;
			}
		}
		$table = new html_table();
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
		$editable = praxe_has_capability('editstudentschedule') || praxe_has_capability('manageallincourse');
		$params = $editlinks;
		$delparams = array('praxeaction'=>'deleteschedule');
		foreach($schedules as $item) {
			if(is_null($item->lesnumber)) {
				$item->lesnumber = "---";
			}else {
				$item->lesnumber .= ".";
			}
			$row = array(	userdate((int)$item->timestart, get_string('strftimedateshort')),
							$item->lesnumber,
							//date('G:i',(int)$item->timestart) .' - '. date('G:i',(int)$item->timeend),
							userdate((int)$item->timestart, "%H:%M") . ' - ' . userdate((int)$item->timeend, "%H:%M"),
							praxe_get_yearclass($item->yearclass),
							s($item->schoolroom),
							s($item->lessubject),
							format_text($item->lestheme)
						);
			if($editable && ($item->timestart-PRAXE_TIME_TO_EDIT_SCHEDULE) > time()) {
				$params['scheduleid'] = $item->id;
				$delparams['scheduleid'] = $item->id;
				$row[] = $OUTPUT->action_icon(praxe_get_base_url($params), new pix_icon('t/edit',get_string('edit')))
				         .$OUTPUT->action_icon(praxe_get_base_url($delparams), new pix_icon('t/delete',get_string('delete')));
			}else {
				$row[] = "---";
			}
			if(count($item->inspectors)) {
				$ins = "";
			    foreach($item->inspectors as $insp) {
					$ins .= "<div class=\"inspector right\">".$OUTPUT->render(new pix_icon('icon_inspect',get_string('inspection','praxe'),'praxe'))."&nbsp;".praxe_get_user_fullname($insp)."</div>";
				}
				$row[] = $ins;
			}else {
			    $row[] = "&nbsp;";
			}
			$table->data[] = $row;
		}
		if($boolReturn) {
			return html_writer::table($table);
		}else {
			$this->content .= html_writer::table($table);
			return true;
		}
	}
}
?>
