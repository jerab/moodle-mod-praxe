<?php
$cancel = optional_param('cancelbutton',null,PARAM_ALPHA);
$confirm = optional_param('confirmsubmitbutton',null,PARAM_ALPHA);
if(!is_null($cancel)) {
    if($praxeaction == 'deleteschedule') {
	    redirect(praxe_get_base_url(array('mode'=>'schedule')));
    }
    redirect(praxe_get_base_url());
}
$echo = '<div class="center">';
switch($praxeaction) {
	case 'deleteschedule':
		$scheduleid = optional_param('scheduleid',null,PARAM_INT);
		$schedule = $DB->get_record('praxe_schedules', array('id' => $scheduleid));
		if(!$schedule) {
			print_error('notallowedaction', 'praxe');
		}
		$editable = (praxe_has_capability('editstudentschedule') && $DB->get_record('praxe_records', array('id' => $schedule->record, 'student' => $USER->id)))
					|| praxe_has_capability('manageallincourse');
		if(!$editable) {
			print_error('notallowedaction', 'praxe');
		}
		if(is_null($confirm)) {
			$echo .= '<div class="before_form">'.get_string('realy_delete_schedule','praxe').'</div>';
			$table = new html_table();
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
			$table->data[] = array(	userdate((int)$schedule->timestart, get_string('strftimedateshort')),
									(int)$schedule->lesnumber.".",
									date('G:i',(int)$schedule->timestart)." - ".date('G:i',(int)$schedule->timeend),
									$yc,
									s($schedule->schoolroom),
									s($schedule->lessubject)
								);
			$echo .= html_writer::table($table);
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
			$DB->update_record('praxe_schedules',$post);
			redirect(praxe_get_base_url(array('mode'=>'schedule')));
		}
		break;
	case 'confirmschedule':
		require_sesskey();
		$teachermail = optional_param('mailtoextteacher', 0, PARAM_INT);
		$praxe = praxe_record::getData();
		$schedules = $DB->get_records('praxe_schedules', array('record' => $praxe->rec_id));
		if(empty($schedules)) {
			print_error('notallowedaction', 'praxe');
		}
		$post = (object) array('id'=>$praxe->rec_id,'status'=>PRAXE_STATUS_SCHEDULE_DONE);
		if($DB->update_record('praxe_records',$post) && $teachermail == 1) {
			$emfrom = get_complete_user_data('id',$praxe->rec_student);
			require_once($CFG->dirroot . '/mod/praxe/mailing.php');
			$mail = new praxe_mailing();
			$fak = new stdClass();
			$fak->name = fullname($emfrom);
			$fak->school = s($praxe->location->name);
			$mail->setSubject(get_string('confirmschedule_mailsubject','praxe'));
			$emtext = get_string('confirmschedule_mail','praxe',$fak);
			$mail->setPlain($emtext);
			$mail->setHtml($emtext);
			$emuser = get_complete_user_data('id',$praxe->location->teacherid);
			$mail->mailToUser($emuser, $emfrom);
		};
		redirect(praxe_get_base_url(array('mode'=>'schedule')));
		break;
}
$echo .= "</div>";
echo $echo;
