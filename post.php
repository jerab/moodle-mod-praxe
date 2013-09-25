<?php

//  Edit and save a new post data //

	require_login($course, true, $cm);

    $praxe   = optional_param('praxe', praxe_record::getData('id'), PARAM_INT);
    $edit    = optional_param('edit', null, PARAM_ALPHA);
    $cancel  = optional_param('cancel', null, PARAM_TEXT);
    $submit  = optional_param('submitbutton', null, PARAM_TEXT);

    /// praxe instance doesn't exist ///
    if(empty($praxe)){
    	print_error('notallowedaction', 'praxe');
    }
    /*
    //these page_params will be passed as hidden variables later in the form.
    $page_params = array('praxe'=>$praxe, 'edit'=>$edit);
    */

    $post = new stdClass();
    //$post->praxe = $praxe;
    $todisplay = '';
	if(!is_null($submit)) {
	    require_sesskey();
		switch($post_form) :
			case ('assignteachers'): /// assigning external teacher to school
				$post->teacher_school = required_param('teacher_school', PARAM_INT);
				$post->ext_teacher = required_param('ext_teacher', PARAM_INT);

				/// no access ///
				if(!(praxe_has_capability('assignteachertoanyschool')
                    || (praxe_has_capability('assignteachertoownschool')
                        && $DB->get_record('praxe_schools', array('id' => $post->teacher_school, 'headmaster' => $USER->id))))) {
				            print_error('notallowedaction', 'praxe');
				}

				if(!praxe_is_user_in_cohort($post->ext_teacher, PRAXE_COHORT_EXTTEACHERS)) {
					print_error('notallowedaction', 'praxe');
				}

				$id = false;
				$id = $DB->insert_record('praxe_school_teachers', $post);
				if(is_numeric($id)) {
					redirect(praxe_get_base_url(array('mode'=>'teachers','schoolid'=>$post->teacher_school)), get_string('user_assigned_to_school','praxe'));
				}

				break;
            case ('assigntolocation'): /// assigning student to location
				if(praxe_has_capability('assignstudenttolocation')) {
					$post->student = optional_param('student', 0, PARAM_INT);
					$bInformStudent = true;
				}else if(praxe_has_capability('editownrecord')) {
					$post->student = $USER->id;
				}else if(praxe_has_capability('editanyrecord')) {
					$post->student = optional_param('student', 0, PARAM_INT);
				}

				/// check location data ///
				if(is_null($post->location = optional_param('location', null, PARAM_INT)) ) {
					print_error('notallowedaction', 'praxe', praxe_get_base_url());
				}
				if(!$location = praxe_get_location($post->location)) {
					print_error('notallowedaction', 'praxe', praxe_get_base_url());
				}

				/// no access / no id of student to assign ///
				if(!$post->student) {
					if($bInformStudent) {
						print_error('notallowedaction', 'praxe', praxe_get_base_url(array('mode'=>'locations','assignuser'=>1,'edit'=>true,'locationid'=>$post->location), 'assigntolocation'));
					}else {
						print_error('notallowedaction', 'praxe', praxe_get_base_url());
					}
				}

				$post->praxe = $praxe;

				/// this location is already used by other student ///
				if($rec = $DB->get_record('praxe_records', array('location' => $location->id, 'praxe' => praxe_record::getData('id')))) {
					if($rec->status != PRAXE_STATUS_REFUSED || $rec->student == $post->student) {
						redirect(praxe_get_base_url(), get_string('location_no_available','praxe'));
					}
				}

				$post->timecreated = time();
				$post->timemodified = time();
				//$DB->delete_records('praxe_records', array('praxe' => $praxe, 'student' => $post->student));

				$id = false;
				$id = $DB->insert_record('praxe_records', $post);
				if($id) {

					/// sending mail to external teacher ///
					if(optional_param('sendemailtoextteacher',0,PARAM_INT) == 1 && !is_null($location->teacherid)) {
					    $emuser = get_complete_user_data('id',$location->teacherid);
						$emfrom = get_complete_user_data('id',$post->student);
						require_once($CFG->dirroot . '/mod/praxe/mailing.php');
						$mail = new praxe_mailing();
						$stud = new stdClass();
						$stud->name = fullname($emfrom);
						$stud->date = userdate(praxe_record::getData('datestart'), get_string('strftimedateshort'))." - ".userdate(praxe_record::getData('dateend'), get_string('strftimedateshort'));
						$stud->subject = s($location->subject);
						$stud->school = s($location->name);
						$stud->studyfield = s(praxe_record::getData('studyfield_name'));
						$mail->setSubject(get_string('studenttopraxe','praxe'));
						$mail->addLinkToFoot(praxe_get_base_url(), get_string('confirmorrefusestudent','praxe'));
						$emtext = get_string('assigntolocation_mail','praxe',$stud);
						$mail->setPlain($emtext);
						$mail->setHtml($emtext);
						if(!$mail->mailToUser($emuser, $emfrom)) {
							print_error('mailnotsenttoexternalteacher', 'praxe', praxe_get_base_url(array('schoolid'=>0, 'mode'=>'locations')));
						}
					}
					/// sending mail to student (from actual user) ///
					if(isset($bInformStudent) && optional_param('sendemailtostudent',0,PARAM_INT) == 1) {
						global $USER;
						$emuser = get_complete_user_data('id',$post->student);
						$emfrom = get_complete_user_data('id',$USER->id);
						require_once($CFG->dirroot . '/mod/praxe/mailing.php');
						$mail = new praxe_mailing();
						$stud = new stdClass();
						$stud->name = fullname($emuser);
						$stud->date = userdate(praxe_record::getData('datestart'), get_string('strftimedateshort'))." - ".userdate(praxe_record::getData('dateend'), get_string('strftimedateshort'));
						$stud->subject = s($location->subject);
						$stud->school = s($location->name);
						$stud->studyfield = s(praxe_record::getData('studyfield_name'));
						$mail->setSubject(get_string('studenttopraxe','praxe'));
						$mail->addLinkToFoot(praxe_get_base_url(), get_string('praxe','praxe'));
						$emtext = get_string('assigntolocation_mail_student','praxe',$stud);
						$mail->setPlain($emtext);
						$mail->setHtml($emtext);
						if(!$mail->mailToUser($emuser, $emfrom)) {
							print_error('mailnotsenttostudent', 'praxe', praxe_get_base_url(array('schoolid'=>0, 'mode'=>'locations')));
						}
					}
					redirect(praxe_get_base_url(array('schoolid'=>0, 'mode'=>'locations')), get_string('assigned_to_location','praxe'));
				}

				break;
			case ('addschool'): /// add new school form
				/// no access ///
				$post->headmaster = optional_param('headmaster',null,PARAM_INT);
				if(!praxe_has_capability('manageallincourse') && !praxe_has_capability('editanyschool')) {
					if(!praxe_has_capability('editownschool') && (is_null($post->headmaster) || $post->headmaster != $USER->id)) {
						print_error('notallowedactiona', 'praxe');
					}
				}

				if(is_null($post->headmaster)) {
					$post->headmaster = null;
				}

				if($post->headmaster != $USER->id && !has_capability('mod/praxe:manageallincourse',$context)) {
					print_error("You don't have rights for this action!");
				}

				$post->name = optional_param('name', '', PARAM_TEXT);
				$post->type = optional_param('type', PRAXE_SCHOOL_TYPE_5, PARAM_INT);
				$post->street = optional_param('street', '', PARAM_TEXT);
				$post->city = optional_param('city', '', PARAM_TEXT);
				$post->zip = optional_param('zip', null, PARAM_NUMBER);
				if(is_null($post->zip)) {
					$post->zip = null;
				}
				$post->email = optional_param('email', '', PARAM_EMAIL);
				$post->phone = optional_param('phone', '', PARAM_TEXT);
				$post->website = optional_param('website','', PARAM_URL);
				$post->usermodified = $USER->id;
				//$post->timemodified = time();

				/// insert record ///
				if(is_null($edit)) {
					$post->timecreated = time();
					if($id = $DB->insert_record('praxe_schools', $post)){
					    redirect(praxe_get_base_url(array('mode'=>'schools', 'schoolid'=>$id)), get_string('school_added','praxe'));
					}
				}else{
					$post->id = required_param('schoolid',PARAM_INT);
					$post->timemodified = time();
					if($DB->update_record('praxe_schools',$post)) {
						redirect(praxe_get_base_url(array('mode'=>'schools', 'schoolid'=>$post->id)), get_string('school_updated','praxe'));
					}
				}

				break;
			case ('addlocation'): /// add new location
			    $bManage = praxe_has_capability('manageallincourse');
			    if(!is_null($edit)) {
			        $post->id = required_param('locationid',PARAM_INT);
			        $used = $DB->record_exists_select('praxe_records', 'location = '.$post->id.' AND status <> '.PRAXE_STATUS_REFUSED);
			    }else {
			        $used = false;
			    }
			    /// it is not supposed to be teacher or admin role (but extteacher or headmaster) and not selected by student ///
			    if($bManage && !$used) {
			        $post->school = required_param('school',PARAM_INT);
				    $post->teacher = required_param('teacher',PARAM_INT);
				    $post->studyfield = required_param('studyfield',PARAM_INT);
				    $post->isced = required_param('isced',PARAM_INT);
				    $post->year = required_param('year',PARAM_INT);
				    $post->term = required_param('term',PARAM_INT);
			        if(!$post->term == PRAXE_TERM_SS && !$post->term == PRAXE_TERM_WS) {
					    print_error('notallowedaction', 'praxe');
				    }
				    $post->active = required_param('active',PARAM_INT);
				}

			    $post->subject = required_param('subject',PARAM_TEXT);

				if(!$bManage) {
				    if(is_null($edit)) {
						if(!praxe_has_capability('createownlocation')) {
						    print_error('notallowedaction', 'praxe');
						}
						$school = $DB->get_record('praxe_schools', array('headmaster' => $USER->id, 'id' => $post->school));
						$ext = $DB->get_record('praxe_school_teachers', array('id' => $post->teacher));
						if(!$school && !$ext) {
							print_error('notallowedaction', 'praxe');
						}
					}else if(!(praxe_has_capability('editanylocation') || (praxe_has_capability('editownlocation') && praxe_get_location($post->id,$USER->id)))) {
						print_error('notallowedaction', 'praxe');
					}
				}
				$redurl = optional_param('redurl',praxe_get_base_url(array('mode'=>'locations','schoolid'=>0)),PARAM_URL);
				/// insert record ///
				if(is_null($edit)) {
					$post->timecreated = time();
					if($DB->insert_record('praxe_locations', $post)){
						redirect($redurl, get_string('location_added','praxe'));
					}
				}else{
					$post->timemodified = time();
					if($DB->update_record('praxe_locations',$post)) {
						redirect($redurl, get_string('location_updated','praxe'));
					}
				}

				break;
			case ('assigntoinspection'):
				$post->schedule = required_param('scheduleid',PARAM_INT);
				$post->inspector = required_param('userid',PARAM_INT);
				require_capability('mod/praxe:assignselftoinspection',$context,$USER->id,false);
				require_capability('mod/praxe:assignselftoinspection',$context,$post->inspector,false);
				if(!$sch = praxe_get_schedule($post->schedule)) {
					print_error('notallowedaction', 'praxe');
				}

				$post->timecreated = time();
				if($DB->insert_record('praxe_schedule_inspections', $post)){
				    redirect(praxe_get_base_url(array("mode"=>$tab_modes['editteacher'][PRAXE_TAB_EDITTEACHER_HOME],"recordid"=>$sch->record)), get_string('assigned_to_inspection','praxe'));
				}

				break;
			case 'removefrominspection' :
			    $post->schedule = required_param('scheduleid',PARAM_INT);
				$post->inspector = required_param('inspid',PARAM_INT);
				require_capability('mod/praxe:assignselftoinspection',$context,$post->inspector,false);
				if(!$sch = praxe_get_schedule($post->schedule)) {
			    	print_error('notallowedaction', 'praxe');
				}
				if($DB->delete_records('praxe_schedule_inspections', (array)$post)) {
				    redirect(praxe_get_base_url(array("mode"=>$tab_modes['editteacher'][PRAXE_TAB_EDITTEACHER_HOME],"recordid"=>$sch->record)), get_string('removed_from_inspection','praxe'));
				}
			    break;
			case ('confirmlocation'): /// confirmation location by external teacher or teacher
			    $recid = optional_param('recordid',0,PARAM_INT);
				$record = praxe_get_record($recid);
				$refuse = optional_param('submitrefuse',null,PARAM_TEXT);
				$confirm = optional_param('submitconfirm',null,PARAM_TEXT);
				if(!$record || !($refuse || $confirm)) {
					print_error('notallowedaction', 'praxe');
				}
				if(!praxe_has_capability('confirmlocation') && !(praxe_has_capability('confirmownlocation') && $record->teacherid == $USER->id)) {
					print_error('notallowedaction', 'praxe');
				}
                $post->id = $record->id;
				$msg = '';
				if(!is_null($refuse)) {
					$post->status = PRAXE_STATUS_REFUSED;
					$msg = get_string('you_refused_location','praxe');
				}else if(!is_null($confirm)) {
					$post->status = PRAXE_STATUS_CONFIRMED;
					$msg = get_string('you_confirmed_location','praxe');
				}

				if($DB->update_record('praxe_records',$post) && false) {
					if($post->status = PRAXE_STATUS_CONFIRMED) {
						$emuser = get_complete_user_data('id',$record->student);
						$emfrom = get_complete_user_data('id',$record->teacherid);
						require_once($CFG->dirroot . '/mod/praxe/mailing.php');
						$mail = new praxe_mailing();
						$fak = new stdClass();
						$fak->name = fullname($emfrom);
						$fak->date = userdate(praxe_record::getData('datestart'), get_string('strftimedateshort'))." - ".userdate(praxe_record::getData('dateend'), get_string('strftimedateshort'));
						$fak->subject = s($record->subject);
						$fak->school = s($record->name);
						$fak->studyfield = s(praxe_record::getData('studyfield_name'));
						$fak->praxename = praxe_record::getData('name');
						$mail->setSubject(get_string('confirmedlocation','praxe'));
						$emtext = get_string('confirmlocation_mail','praxe',$fak);
						$mail->setPlain($emtext);
						$mail->setHtml($emtext);
						$mail->mailToUser($emuser, $emfrom);
					}
					redirect(praxe_get_base_url(), $msg);
				}

				break;
			case ('makeschedule'):
				require_capability('mod/praxe:addstudentschedule', $context);

				$post->yearclass = required_param('yearclass',PARAM_INT);
				$post->lessubject = required_param('lessubject',PARAM_TEXT);
				$post->lesnumber = optional_param('lesnumber',0,PARAM_INT);
				$post->schoolroom = optional_param('schoolroom','',PARAM_TEXT);
				$post->lestheme = optional_param_array('lestheme','',PARAM_RAW);
				$post->lestheme = $post->lestheme['text'];
				$timestart = required_param_array('timestart', PARAM_INT);
				$timeend = required_param_array('timeend', PARAM_INT);
				$post->timestart = mktime($timestart['hour'],$timestart['minute'], null, $timestart['month'],$timestart['day'],$timestart['year']);
				$post->timeend = mktime($timeend['hour'],$timeend['minute'], null, $timeend['month'],$timeend['day'],$timeend['year']);

				if($post->timestart > $post->timeend || $post->timestart < time()+60*60*24) {
					redirect(praxe_get_base_url(array('mode'=>$tab_modes['student'][PRAXE_TAB_STUDENT_ADDSCHEDULE])), get_string('error_timeschedule','praxe'));
				}
				$post->record = praxe_record::getData('rec_id');

				$redurl = praxe_get_base_url(array('mode'=>$tab_modes['student'][PRAXE_TAB_STUDENT_SCHEDULE]));
				/// insert record ///
				if(empty($edit)) {
					$post->timecreated = time();
					if($DB->insert_record('praxe_schedules', $post)){
						redirect($redurl, get_string('schedule_item_added','praxe'));
					}
				}else{
					$post->id = required_param('scheduleid',PARAM_INT);
					$post->timemodified = time();
					if($DB->update_record('praxe_schedules',$post)) {
						redirect($redurl, get_string('schedule_updated','praxe'));
					}
				}

				break;
			default:
				break;
	    endswitch;
    }

    if(strlen($todisplay)) {
    	echo "<div class=\"aligncenter\">$todisplay</div>";
    }

    if(!is_null($cancel)) {
        $params = array();
    	switch($post_form) {
    	    case 'addschool' :
    	        $params['mode'] = $tab_modes[strtolower($viewrole)][constant('PRAXE_TAB_'.$viewrole.'_SCHOOLS')];
    	        if(optional_param('detail',0,PARAM_INT) == 1 && ($schoolid = optional_param('schoolid',0,PARAM_INT)) > 0) {
    	            $params['schoolid'] = $schoolid;
    	        }
   	            break;
            case 'assignteachers' :
    	        $params['mode'] = $tab_modes[strtolower($viewrole)][constant('PRAXE_TAB_'.$viewrole.'_TEACHERS')];
    	        $params['schoolid'] = optional_param('teacher_school',0,PARAM_INT);
   	            break;
   	        case 'addlocation' :
    	        $params['mode'] = ($viewrole == 'EXTTEACHER') ? 'mylocations' : 'locations';
    	        $params['schoolid'] = optional_param('school',0,PARAM_INT);
    	        break;
   	        /// student role ///
    	    case 'makeschedule' :
   	            $params['mode'] = 'schedule';
   	            break;
					case 'assigntolocation' :
						$params['mode'] = 'locations';
						if(optional_param('detail',0,PARAM_INT) == 1 && ($schoolid = optional_param('schoolid',0,PARAM_INT)) > 0) {
							$params['schoolid'] = $schoolid;
						}else {
							$params['schoolid'] = 0;
						}
						$params['factualloc'] = 1;
						break;
    	    default :
    	        break;
    	}
    	foreach($_POST as $k=>$v) {
    		unset($_POST[$k]);
    	}

    	//echo praxe_get_base_url($params);
    	redirect(praxe_get_base_url($params),get_string('action_canceled','praxe'),1);
    }
?>
