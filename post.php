<?php 

//  Edit and save a new post data //

	require_login($course, true, $cm);    
    
    $praxe   = optional_param('praxe', praxe_record::getData('id'), PARAM_INT);
    $edit    = optional_param('edit', null);
    $cancel    = optional_param('cancel', null);
    $submit    = optional_param('submitbutton', null);    
    
    /// praxe instance doesn't exist ///
    if(empty($praxe)){
    	error(get_string('notallowedaction','praxe'));
    }
    /*
    //these page_params will be passed as hidden variables later in the form.
    $page_params = array('praxe'=>$praxe, 'edit'=>$edit);
    */
    
    $post = new object();
    //$post->praxe = $praxe;    
    $todisplay = '';
	if(!empty($submit)) {
	    require_sesskey();
		switch($post_form) :
			case ('assignteachers'): /// assigning external teacher to school				
				$post->teacher_school = required_param('teacher_school', PARAM_INT);
				$post->ext_teacher = required_param('ext_teacher', PARAM_INT);
				
				/// no access ///	
				if(!praxe_has_capability('assignteachertoanyschool')
					&& !(praxe_has_capability('assignteachertoownschool') 
							&& !get_record('praxe_schools','id',$post->teacher_school,'headmaster',$USER->id)) ) {					
								error(get_string('notallowedaction','praxe'));							
				}
				if(!has_capability('mod/praxe:beexternalteacher',$context,$post->ext_teacher)) {
					error(get_string('notallowedaction','praxe'));
				}
				
				$id = false;
				$id = insert_record('praxe_school_teachers',$post);				
				if(is_numeric($id)) {					
					redirect(praxe_get_base_url(), get_string('user_assigned_to_school','praxe'));
				}
				
				break;
	    	case ('assigntolocation'): /// assigning student to location				
				/// no access / no id of student to assign ///	
				if(has_capability('mod/praxe:editownrecord',$context)) {
					$student = $USER->id; 	
				}else if(has_capability('mod/praxe:editanyrecord',$context)) {
					$student = optional_param('student', null, PARAM_INT);
				}
				
				if(!isset($student) || is_null($student)) {
					error(get_string('notallowedaction','praxe'));
				}			
				$post->student = $student;
				$post->praxe = $praxe;
				/// check location data ///
				if(is_null($post->location = optional_param('location', null, PARAM_INT)) ) {
					error(get_string('notallowedaction','praxe'));
				}
				if(!$location = praxe_get_location($post->location)) {
					error(get_string('notallowedaction','praxe'));
				}
				
				/// this location is already used by other student ///				
				if($rec = get_record('praxe_records','location',$location->id, 'praxe', praxe_record::getData('id'))) {					
					if($rec->status != PRAXE_STATUS_REFUSED || $rec->student == $student) {						
						redirect(praxe_get_base_url(), get_string('location_no_available','praxe'));
					}
				}
				
				$post->timecreated = mktime();				 
				$post->timemodified = mktime();
				delete_records('praxe_records', 'praxe', $praxe, 'student', $student);
				
				$id = false;
				$id = insert_record('praxe_records',$post);				
				if(is_numeric($id)) {
					/// sending mail to external teacher ///
					if(!is_null($location->teacherid)) {
						$emuser = get_user_info_from_db('id',$location->teacherid);
						$emfrom = get_user_info_from_db('id',$post->student);
						require_once($CFG->dirroot . '/mod/praxe/mailing.php');
						$mail = new praxe_mailing();
						$stud = new stdClass();
						$stud->name = fullname($emuser);
						$stud->date = userdate(praxe_record::getData('datestart'), get_string('strftimedateshort'))." - ".userdate(praxe_record::getData('dateend'), get_string('strftimedateshort'));;
						$stud->subject = s($location->subject);
						$stud->school = s($location->name);
						$stud->studyfield = s(praxe_record::getData('studyfield_name'));
						$mail->setSubject(get_string('studenttopraxe','praxe'));
						$mail->addLinkToFoot(praxe_get_base_url(), get_string('confirmorrefusestudent','praxe'));
						$emtext = get_string('assigntolocation_mail','praxe',$stud);
						$mail->setPlain($emtext);
						$mail->setHtml($emtext);
						if($mail->mailToUser($emuser, $emfrom)) {						
							redirect(praxe_get_base_url(), get_string('assigned_to_location','praxe'));				
						}			
						
					}					
					$todisplay .= "<div>".get_string('mailnotsenttoexternalteacher','praxe')."</div>";
					$todisplay .= "<div>".get_string('contactselectedschool','praxe')."</div>"; 
				}				
				
				break;
			case ('addschool'): /// add new school form				
				/// no access ///
				$post->headmaster = optional_param('headmaster',null,PARAM_INT);				
				if(!praxe_has_capability('manageallincourse') && !praxe_has_capability('editanyschool')) {
					if(!praxe_has_capability('editownschool') && (empty($post->headmaster) || $post->headmaster != $USER->id)) {					
						error(get_string('notallowedactiona','praxe'));
					}
				}			
				
				if(empty($post->headmaster)) {
					$post->headmaster = null;
				}
				/*
				if($post->headmaster != $USER->id && !has_capability('mod/praxe:manageallincourse',$context)) {
					error("You don't have rights for this action!"); 	
				}				
				*/
				$post->name = optional_param('name', '');				
				$post->type = optional_param('type', PRAXE_SCHOOL_TYPE_5, PARAM_INT);				
				$post->street = optional_param('street', '');
				$post->city = optional_param('city', '');
				$post->zip = optional_param('zip', null, PARAM_NUMBER);
				if(empty($post->zip)) {
					$post->zip = null;
				}
				$post->email = optional_param('email', '');
				$post->phone = optional_param('phone', '');
				$post->website = optional_param('website','');
				$post->usermodified = $USER->id;
				//$post->timemodified = time();
				
				/// insert record ///				
				if(empty($edit)) {
					$post->timecreated = time();
					if(insert_record('praxe_schools',$post)){
						redirect(praxe_get_base_url(), get_string('school_added','praxe'));
					}				
				}else{
					$post->id = required_param('schoolid',PARAM_INT);				
					$post->timemodified = time();					
					if(update_record('praxe_schools',$post)) {
						redirect(praxe_get_base_url(), get_string('school_updated','praxe'));
					}
				}
												
				break;
			case ('addlocation'): /// add new location				
				/// no access ///
				//require_capability('mod/praxe:editschool', $context);
				$post->school = required_param('school',PARAM_INT);
				$post->teacher = required_param('teacher',PARAM_INT);
				$post->studyfield = required_param('studyfield',PARAM_INT);
				$post->isced = required_param('isced',PARAM_INT);
				$post->subject = required_param('subject',PARAM_TEXT);
				$post->year = required_param('year',PARAM_INT);
				$post->term = required_param('term',PARAM_INT);
				if(!$post->term == PRAXE_TERM_SS && !$post->term == PRAXE_TERM_WS) {
					error(get_string('notallowedaction','praxe'));
				}				
				$post->active = required_param('active',PARAM_INT);
								
				if(!praxe_has_capability('manageallincourse')) {					
					if(empty($edit)) {
						if(praxe_has_capability('createownlocation')) {
							$school = get_record('praxe_schools','headmaster',$USER->id,'id',$post->school);
							$ext = get_record('praxe_school_teachers','id',$post->teacher);
							if(!$school && !$ext) {
								error(get_string('notallowedaction','praxe'));
							}
						}else{
							error(get_string('notallowedaction','praxe'));
						}
					}else{
						$post->id = required_param('locationid',PARAM_INT);
						/// if this location is used, not allows to edit it ///
						$used = get_record('praxe_records','location',$post->id);						
						$true = (praxe_has_capability('editanylocation') || (praxe_has_capability('editownlocation') && praxe_get_location($post->id,$USER->id))
								&& !$used);						
						if(!$true) {
							error(get_string('notallowedaction','praxe'));
						}
					}
				}				

				$redurl = optional_param('redurl',praxe_get_base_url(),PARAM_URL);
				/// insert record ///				
				if(empty($edit)) {
					$post->timecreated = time();
					if(insert_record('praxe_locations',$post)){						
						redirect($redurl, get_string('location_added','praxe'));
					}				
				}else{
					$post->id = required_param('locationid',PARAM_INT);				
					$post->timemodified = time();					
					if(update_record('praxe_locations',$post)) {
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
					error(get_string('notallowedaction','praxe'));
				}
				
				$post->timecreated = time();
				if(insert_record('praxe_schedule_inspections',$post)){
					$redurl = praxe_get_base_url(array("mode=".$tab_modes['editteacher'][PRAXE_TAB_EDITTEACHER_HOME], "recordid=$sch->record")); 											
					redirect($redurl, get_string('assigned_to_inspection','praxe'));
				}
				
				break;
			case ('confirmlocation'): /// confirmation location by external teacher or teacher				
				$recid = optional_param('recordid',null,PARAM_INT);
				$record = praxe_get_record($recid);
				$refuse = optional_param('submitrefuse',null);
				$confirm = optional_param('submitconfirm',null);
				if(!$record || !($refuse || $confirm)) {
					error(get_string('notallowedaction','praxe'));
				}				
				if(!praxe_has_capability('confirmlocation') && !(praxe_has_capability('confirmownlocation') && $record->teacherid == $USER->id)) {				
					error(get_string('notallowedaction','praxe'));
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
				
				if(update_record('praxe_records',$post)) {
					// ??? TODO - send email to student about confirmation ???
					redirect(praxe_get_base_url(), $msg);
				}
				//print_object($post);
				
				break;
			case ('makeschedule'):				
				require_capability('mod/praxe:addstudentschedule', $context);
				
				$post->yearclass = required_param('yearclass',PARAM_INT);
				$post->lessubject = required_param('lessubject',PARAM_TEXT);
				$post->lesnumber = optional_param('lesnumber',0,PARAM_INT);
				$post->schoolroom = optional_param('schoolroom','',PARAM_TEXT);
				$post->lestheme = optional_param('lestheme','');
				
								
				$tstart = required_param('timestart');
				$tend = required_param('timeend');
				$post->timestart = mktime($tstart['hour'],$tstart['minute'], null, $tstart['month'],$tstart['day'],$tstart['year']);
				$post->timeend = mktime($tend['hour'],$tend['minute'], null, $tend['month'],$tend['day'],$tend['year']);
				if($post->timestart > $post->timeend || $post->timestart < mktime()+60*60*24) {
					$redurl = praxe_get_base_url("mode=$mode");
					redirect($redurl, get_string('error_timeschedule','praxe'));
				}				
				$post->record = praxe_record::getData('rec_id');
				
				$redurl = praxe_get_base_url().'&amp;mode='.$tab_modes['student'][PRAXE_TAB_STUDENT_SCHEDULE];
				/// insert record ///				
				if(empty($edit)) {
					$post->timecreated = time();
					if(insert_record('praxe_schedules',$post)){						
						redirect($redurl, get_string('schedule_item_added','praxe'));
					}				
				}else{
					$post->id = required_param('scheduleid',PARAM_INT);				
					$post->timemodified = time();					
					if(update_record('praxe_schedules',$post)) {
						redirect($redurl, get_string('schedule_updated','praxe'));
					}
				}				
				
				break;
			default:
				echo "POST data:";
    			print_object($_POST);
	    		echo "object post:";
    			print_object($post);			
				break;
	    endswitch;
    }
	
    if(strlen($todisplay)) {
    	echo "<div class=\"aligncenter\">$todisplay</div>";	
    }
    
    if(!is_null($cancel)) {
    	foreach($_POST as $k=>$v) {
    		unset($_POST[$k]);
    	}
    }
?>
