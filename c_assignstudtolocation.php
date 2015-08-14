<?php

/**
 *
 */

require_once ('c_actionform.php');

class praxe_assignstudtolocation extends praxe_actionform {
	private $locationid;
	function praxe_assignstudtolocation($locationid) {
		$this->locationid = $locationid;
		parent::praxe_actionform(get_class($this));
	}
    public function definition() {
        $err = false;
    	global $USER, $cm, $course, $DB, $CFG;
        $location = praxe_get_location($this->locationid);
        $users = praxe_get_student_participants($cm->id);

        /// link back to list of locations - we will use it later
        $back = html_writer::link(new moodle_url(praxe_get_base_url(array('mode'=>'locations','schoolid'=>0))),get_string('back'));

        //if(praxe_get_use_status_of_location($location->id))
        //var_dump(praxe_get_use_status_of_location($location->id));
        //var_dump($location);

 		if(!empty($location)) {
 			$this->content_before_form .= html_writer::tag('p',get_string('assignstudtolocation_text', 'praxe'));

 			$info = $location->name;
    		if(strlen(trim($location->city))) {
    			$info .= ", ".s($location->city);
    		}
    		if(strlen(trim($location->street))) {
    			$info .= ", ".s($location->street);
    		}
    		$info = html_writer::tag('strong',$info);
    		if(strlen(trim($location->subject))) {
    			$info .= '<br>'.html_writer::tag('strong',s($location->subject));
    		}
    		if(!is_null($location->teacherid)) {
    			$info .= '<br>'.s($location->teacher_name). ' ' .s($location->teacher_lastname);
    		}
    		$info .= '<br>'.praxe_get_term_text($location->term). ' ' .$location->year;

    		$this->content_before_form .= html_writer::tag('p',$info);

    		// location is occupied
    		$sql = "SELECT rec.* FROM {praxe_records} rec WHERE rec.location = ? AND rec.status != ?";
    		$params = array($this->locationid, PRAXE_STATUS_REFUSED);
    		if($ret = $DB->get_record_sql($sql, $params)) {
    			$this->content .= html_writer::tag('div', get_string('location_is_not_available', 'praxe'));
    			$this->content .= html_writer::tag('div', $back);
    		/// location and users are available
    		} else if(is_array($users) && count($users)) {
				$form = '<form class="mform" action="'.praxe_get_base_url(array('locationid'=>$this->locationid),'assigntolocation').'" method="post">';
				$form .= '<input type="hidden" name="post_form" value="assigntolocation" />';
				$form .= '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
				$form .= '<input type="hidden" name="location" value="'.$this->locationid.'" />';

				$select = '<select name="student">';
				$select .= '<option value="null">'.get_string('select_student','praxe').'</option>';
				foreach($users as $us) {
					$select .= '<option value="'.$us->id.'">'.s($us->firstname.' '.$us->lastname).'</option>';
	    		}
	    		$select .= '</select>';
				$form .= html_writer::tag('div', html_writer::tag('label', get_string('student')). html_writer::tag('div', $select), array('class' => 'frow'));

	    		/// option to send emails
	    		require_once($CFG->dirroot . "/lib/pear/HTML/QuickForm/checkbox.php");

	    		$checks = '';
	    		if(!is_null($location->teacherid)) {
		    		$check = new HTML_QuickForm_checkbox('sendemailtoextteacher','',get_string('sendinfotoextteacher','praxe'));
		    		$checks .= html_writer::tag('div', $check->toHtml());
	    		}
	    		$check = new HTML_QuickForm_checkbox('sendemailtostudent','',get_string('sendinfotostudent','praxe'),'checked');
	    		$checks .= $check->toHtml();
	    		$form .= html_writer::tag('div', html_writer::tag('label', get_string('informparticipants')).html_writer::tag('div', $checks), array('class' => 'frow'));

	    		/// action buttons
	    		//$sub = '<div class="fitem center" style="margin: 10px 0;">'
				$sub = 	'<input type="submit" id="id_submitbutton" value="Submit" name="submitbutton" /> '
						.'<input type="submit" id="id_cancel" onclick="skipClientValidation = true; return true;" value="Cancel" name="cancel" />';
	    		//felement fsubmit
				$form .= html_writer::tag('div', $sub, array('class' => 'frow submit'));
				$form .= '</form>';
				$this->content .= html_writer::tag('div', $form, array('class' => 'thin-form'));
			/// no students to be assigned to location
    		}else {
				$table->data[] = array(get_string('nostudentsavailable','praxe'));
				$table->data[] = array($back);
				$table->align = array('center');
				$this->content .= html_writer::table($table,true);
			}
    	}else{
    		$this->content_before_form .= html_writer::tag('strong',get_string('notallowedaction', 'praxe'));
    		$this->content .= $back;
    	}
    }
}

?>
