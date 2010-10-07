<?php
$cancel = optional_param('cancelbutton',null,PARAM_ALPHA);
$confirm = optional_param('confirmsubmitbutton',null,PARAM_ALPHA);

if(!is_null($cancel)) {
	redirect(praxe_get_base_url());	
}
$echo = '<div class="center">';
switch($praxeaction) {
	case 'deleteschedule':
		
		$scheduleid = optional_param('scheduleid',null,PARAM_INT);		
		$schedule = get_record('praxe_schedules','id',$scheduleid);		
		
		if(!$schedule) {
			error(get_string('notallowedaction','praxe'));
		}
		$editable = (praxe_has_capability('editstudentschedule') && get_record('praxe_records','id',$schedule->record,'student',$USER->id))
					|| praxe_has_capability('manageallincourse');
		if(!$editable) {
			error(get_string('notallowedaction','praxe'));
		}
		if(is_null($confirm)) {
			$echo .= '<div class="before_form">'.get_string('realy_delete_schedule','praxe').'</div>';
			$table = new stdClass();			 
			$yc = $schedule->yearclass;
			if($schedule->yearclass >= 6 && $schedule->yearclass <= 9) {
				$yc = PRAXE_ISCED_2_TEXT." $yc.";
			}else if($schedule->yearclass >= 10) {
				$yc = PRAXE_ISCED_3_TEXT.' '.($yc-9).".";
			}
			$table->head = array(	get_string('date'),
								get_string('lesson_number','praxe'),
								get_string('time'),
								get_string('yearclass','praxe'),
								get_string('schoolroom','praxe'),
								get_string('subject','praxe')
							);
			$table->data = array();
			$table->data[] = array(	userdate((int)$schedule->timestart, get_string('strftimedateshort')),
									(int)$schedule->lesnumber.".",
									date('G:i',(int)$schedule->timestart)." - ".date('G:i',(int)$schedule->timeend),
									$yc,
									s($schedule->schoolroom),
									s($schedule->lessubject)							
								);
			$echo .= print_table($table,true);
			$f = '<form method="post" class="confirmform"><input type="hidden" name="scheduleid" value="'.$schedule->id.'">';
			$f .= '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
			$f .= '<div class="fitem center" style="margin: 10px 0;">';
			$f .= '<input type="submit" name="confirmsubmitbutton" value="'.get_string('confirm').'" />';
			$f .= ' <input type="submit" name="cancelbutton" value="'.get_string('cancel').'" />';
			$f .= '</div>';
			$f .= '</form>';
			$echo .= $f;			
		}else {
			require_sesskey();
			$post = (object) array('id'=>$scheduleid, 'deleted'=>1);
			update_record('praxe_schedules',$post);
			redirect(praxe_get_base_url());
		}
		
		break;
	case 'confirmschedule':
		require_sesskey();
		$schedules = get_records('praxe_schedules','record',praxe_record::getData('rec_id'));		
		if(empty($schedules)) {
			error(get_string('notallowedaction','praxe'));
		}
			
		$post = (object) array('id'=>praxe_record::getData('rec_id'),'status'=>PRAXE_STATUS_SCHEDULE_DONE);
		update_record('praxe_records',$post);
		redirect(praxe_get_base_url());			
		
		break;
	/*case 'viewschool':
		$schoolid = optional_param('schoolid',null,PARAM_INT);
		require_once($CFG->dirroot . '/mod/praxe/view_headm.php');
		echo praxe_view_headm::show_school($schoolid);		
		break;
	case 'editschool':
		$schoolid = optional_param('schoolid',null,PARAM_INT);		
		/// no id or school doesn't exist ///
		if(empty($schoolid) || !($school = praxe_get_school($schoolid))) {
			error(get_string('notallowedaction','praxe'));
		}
		/// not allowed to edit any or this school ///
		if(!praxe_has_capability('editanyschool') && !(praxe_has_capability('editownschool') && $school->headmaster == $USER->id)) {				
			error(get_string('notallowedaction','praxe'));
		}
		
		require_once($CFG->dirroot . '/mod/praxe/c_addschool.php');
		$form = new praxe_addschool();
		//print_object($school);
		$form->set_form_to_edit($school);
		$form->_form->display();
		break;
	*/			
}
$echo .= "</div>";
echo $echo;// . praxe_praxehome_buttons();

//echo $pacon;